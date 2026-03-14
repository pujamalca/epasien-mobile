<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EPasien')</title>
    @vite(['resources/css/app.css'])
    <script>
        window.CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
</head>
<body>
<div id="offlineBanner">Tidak ada koneksi — menampilkan data terakhir</div>

@yield('content')

@if(!in_array(Request::route()?->getName(), ['login', 'splash', 'offline']))
<nav class="bottom-nav">
    <a href="/dashboard" class="bottom-nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
        <span class="nav-icon">&#127968;</span>Beranda
    </a>
    <a href="/pendaftaran" class="bottom-nav-item {{ Request::is('pendaftaran*') ? 'active' : '' }}">
        <span class="nav-icon">&#128203;</span>Daftar
    </a>
    <a href="/hasil" class="bottom-nav-item {{ Request::is('hasil*') ? 'active' : '' }}">
        <span class="nav-icon">&#129514;</span>Hasil
    </a>
    <a href="/dokumen" class="bottom-nav-item {{ Request::is('dokumen*') ? 'active' : '' }}">
        <span class="nav-icon">&#128196;</span>Dokumen
    </a>
    <a href="/notifikasi" class="bottom-nav-item {{ Request::is('notifikasi*') ? 'active' : '' }}">
        <span class="nav-icon">&#128276;</span>Notif
    </a>
    <a href="/akun" class="bottom-nav-item {{ Request::is('akun*') ? 'active' : '' }}">
        <span class="nav-icon">&#128100;</span>Akun
    </a>
</nav>
@endif

<div id="toastOverlay" class="toast-overlay"></div>
<div id="toast" class="toast" role="alert" aria-live="assertive">
    <div class="toast-icon" id="toastIcon"></div>
    <div class="toast-msg" id="toastMsg"></div>
</div>

@vite(['resources/js/api.js'])
@stack('scripts')

<script>
setInterval(function () {
    if (document.visibilityState === 'visible') {
        fetch('/api/ping', { headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN } }).catch(function () {});
    }
}, 600000);
</script>
</body>
</html>
