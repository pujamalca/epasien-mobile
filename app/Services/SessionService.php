<?php

namespace App\Services;

use Native\Mobile\Facades\SecureStorage;

class SessionService
{
    private const KEY_PASIEN = 'epasien_pasien';

    public function store(string $sessionId, array $pasien): void
    {
        session(['epasien_phpsessid' => $sessionId]);
        // pasien_data bukan credential, boleh di SecureStorage untuk offline display
        SecureStorage::set(self::KEY_PASIEN, json_encode($pasien));
    }

    public function getSessionId(): ?string
    {
        $val = session('epasien_phpsessid');
        return ($val !== null && $val !== '') ? $val : null;
    }

    public function getPasien(): ?array
    {
        $json = SecureStorage::get(self::KEY_PASIEN);
        if (!$json) return null;
        return json_decode($json, true) ?: null;
    }

    public function isLoggedIn(): bool
    {
        return $this->getSessionId() !== null;
    }

    public function clear(): void
    {
        session()->forget('epasien_phpsessid');
        session()->flush();
        SecureStorage::delete(self::KEY_PASIEN);
    }
}
