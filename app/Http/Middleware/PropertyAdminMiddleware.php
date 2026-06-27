<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PropertyAdminMiddleware
{
    /**
     * Membatasi menu operasional kos hanya untuk Admin Kos/Property Admin.
     * Super Admin fokus pada pengelolaan platform: data kos, admin kos,
     * pendaftaran pemilik kos, dan monitoring umum.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isPropertyAdmin()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Menu operasional kos hanya untuk Admin Kos. Super Admin mengelola Data Kos, Admin Kos, dan Pendaftaran.');
        }

        if ($user->status !== 'active') {
            auth()->logout();

            return redirect()
                ->route('admin.login')
                ->with('error', 'Akun admin kos Anda sedang nonaktif. Hubungi Super Admin.');
        }

        return $next($request);
    }
}
