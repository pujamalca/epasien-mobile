<?php

namespace App\Listeners;

use App\Services\FcmService;
use Native\Mobile\Events\PushNotification\TokenGenerated;

class FcmTokenListener
{
    public function __construct(private FcmService $fcm) {}

    /**
     * Dipanggil saat Android berhasil dapat FCM token dari Firebase.
     * Kirim token ke server SIMRS Khanza untuk disimpan di personal_pasien.fcm_token.
     */
    public function handle(TokenGenerated $event): void
    {
        $this->fcm->registerToServer($event->token);
    }
}
