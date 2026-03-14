<?php

namespace App\Http\Controllers;

use App\Services\EpasienApiService;
use App\Services\SessionService;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function __construct(
        private EpasienApiService $api,
        private SessionService $session,
    ) {}

    public function index()   { return view('pendaftaran.index'); }
    public function jadwal()  { return view('pendaftaran.jadwal-dokter'); }
    public function antrian() { return view('pendaftaran.antrian'); }
    public function poli()    { return view('pendaftaran.poliklinik'); }
    public function tagihan() { return view('pendaftaran.tagihan'); }
    public function booking() { return view('pendaftaran.booking'); }

    public function apiPoliList()
    {
        return response()->json(
            $this->api->get('v2/poli-list.php', $this->session->getSessionId())
        );
    }

    public function apiJadwal(Request $request)
    {
        $poli = preg_replace('/[^a-zA-Z0-9\-]/', '', $request->input('poli', ''));
        $qs   = $poli ? '?poli=' . urlencode($poli) : '';
        return response()->json(
            $this->api->get('v2/jadwal-dokter.php' . $qs, $this->session->getSessionId())
        );
    }

    public function apiAntrian()
    {
        return response()->json(
            $this->api->get('v2/antrian.php', $this->session->getSessionId())
        );
    }

    public function apiTagihan()
    {
        return response()->json(
            $this->api->get('v2/tagihan.php', $this->session->getSessionId())
        );
    }

    public function apiBooking(Request $request)
    {
        $request->validate([
            'kd_poli'    => ['required', 'string', 'max:20'],
            'kd_dokter'  => ['required', 'string', 'max:20'],
            'tgl_daftar' => ['required', 'date'],
        ]);
        return response()->json(
            $this->api->post('v2/booking.php', $request->only(['kd_poli', 'kd_dokter', 'tgl_daftar']), $this->session->getSessionId())
        );
    }
}
