<?php

namespace App\Http\Controllers;

use App\Services\EpasienApiService;
use App\Services\SessionService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class NotifikasiController extends Controller
{
    public function __construct(
        private EpasienApiService $api,
        private SessionService $session,
    ) {}

    public function index()
    {
        $sessionId = $this->session->getSessionId();
        $domain    = parse_url($this->api->getBaseUrl(), PHP_URL_HOST) ?? '';

        try {
            $response = Http::timeout(10)
                ->withCookies(['PHPSESSID' => $sessionId], $domain)
                ->get($this->api->getBaseUrl() . '/epasien/api/notifications.php');

            $notifs = $response->successful() ? ($response->json('data') ?? []) : [];
        } catch (ConnectionException) {
            $notifs = [];
        }

        return view('notifikasi', ['notifs' => $notifs]);
    }
}
