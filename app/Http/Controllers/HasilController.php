<?php

namespace App\Http\Controllers;

use App\Services\EpasienApiService;
use App\Services\SessionService;
use Illuminate\Http\Request;

class HasilController extends Controller
{
    public function __construct(
        private EpasienApiService $api,
        private SessionService $session,
    ) {}

    public function index()          { return view('hasil.index'); }
    public function riwayat()        { return view('hasil.riwayat'); }
    public function resep()          { return view('hasil.resep'); }
    public function rekamMedis()     { return view('hasil.rekam-medis'); }
    public function lab()            { return view('hasil.lab'); }
    public function labDetail()      { return view('hasil.lab-detail'); }
    public function radiologi()      { return view('hasil.radiologi'); }
    public function radiologiDetail(){ return view('hasil.radiologi-detail'); }

    public function apiRiwayat()     { return response()->json($this->api->get('v2/riwayat-periksa.php', $this->session->getSessionId())); }
    public function apiResep()       { return response()->json($this->api->get('v2/resep.php', $this->session->getSessionId())); }
    public function apiRekamMedis()  { return response()->json($this->api->get('v2/rekam-medis.php', $this->session->getSessionId())); }
    public function apiLabList()     { return response()->json($this->api->get('v2/lab-list.php', $this->session->getSessionId())); }
    public function apiRadiologiList(){ return response()->json($this->api->get('v2/radiologi-list.php', $this->session->getSessionId())); }

    public function apiLabDetail(Request $request)
    {
        $noRawat = preg_replace('/[^a-zA-Z0-9\/\-]/', '', $request->input('no_rawat', ''));
        return response()->json($this->api->get('v2/lab-detail.php?no_rawat=' . urlencode($noRawat), $this->session->getSessionId()));
    }

    public function apiRadiologiDetail(Request $request)
    {
        $noRawat = preg_replace('/[^a-zA-Z0-9\/\-]/', '', $request->input('no_rawat', ''));
        return response()->json($this->api->get('v2/radiologi-detail.php?no_rawat=' . urlencode($noRawat), $this->session->getSessionId()));
    }
}
