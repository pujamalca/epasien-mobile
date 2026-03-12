<?php

namespace App\Services;

use NativePHP\Mobile\Facades\SecureStorage;

class SessionService
{
    private const KEY_SESSION = 'epasien_phpsessid';
    private const KEY_PASIEN  = 'epasien_pasien';

    /**
     * Simpan session setelah login berhasil
     */
    public function store(string $sessionId, array $pasien): void
    {
        SecureStorage::set(self::KEY_SESSION, $sessionId);
        SecureStorage::set(self::KEY_PASIEN, json_encode($pasien));
    }

    /**
     * Ambil PHPSESSID yang tersimpan
     */
    public function getSessionId(): ?string
    {
        $val = SecureStorage::get(self::KEY_SESSION);
        return ($val !== null && $val !== '') ? $val : null;
    }

    /**
     * Ambil data pasien yang tersimpan
     */
    public function getPasien(): ?array
    {
        $json = SecureStorage::get(self::KEY_PASIEN);
        if (!$json) {
            return null;
        }
        return json_decode($json, true) ?: null;
    }

    /**
     * Cek apakah sudah login
     */
    public function isLoggedIn(): bool
    {
        return $this->getSessionId() !== null;
    }

    /**
     * Hapus semua data session (logout)
     */
    public function clear(): void
    {
        SecureStorage::forget(self::KEY_SESSION);
        SecureStorage::forget(self::KEY_PASIEN);
    }
}
