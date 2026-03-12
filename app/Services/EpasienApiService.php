<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class EpasienApiService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('epasien.base_url'), '/');
        $this->timeout = config('epasien.timeout', 10);
    }

    /**
     * Login pasien — panggil api/auth.php
     * Return: ['success' => bool, 'phpsessid' => string, 'pasien' => array, 'message' => string]
     */
    public function login(string $noRm, string $password): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/epasien/api/auth.php', [
                    'no_rm'    => $noRm,
                    'password' => $password,
                ]);

            $data = $response->json();

            if ($response->status() === 429) {
                return ['success' => false, 'message' => $data['message'] ?? 'Terlalu banyak percobaan.'];
            }

            if ($response->successful() && !empty($data['success'])) {
                return [
                    'success'   => true,
                    'phpsessid' => $data['phpsessid'] ?? '',
                    'pasien'    => $data['pasien'] ?? [],
                ];
            }

            return ['success' => false, 'message' => $data['message'] ?? 'Login gagal.'];

        } catch (ConnectionException) {
            return ['success' => false, 'message' => 'Tidak dapat terhubung ke server.'];
        }
    }

    /**
     * Ambil konfigurasi RS dari api/settings.php
     * Return: ['success' => bool, 'nama_rs' => string, 'logo_url' => string|null]
     */
    public function getSettings(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl . '/epasien/api/settings.php');

            if ($response->successful()) {
                return array_merge(['success' => true], $response->json());
            }

            return ['success' => false];

        } catch (ConnectionException) {
            return ['success' => false];
        }
    }

    /**
     * Cek koneksi ke server
     */
    public function isOnline(): bool
    {
        try {
            $response = Http::timeout(5)
                ->get($this->baseUrl . '/epasien/api/settings.php');
            return $response->successful();
        } catch (ConnectionException) {
            return false;
        }
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
