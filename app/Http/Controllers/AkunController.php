<?php

namespace App\Http\Controllers;

use App\Services\EpasienApiService;
use App\Services\SessionService;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    public function __construct(
        private EpasienApiService $api,
        private SessionService $session,
    ) {}

    public function index()        { return view('akun.index', ['pasien' => $this->session->getPasien() ?? []]); }
    public function profil()       { return view('akun.profil'); }
    public function pengumuman()   { return view('akun.pengumuman'); }
    public function jadwalKontrol(){ return view('akun.jadwal-kontrol'); }
    public function infoRs()       { return view('akun.info-rs'); }

    public function apiProfil()        { return response()->json($this->api->get('v2/profil-get.php', $this->session->getSessionId())); }
    public function apiPengumuman()    { return response()->json($this->api->get('v2/pengumuman.php', $this->session->getSessionId())); }
    public function apiJadwalKontrol() { return response()->json($this->api->get('v2/jadwal-kontrol.php', $this->session->getSessionId())); }
    public function apiInfoRs()        { return response()->json($this->api->get('v2/info-rs.php', $this->session->getSessionId())); }

    public function apiUpdateProfil(Request $request)
    {
        $request->validate([
            'no_hp'  => ['nullable', 'string', 'max:15'],
            'alamat' => ['nullable', 'string', 'max:200'],
        ]);
        return response()->json(
            $this->api->post('v2/profil-update.php', $request->only(['no_hp', 'alamat']), $this->session->getSessionId())
        );
    }

    public function apiGantiPassword(Request $request)
    {
        $request->validate([
            'password_lama' => ['required', 'string', 'min:1', 'max:50'],
            'password_baru' => ['required', 'string', 'min:6', 'max:50'],
        ]);
        return response()->json(
            $this->api->post('v2/ganti-password.php', $request->only(['password_lama', 'password_baru']), $this->session->getSessionId())
        );
    }
}
