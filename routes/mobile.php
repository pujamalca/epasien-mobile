<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\SessionExpiredController;
use App\Http\Controllers\WebViewController;
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

// WebView
Route::get('/webview', [WebViewController::class, 'open'])->name('webview');

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
