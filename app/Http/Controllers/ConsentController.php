<?php

namespace App\Http\Controllers;

use App\Services\EpasienApiService;
use App\Services\SessionService;
use Illuminate\Http\Request;

class ConsentController extends Controller
{
    public function __construct(
        private EpasienApiService $api,
        private SessionService $session,
    ) {}

    public function persetujuan()    { return view('consent.persetujuan'); }
    public function penolakan()      { return view('consent.penolakan'); }
    public function edukasi()        { return view('consent.edukasi'); }
    public function generalConsent() { return view('consent.general-consent'); }
    public function hakKewajiban()   { return view('consent.hak-kewajiban'); }

    public function apiList(string $type)
    {
        $map = ['consent' => 'v2/consent-list.php', 'penolakan' => 'v2/penolakan-list.php', 'general-consent' => 'v2/general-consent.php'];
        if (!isset($map[$type])) abort(422);
        return response()->json($this->api->get($map[$type], $this->session->getSessionId()));
    }

    public function apiEdukasi()      { return response()->json($this->api->get('v2/edukasi.php', $this->session->getSessionId())); }
    public function apiHakKewajiban() { return response()->json($this->api->get('v2/hak-kewajiban.php', $this->session->getSessionId())); }

    public function apiSign(Request $request, string $type)
    {
        $allowed = ['consent', 'penolakan', 'general-consent'];
        if (!in_array($type, $allowed)) abort(422);

        $request->validate([
            'no_reg'       => ['required', 'string', 'max:30'],
            'tanda_tangan' => ['required', 'string', 'starts_with:data:image/png;base64,'],
            'confirmed'    => ['required', 'accepted'],
        ]);

        $data = $this->api->post('v2/' . $type . '-sign.php', [
            'no_reg'       => $request->input('no_reg'),
            'tanda_tangan' => $request->input('tanda_tangan'),
            'confirmed'    => true,
            'timestamp'    => now()->toISOString(),
        ], $this->session->getSessionId());

        return response()->json($data);
    }
}
