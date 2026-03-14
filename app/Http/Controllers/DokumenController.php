<?php

namespace App\Http\Controllers;

use App\Services\EpasienApiService;
use App\Services\SessionService;

class DokumenController extends Controller
{
    public function __construct(
        private EpasienApiService $api,
        private SessionService $session,
    ) {}

    public function index()        { return view('dokumen.index'); }
    public function suratKontrol() { return view('dokumen.surat-kontrol'); }
    public function suratSakit()   { return view('dokumen.surat-sakit'); }
    public function suratRujukan() { return view('dokumen.surat-rujukan'); }
    public function resumeMedis()  { return view('dokumen.resume-medis'); }
    public function kartuBerobat() { return view('dokumen.kartu-berobat'); }

    public function apiSuratKontrol() { return response()->json($this->api->get('v2/surat-kontrol.php', $this->session->getSessionId())); }
    public function apiSuratSakit()   { return response()->json($this->api->get('v2/surat-sakit.php', $this->session->getSessionId())); }
    public function apiSuratRujukan() { return response()->json($this->api->get('v2/surat-rujukan.php', $this->session->getSessionId())); }
    public function apiResumeMedis()  { return response()->json($this->api->get('v2/resume-medis.php', $this->session->getSessionId())); }
    public function apiKartuBerobat() { return response()->json($this->api->get('v2/kartu-berobat.php', $this->session->getSessionId())); }
}
