<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebViewController;
use Illuminate\Support\Facades\Route;

// Splash
Route::get('/', fn () => view('splash'))->name('splash');

// Auth
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// WebView
Route::get('/webview',         [WebViewController::class, 'open'])->name('webview');
Route::get('/session-expired', [WebViewController::class, 'sessionExpired'])->name('session.expired');

// Offline
Route::get('/offline', fn () => view('offline'))->name('offline');
