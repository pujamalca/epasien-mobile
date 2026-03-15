@extends('layouts.app')
@section('title', 'Akun')
@section('active_tab', 'akun')
@section('content')
<div class="page">
    <div class="topbar">
        <div class="topbar-title">Akun</div>
    </div>
    <div class="page-content">
        {{-- Pasien info card --}}
        <div class="card" style="margin-bottom:16px;">
            <div style="font-weight:600;font-size:16px;">{{ $pasien['nama'] ?? '-' }}</div>
            <div style="color:var(--color-text-muted);font-size:13px;">No. RM: {{ $pasien['no_rm'] ?? '-' }}</div>
        </div>
        {{-- Menu list --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <a href="{{ route('akun.profil') }}" class="card-row">
                <span class="card-icon">👤</span>
                <span class="card-label">Profil Saya</span>
                <span class="card-chevron">&#8250;</span>
            </a>
            <a href="{{ route('akun.pengumuman') }}" class="card-row">
                <span class="card-icon">&#128226;</span>
                <span class="card-label">Pengumuman</span>
                <span class="card-chevron">&#8250;</span>
            </a>
            <a href="{{ route('akun.jadwal-kontrol') }}" class="card-row">
                <span class="card-icon">📅</span>
                <span class="card-label">Jadwal Kontrol</span>
                <span class="card-chevron">&#8250;</span>
            </a>
            <a href="{{ route('akun.info-rs') }}" class="card-row">
                <span class="card-icon">&#127973;</span>
                <span class="card-label">Informasi RS</span>
                <span class="card-chevron">&#8250;</span>
            </a>
        </div>
        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" style="margin-top:16px;">
            @csrf
            <button type="submit" class="btn btn-outline" style="color:var(--color-danger);border-color:var(--color-danger);">
                Keluar
            </button>
        </form>
    </div>
</div>
@endsection
