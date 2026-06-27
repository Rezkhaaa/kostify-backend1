<?php

namespace App\Services;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordResetCodeService
{
    /**
     * Membuat dan mengirim kode reset password ke email akun.
     * Admin/super admin tidak pernah melihat password lama maupun password baru pengguna.
     */
    public function sendCodeToUser(User $user, ?User $requestedBy = null, string $notes = 'Permintaan reset password.'): array
    {
        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $user->email],
            [
                'code' => Hash::make($code),
                'expires_at' => now()->addMinutes(15),
                'verified_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        PasswordResetRequest::updateOrCreate(
            ['email' => $user->email, 'status' => 'pending'],
            [
                'property_id' => $user->property_id,
                'phone' => $user->phone,
                'notes' => $notes,
                'admin_notes' => $requestedBy
                    ? 'Kode reset dikirim oleh '.$requestedBy->name.'. Pengguna tetap membuat password baru sendiri.'
                    : null,
                'handled_by' => $requestedBy?->id,
                'handled_at' => $requestedBy ? now() : null,
            ]
        );

        $emailSent = $this->sendEmail($user, $code);

        $response = [
            'sent' => $emailSent,
            'message' => $emailSent
                ? 'Kode reset password sudah dikirim ke email akun.'
                : 'Kode reset berhasil dibuat, tetapi email belum terkirim. Periksa pengaturan SMTP aplikasi.',
        ];

        if (app()->environment('local') || config('app.debug')) {
            $response['debug_code'] = $code;
        }

        return $response;
    }

    public function sendCodeToEmail(string $email, array $allowedRoles, ?User $requestedBy = null, string $notes = 'Permintaan reset password.'): array
    {
        $user = User::where('email', $email)->whereIn('role', $allowedRoles)->first();

        if (! $user) {
            // Pesan dibuat umum agar email akun tidak mudah ditebak orang lain.
            return [
                'sent' => false,
                'message' => 'Jika email terdaftar, kode reset password akan dikirim ke email tersebut.',
            ];
        }

        return $this->sendCodeToUser($user, $requestedBy, $notes);
    }

    public function verifyCode(string $email, string $code): bool
    {
        $row = DB::table('password_reset_codes')->where('email', $email)->first();

        if (! $row || now()->greaterThan($row->expires_at) || ! Hash::check($code, $row->code)) {
            return false;
        }

        DB::table('password_reset_codes')->where('email', $email)->update([
            'verified_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    public function resetPassword(string $email, string $code, string $newPassword, array $allowedRoles): bool
    {
        $row = DB::table('password_reset_codes')->where('email', $email)->first();

        if (! $row || ! $row->verified_at || now()->greaterThan($row->expires_at) || ! Hash::check($code, $row->code)) {
            return false;
        }

        $user = User::where('email', $email)->whereIn('role', $allowedRoles)->first();
        if (! $user) {
            return false;
        }

        $user->forceFill(['password' => Hash::make($newPassword)])->save();

        DB::table('password_reset_codes')->where('email', $email)->delete();

        PasswordResetRequest::where('email', $email)
            ->where('status', 'pending')
            ->update([
                'property_id' => $user->property_id,
                'status' => 'completed',
                'admin_notes' => 'Password baru dibuat sendiri oleh pemilik akun melalui kode email.',
                'handled_at' => now(),
                'updated_at' => now(),
            ]);

        return true;
    }

    private function sendEmail(User $user, string $code): bool
    {
        try {
            Mail::raw(
                "Halo {$user->name},\n\n".
                "Kode reset password Kostify Anda adalah: {$code}\n\n".
                "Kode ini berlaku selama 15 menit. Jangan berikan kode ini kepada siapa pun, termasuk admin.\n\n".
                "Jika Anda tidak meminta reset password, abaikan email ini.",
                function ($message) use ($user) {
                    $message->to($user->email)->subject('Kode Reset Password Kostify');
                }
            );

            return true;
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim email reset password Kostify', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
