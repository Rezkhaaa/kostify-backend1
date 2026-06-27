<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OneSignalService;
use App\Models\PushNotificationLog;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private OneSignalService $oneSignal) {}

    /**
     * Mobile memanggil endpoint ini untuk mengambil konfigurasi publik OneSignal.
     * REST API KEY tidak pernah dikirim ke mobile.
     */
    public function config(Request $request)
    {
        $user = $request->user();
        $externalId = $this->oneSignal->externalId($user);

        if ($user->onesignal_external_id !== $externalId) {
            $user->update(['onesignal_external_id' => $externalId]);
        }

        return response()->json([
            'enabled' => (bool) config('services.onesignal.enabled') && filled(config('services.onesignal.app_id')),
            'app_id' => config('services.onesignal.app_id'),
            'external_id' => $externalId,
            'user_enabled' => (bool) $user->onesignal_enabled,
        ]);
    }

    /**
     * Mobile menyimpan subscription/player ID agar admin bisa audit apakah device sudah terhubung.
     */
    public function storeDevice(Request $request)
    {
        $data = $request->validate([
            'subscription_id' => 'nullable|string|max:255',
            'external_id' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $user->update([
            'onesignal_external_id' => $data['external_id'] ?? $this->oneSignal->externalId($user),
            'onesignal_subscription_id' => $data['subscription_id'] ?? $user->onesignal_subscription_id,
            'onesignal_enabled' => true,
            'onesignal_last_synced_at' => now(),
        ]);

        return response()->json([
            'message' => 'Device notifikasi berhasil disimpan.',
            'external_id' => $user->onesignal_external_id,
            'subscription_id' => $user->onesignal_subscription_id,
        ]);
    }

    public function toggle(Request $request)
    {
        $data = $request->validate([
            'enabled' => 'required|boolean',
        ]);

        $request->user()->update([
            'onesignal_enabled' => (bool) $data['enabled'],
        ]);

        return response()->json([
            'message' => $data['enabled'] ? 'Notifikasi diaktifkan.' : 'Notifikasi dinonaktifkan.',
            'enabled' => (bool) $data['enabled'],
        ]);
    }

    public function count(Request $request)
    {
        PushNotificationLog::cleanupExpired();

        $count = PushNotificationLog::unreadForTenant($request->user())->count();

        return response()->json([
            'count' => min($count, 99),
            'expires_after_hours' => 24,
        ]);
    }

    public function markRead(Request $request)
    {
        PushNotificationLog::cleanupExpired();

        $updated = PushNotificationLog::markTenantRead($request->user());

        return response()->json([
            'message' => 'Notifikasi sudah dibaca.',
            'updated' => $updated,
            'count' => 0,
        ]);
    }

}
