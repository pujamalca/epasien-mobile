<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\HasilController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\RadiologyProxyController;
use App\Http\Controllers\SessionExpiredController;
use Illuminate\Support\Facades\Route;

// Splash
Route::get('/', fn () => view('splash'))->name('splash');

// Auth
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth.session');

// Generik menu dispatcher
Route::get('/menu/{act}', [MenuController::class, 'dispatch'])
    ->name('menu.dispatch')
    ->middleware('auth.session');

// Session expired
Route::get('/session-expired', [SessionExpiredController::class, 'index'])
    ->name('session.expired');

// Ping keepalive
Route::get('/api/ping', fn() => response()->json(['ok' => true]))
    ->middleware('auth.session');

// Offline
Route::get('/offline', fn () => view('offline'))->name('offline');

// Pendaftaran — views
Route::prefix('pendaftaran')->middleware('auth.session')->group(function () {
    Route::get('/',        [PendaftaranController::class, 'index'])->name('pendaftaran');
    Route::get('/jadwal',  [PendaftaranController::class, 'jadwal'])->name('pendaftaran.jadwal');
    Route::get('/antrian', [PendaftaranController::class, 'antrian'])->name('pendaftaran.antrian');
    Route::get('/poli',    [PendaftaranController::class, 'poli'])->name('pendaftaran.poli');
    Route::get('/tagihan', [PendaftaranController::class, 'tagihan'])->name('pendaftaran.tagihan');
    Route::get('/booking', [PendaftaranController::class, 'booking'])->name('pendaftaran.booking');
});

// Pendaftaran — API proxy
Route::prefix('api/pendaftaran')->middleware('auth.session')->group(function () {
    Route::get('/poli-list', [PendaftaranController::class, 'apiPoliList']);
    Route::get('/jadwal',    [PendaftaranController::class, 'apiJadwal']);
    Route::get('/antrian',   [PendaftaranController::class, 'apiAntrian']);
    Route::get('/tagihan',   [PendaftaranController::class, 'apiTagihan']);
    Route::post('/booking',  [PendaftaranController::class, 'apiBooking']);
});

// Hasil — views
Route::prefix('hasil')->middleware('auth.session')->group(function () {
    Route::get('/',                 [HasilController::class, 'index'])->name('hasil');
    Route::get('/riwayat',          [HasilController::class, 'riwayat'])->name('hasil.riwayat');
    Route::get('/resep',            [HasilController::class, 'resep'])->name('hasil.resep');
    Route::get('/rekam-medis',      [HasilController::class, 'rekamMedis'])->name('hasil.rekam-medis');
    Route::get('/lab',              [HasilController::class, 'lab'])->name('hasil.lab');
    Route::get('/lab/detail',       [HasilController::class, 'labDetail'])->name('hasil.lab-detail');
    Route::get('/radiologi',        [HasilController::class, 'radiologi'])->name('hasil.radiologi');
    Route::get('/radiologi/detail', [HasilController::class, 'radiologiDetail'])->name('hasil.radiologi-detail');
});

// Hasil — API proxy
Route::prefix('api/hasil')->middleware('auth.session')->group(function () {
    Route::get('/riwayat',               [HasilController::class, 'apiRiwayat']);
    Route::get('/resep',                 [HasilController::class, 'apiResep']);
    Route::get('/rekam-medis',           [HasilController::class, 'apiRekamMedis']);
    Route::get('/lab/list',              [HasilController::class, 'apiLabList']);
    Route::get('/lab/detail',            [HasilController::class, 'apiLabDetail']);
    Route::get('/radiologi/list',        [HasilController::class, 'apiRadiologiList']);
    Route::get('/radiologi/detail',      [HasilController::class, 'apiRadiologiDetail']);
    Route::get('/radiologi/foto/{filename}', [RadiologyProxyController::class, 'image']);
});

// Dokumen — views
Route::prefix('dokumen')->middleware('auth.session')->group(function () {
    Route::get('/',              [DokumenController::class, 'index'])->name('dokumen');
    Route::get('/surat-kontrol', [DokumenController::class, 'suratKontrol'])->name('dokumen.surat-kontrol');
    Route::get('/surat-sakit',   [DokumenController::class, 'suratSakit'])->name('dokumen.surat-sakit');
    Route::get('/surat-rujukan', [DokumenController::class, 'suratRujukan'])->name('dokumen.surat-rujukan');
    Route::get('/resume-medis',  [DokumenController::class, 'resumeMedis'])->name('dokumen.resume-medis');
    Route::get('/kartu-berobat', [DokumenController::class, 'kartuBerobat'])->name('dokumen.kartu-berobat');
});

// Dokumen — API proxy
Route::prefix('api/dokumen')->middleware('auth.session')->group(function () {
    Route::get('/surat-kontrol', [DokumenController::class, 'apiSuratKontrol']);
    Route::get('/surat-sakit',   [DokumenController::class, 'apiSuratSakit']);
    Route::get('/surat-rujukan', [DokumenController::class, 'apiSuratRujukan']);
    Route::get('/resume-medis',  [DokumenController::class, 'apiResumeMedis']);
    Route::get('/kartu-berobat', [DokumenController::class, 'apiKartuBerobat']);
});

// Consent — views
Route::prefix('consent')->middleware('auth.session')->group(function () {
    Route::get('/persetujuan',   [ConsentController::class, 'persetujuan'])->name('consent.persetujuan');
    Route::get('/penolakan',     [ConsentController::class, 'penolakan'])->name('consent.penolakan');
    Route::get('/edukasi',       [ConsentController::class, 'edukasi'])->name('consent.edukasi');
    Route::get('/general',       [ConsentController::class, 'generalConsent'])->name('consent.general-consent');
    Route::get('/hak-kewajiban', [ConsentController::class, 'hakKewajiban'])->name('consent.hak-kewajiban');
});

// Consent — API proxy
Route::prefix('api/consent')->middleware('auth.session')->group(function () {
    Route::get('/list/{type}',   [ConsentController::class, 'apiList']);
    Route::get('/edukasi',       [ConsentController::class, 'apiEdukasi']);
    Route::get('/hak-kewajiban', [ConsentController::class, 'apiHakKewajiban']);
    Route::post('/sign/{type}',  [ConsentController::class, 'apiSign']);
});

// Akun — views
Route::prefix('akun')->middleware('auth.session')->group(function () {
    Route::get('/',               [AkunController::class, 'index'])->name('akun');
    Route::get('/profil',         [AkunController::class, 'profil'])->name('akun.profil');
    Route::get('/pengumuman',     [AkunController::class, 'pengumuman'])->name('akun.pengumuman');
    Route::get('/jadwal-kontrol', [AkunController::class, 'jadwalKontrol'])->name('akun.jadwal-kontrol');
    Route::get('/info-rs',        [AkunController::class, 'infoRs'])->name('akun.info-rs');
});

// Akun — API proxy
Route::prefix('api/akun')->middleware('auth.session')->group(function () {
    Route::get('/profil',          [AkunController::class, 'apiProfil']);
    Route::post('/profil',         [AkunController::class, 'apiUpdateProfil']);
    Route::get('/pengumuman',      [AkunController::class, 'apiPengumuman']);
    Route::get('/jadwal-kontrol',  [AkunController::class, 'apiJadwalKontrol']);
    Route::get('/info-rs',         [AkunController::class, 'apiInfoRs']);
});
