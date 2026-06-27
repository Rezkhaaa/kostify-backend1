<?php

namespace App\Services;

use App\Models\PushNotificationLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    /**
     * External ID dipakai agar backend bisa mengirim notifikasi ke user tertentu,
     * tanpa bergantung pada satu device saja. Satu user bisa punya beberapa device.
     */
    public function externalId(User $user): string
    {
        return 'kostify-user-' . $user->id;
    }

    public function enabled(): bool
    {
        return (bool) config('services.onesignal.enabled')
            && filled(config('services.onesignal.app_id'))
            && filled(config('services.onesignal.rest_api_key'));
    }

    /**
     * Mengirim push notification ke satu tenant.
     * Jika OneSignal belum dikonfigurasi, proses tidak error agar demo fitur lain tetap aman.
     */
    public function sendToUser(User $user, string $title, string $message, array $data = []): ?array
    {
        if (! $user->isTenant() || ! $user->onesignal_enabled) {
            return null;
        }

        $externalId = $user->onesignal_external_id ?: $this->externalId($user);

        PushNotificationLog::cleanupExpired();

        $log = PushNotificationLog::create([
            'user_id' => $user->id,
            'property_id' => $data['property_id'] ?? $user->property_id,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'status' => $this->enabled() ? 'pending' : 'skipped',
        ]);

        if (! $this->enabled()) {
            $log->update([
                'error_message' => 'OneSignal belum dikonfigurasi. Isi ONESIGNAL_APP_ID dan ONESIGNAL_REST_API_KEY di .env.',
            ]);
            return null;
        }

        try {
            $response = Http::withToken(config('services.onesignal.rest_api_key'))
                ->acceptJson()
                ->post(config('services.onesignal.api_url', 'https://api.onesignal.com/notifications'), [
                    'app_id' => config('services.onesignal.app_id'),
                    'target_channel' => 'push',
                    'include_aliases' => [
                        'external_id' => [$externalId],
                    ],
                    'headings' => ['en' => $title, 'id' => $title],
                    'contents' => ['en' => $message, 'id' => $message],
                    'data' => $data + [
                        'source' => 'kostify',
                    ],
                ]);

            $payload = $response->json() ?? ['body' => $response->body()];

            $log->update([
                'onesignal_notification_id' => $payload['id'] ?? null,
                'status' => $response->successful() ? 'sent' : 'failed',
                'response' => $payload,
                'sent_at' => $response->successful() ? now() : null,
                'error_message' => $response->successful() ? null : ($payload['errors'][0] ?? $response->body()),
            ]);

            return $payload;
        } catch (\Throwable $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::warning('Gagal mengirim OneSignal notification', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
