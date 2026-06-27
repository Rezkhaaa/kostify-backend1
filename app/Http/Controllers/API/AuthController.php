<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{PasswordResetRequest, TenantRegistrationRequest, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Services\AdminTenantRegistrationNotificationService;
use App\Services\PasswordResetCodeService;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::with('property')->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['Email atau password salah.']]);
        }

        return $this->issueMobileToken($user);
    }

    /**
     * Login/daftar tenant melalui Google untuk aplikasi mobile.
     * Jika akun tenant belum ada, sistem membuat pendaftaran baru
     * dan menunggu persetujuan Super Admin.
     */
    public function googleLogin(Request $request)
    {
        $request->validate([
            'access_token' => 'nullable|string',
            'id_token' => 'nullable|string',
            'gender' => 'nullable|in:putra,putri',
        ]);

        if (! $request->access_token && ! $request->id_token) {
            return response()->json(['message' => 'Token Google wajib dikirim.'], 422);
        }

        $response = $request->access_token
            ? Http::withToken($request->access_token)->get('https://www.googleapis.com/oauth2/v3/userinfo')
            : Http::asForm()->get('https://oauth2.googleapis.com/tokeninfo', ['id_token' => $request->id_token]);

        if (! $response->ok()) {
            return response()->json(['message' => 'Token Google tidak valid.'], 422);
        }

        $payload = $response->json();
        $googleClientId = config('services.google.client_id');

        if ($request->id_token && $googleClientId && ($payload['aud'] ?? null) !== $googleClientId) {
            return response()->json(['message' => 'Google Client ID tidak sesuai.'], 422);
        }

        $emailVerified = $payload['email_verified'] ?? false;
        if ($emailVerified !== true && $emailVerified !== 'true' && $emailVerified !== 1 && $emailVerified !== '1') {
            return response()->json(['message' => 'Email Google belum terverifikasi.'], 422);
        }

        $email = $payload['email'] ?? null;
        if (! $email) {
            return response()->json(['message' => 'Email Google tidak ditemukan.'], 422);
        }

        $name = $payload['name'] ?? strstr($email, '@', true) ?: 'Penghuni Kostify';
        $googleId = $payload['sub'] ?? null;
        $avatar = $payload['picture'] ?? null;

        $user = User::where('email', $email)->first();

        if ($user && $user->role !== 'tenant') {
            return response()->json(['message' => 'Akun admin tidak bisa login melalui aplikasi mobile.'], 403);
        }

        if ($user) {
            $user->update([
                'google_id' => $googleId ?: $user->google_id,
                'avatar' => $avatar ?: $user->avatar,
                'name' => $user->name ?: $name,
                'gender' => $request->gender ?: $user->gender,
            ]);

            return $this->issueMobileToken($user->load('property'));
        }

        $existingRequest = TenantRegistrationRequest::where('email', $email)->where('status', 'pending')->first();
        if ($existingRequest) {
            return response()->json([
                'message' => 'Pendaftaran kamu sudah tercatat dan sedang menunggu persetujuan Super Admin.'
            ], 202);
        }

        $registration = TenantRegistrationRequest::create([
            'name' => $name,
            'email' => $email,
            'phone' => null,
            'address' => null,
            'password' => Hash::make(Str::random(40)),
            'status' => 'pending',
            'google_id' => $googleId,
            'avatar' => $avatar,
            'gender' => $request->gender,
            'requested_via' => 'google',
        ]);

        app(AdminTenantRegistrationNotificationService::class)->notifySubmitted($registration);

        return response()->json([
            'message' => 'Pendaftaran Google berhasil dikirim. Silakan tunggu persetujuan Super Admin.',
        ], 201);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email|unique:tenant_registration_requests,email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
            'gender' => 'required|in:putra,putri',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.unique' => 'Email sudah pernah didaftarkan. Silakan login atau tunggu persetujuan admin.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $registration = TenantRegistrationRequest::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'gender' => $data['gender'],
            'password' => Hash::make($data['password']),
            'status' => 'pending',
            'requested_via' => 'manual',
        ]);

        app(AdminTenantRegistrationNotificationService::class)->notifySubmitted($registration);

        return response()->json([
            'message' => 'Pendaftaran berhasil dikirim. Akun akan aktif setelah disetujui Super Admin.',
        ], 201);
    }

    private function issueMobileToken(User $user)
    {
        // Status nonaktif tenant sekarang bersifat per kos.
        // Karena itu login mobile tetap diizinkan, lalu akses ke tiap kos
        // dibatasi di endpoint booking/tagihan/komplain/perbaikan.
        if ($user->role !== 'tenant') {
            return response()->json(['message' => 'Akun ini tidak dapat digunakan di aplikasi mobile.'], 403);
        }

        if (($user->status ?: 'active') !== 'active') {
            return response()->json(['message' => 'Akun pengguna sedang nonaktif. Hubungi admin.'], 403);
        }

        $user->load('property');
        $token = $user->createToken('kostify-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'property_id' => $user->property_id,
                'property' => $user->property,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'gender' => $user->gender,
                'gender_label' => $user->genderLabel(),
                'phone' => $user->phone,
                'address' => $user->address,
                'photo' => $user->photo,
                'avatar' => $user->avatar,
            ],
        ]);
    }

    public function sendResetCode(Request $request, PasswordResetCodeService $resetService)
    {
        $data = $request->validate(['email' => 'required|email|max:255']);

        $result = $resetService->sendCodeToEmail(
            $data['email'],
            ['tenant'],
            null,
            'Permintaan reset password dari aplikasi mobile.'
        );

        return response()->json([
            'message' => $result['message'],
        ] + (isset($result['debug_code']) ? ['debug_code' => $result['debug_code']] : []));
    }

    public function verifyResetCode(Request $request, PasswordResetCodeService $resetService)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'code' => 'required|string|max:6',
        ]);

        if (! $resetService->verifyCode($data['email'], $data['code'])) {
            return response()->json(['message' => 'Kode verifikasi tidak valid atau sudah kedaluwarsa.'], 422);
        }

        return response()->json(['message' => 'Kode berhasil diverifikasi.']);
    }

    public function resetPasswordWithCode(Request $request, PasswordResetCodeService $resetService)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'code' => 'required|string|max:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $ok = $resetService->resetPassword(
            $data['email'],
            $data['code'],
            $data['password'],
            ['tenant']
        );

        if (! $ok) {
            return response()->json(['message' => 'Verifikasi belum valid atau email tidak ditemukan.'], 422);
        }

        return response()->json(['message' => 'Password berhasil diganti. Silakan login.']);
    }

    public function forgotPassword(Request $request, PasswordResetCodeService $resetService) { return $this->sendResetCode($request, $resetService); }
    public function logout(Request $request) { $request->user()->currentAccessToken()?->delete(); return response()->json(['message' => 'Logout berhasil']); }
    public function profile(Request $request)
    {
        $user = $request->user()->load('property');
        $payload = $user->toArray();
        $payload['gender_label'] = $user->genderLabel();

        return response()->json(['user' => $payload]);
    }

    public function changePassword(Request $request)
    {
        $request->validate(['current_password' => 'required|string', 'password' => 'required|string|min:6|confirmed']);
        if (! Hash::check($request->current_password, $request->user()->password)) return response()->json(['message' => 'Password lama tidak sesuai.'], 422);
        $request->user()->update(['password' => Hash::make($request->password)]);
        return response()->json(['message' => 'Password berhasil diganti.']);
    }



    public function requestAccountDeletion(Request $request)
    {
        $user = $request->user();

        DB::table('account_deletion_requests')->updateOrInsert(
            ['email' => $user->email, 'status' => 'pending'],
            [
                'user_id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'notes' => 'Permintaan penghapusan akun dari aplikasi mobile.',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Permintaan penghapusan akun berhasil dikirim. Admin akan memproses permintaan ini.',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:1000',
            'gender' => 'sometimes|nullable|in:putra,putri',
        ]);

        // # Tenant boleh memperbarui kategori penghuni dari menu Profil.
        // # Backend akan memakai data ini untuk validasi Kos Putra/Putri/Campuran saat booking.
        $request->user()->update($request->only(['name', 'phone', 'address', 'gender']));

        $user = $request->user()->fresh()->load('property');
        $payload = $user->toArray();
        $payload['gender_label'] = $user->genderLabel();

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user' => $payload,
        ]);
    }
}
