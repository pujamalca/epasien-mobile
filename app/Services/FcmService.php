<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Native\Mobile\Facades\PushNotifications;

class FcmService
{
    public function __construct(
        private EpasienApiService $api,
        private SessionService $session,
    ) {}

    /**
     * Minta permission push notification dari Android dan mulai enrollment FCM.
     * Token akan diterima via event TokenGenerated → FcmTokenListener.
     * Dipanggil setelah login berhasil.
     */
    public function enroll(): void
    {
        // Jika token sudah ada (re-login), langsung register ke server
        $existingToken = PushNotifications::getToken();
        if ($existingToken) {
            $this->registerToServer($existingToken);
            return;
        }

        // Minta permission Android + request FCM token
        // Token akan diterima lewat event TokenGenerated
        PushNotifications::enroll();
    }

    /**
     * Kirim FCM token ke server (push-register.php).
     * Dipanggil dari FcmTokenListener atau langsung jika token sudah tersedia.
     */
    public function registerToServer(string $token): void
    {
        $sessionId = $this->session->getSessionId();
        if (! $sessionId) {
            return;
        }

        $domain = parse_url($this->api->getBaseUrl(), PHP_URL_HOST) ?? '';

        try {
            Http::timeout(10)
                ->withCookies(['PHPSESSID' => $sessionId], $domain)
                ->post($this->api->getBaseUrl() . '/epasien/api/push-register.php', [
                    'fcm_token' => $token,
                ]);
        } catch (ConnectionException $e) {
            Log::warning('FCM register gagal: ' . $e->getMessage());
        }
    }
}
