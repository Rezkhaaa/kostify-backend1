<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Silakan login sebagai admin terlebih dahulu.');
        }

        // Jika yang tersimpan di session adalah akun tenant/non-admin, logout dahulu.
        // Tanpa ini, browser bisa berputar antara /admin/login dan /admin/dashboard.
        if (! Auth::user()->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->with('error', 'Akses web hanya untuk Super Admin atau Admin Kos.');
        }

        if (Auth::user()->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->with('error', 'Akun admin tidak aktif. Hubungi Super Admin.');
        }

        return $next($request);
    }
}
