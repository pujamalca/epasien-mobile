<?php

namespace App\Http\Controllers;

use App\Services\EpasienApiService;
use App\Services\SessionService;
use Illuminate\Http\Request;

class WebViewController extends Controller
{
    public function __construct(
        private SessionService $session,
        private EpasienApiService $api,
    ) {}

    public function open(Request $request)
    {
        if (!$this->session->isLoggedIn()) {
            return redirect()->route('login');
        }

        // Whitelist parameter act: hanya huruf, angka, underscore, strip
        $act = $request->input('act', 'HomeUser');
        if (!preg_match('/^[a-zA-Z0-9_\-]{1,50}$/', $act)) {
            $act = 'HomeUser';
        }

        $baseUrl   = $this->api->getBaseUrl();
        $url       = $baseUrl . '/epasien/indexuser.php?act=' . $act;
        $sessionId = $this->session->getSessionId();
        $domain    = parse_url($baseUrl, PHP_URL_HOST);

        return view('webview', compact('url', 'sessionId', 'domain'));
    }

    public function sessionExpired()
    {
        $this->session->clear();
        return redirect()->route('login')
            ->with('info', 'Sesi Anda telah berakhir. Silakan login kembali.');
    }
}
