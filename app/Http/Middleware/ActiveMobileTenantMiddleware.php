<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveMobileTenantMiddleware
{
    /**
     * Middleware API Mobile Tenant.
     * Catatan penting:
     * - Aplikasi mobile hanya boleh dipakai role tenant.
     * - Status aktif/nonaktif untuk tenant sekarang bersifat per kos.
     *   Contoh: Vita bisa dinonaktifkan oleh Admin Kos Melati, tetapi tetap bisa akses Kos Mawar.
     * - Pengecekan per kos dilakukan di controller/service berdasarkan property_id data terkait.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'code' => 'UNAUTHENTICATED',
                'message' => 'Sesi login tidak valid. Silakan login ulang.',
            ], 401);
        }

        if (! $user->isTenant()) {
            $user->currentAccessToken()?->delete();

            return response()->json([
                'code' => 'MOBILE_TENANT_ONLY',
                'message' => 'Aplikasi mobile hanya untuk penghuni. Admin masuk lewat Web Dashboard.',
            ], 403);
        }

        if (($user->status ?: 'active') !== 'active') {
            return response()->json([
                'code' => 'TENANT_INACTIVE',
                'message' => 'Akun pengguna sedang nonaktif. Hubungi admin.',
            ], 403);
        }

        return $next($request);
    }
}
