<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check() && Auth::user()->isAdmin() && Auth::user()->status === 'active') {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (! $user->isAdmin()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akses web hanya untuk Super Admin atau Admin Kos.']);
            }

            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun admin sedang nonaktif. Hubungi Super Admin.']);
            }

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau kata sandi salah.'])->withInput();
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetCode(Request $request, PasswordResetCodeService $resetService)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $result = $resetService->sendCodeToEmail(
            $data['email'],
            ['super_admin', 'property_admin', 'admin'],
            null,
            'Permintaan reset password dari halaman login admin.'
        );

        return redirect()
            ->route('admin.password.reset.form', ['email' => $data['email']])
            ->with('success', $result['message']);
    }

    public function showResetPassword(Request $request)
    {
        return view('auth.reset-password', [
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request, PasswordResetCodeService $resetService)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'code' => 'required|string|max:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (! $resetService->verifyCode($data['email'], $data['code'])) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid atau sudah kedaluwarsa.'])->withInput();
        }

        $ok = $resetService->resetPassword(
            $data['email'],
            $data['code'],
            $data['password'],
            ['super_admin', 'property_admin', 'admin']
        );

        if (! $ok) {
            return back()->withErrors(['email' => 'Reset password gagal. Pastikan email dan kode benar.'])->withInput();
        }

        return redirect()->route('admin.login')->with('success', 'Kata sandi berhasil diganti. Silakan masuk dengan kata sandi baru.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
