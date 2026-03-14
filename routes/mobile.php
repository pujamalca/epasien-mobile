<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
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
