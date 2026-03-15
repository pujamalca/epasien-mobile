@extends('layouts.app')
@section('title', 'Pelayanan')
@section('content')
<div class="page">
    <div class="topbar">
        <div class="topbar-title">Pelayanan</div>
    </div>
    <div class="page-content">
        <div class="menu-grid">
            <a href="{{ route('pendaftaran.jadwal') }}" class="menu-item">
                <div class="menu-icon">📅</div>
                <div class="menu-label">Jadwal Dokter</div>
            </a>
            <a href="{{ route('pendaftaran.booking') }}" class="menu-item">
                <div class="menu-icon">📋</div>
                <div class="menu-label">Booking</div>
            </a>
            <a href="{{ route('pendaftaran.antrian') }}" class="menu-item">
                <div class="menu-icon">&#127967;</div>
                <div class="menu-label">Antrian</div>
            </a>
            <a href="{{ route('pendaftaran.poli') }}" class="menu-item">
                <div class="menu-icon">&#127973;</div>
                <div class="menu-label">Poliklinik</div>
            </a>
            <a href="{{ route('pendaftaran.tagihan') }}" class="menu-item">
                <div class="menu-icon">&#128179;</div>
                <div class="menu-label">Tagihan</div>
            </a>
        </div>
    </div>
</div>
@endsection
