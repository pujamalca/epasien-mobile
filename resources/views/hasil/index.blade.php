@extends('layouts.app')
@section('title', 'Hasil Pemeriksaan')
@section('content')
<div class="page">
    <div class="topbar">
        <div class="topbar-title">Hasil Pemeriksaan</div>
    </div>
    <div class="page-content">
        <div class="menu-grid">
            <a href="{{ route('hasil.riwayat') }}" class="menu-item">
                <div class="menu-icon">&#128203;</div>
                <div class="menu-label">Riwayat Periksa</div>
            </a>
            <a href="{{ route('hasil.resep') }}" class="menu-item">
                <div class="menu-icon">&#128138;</div>
                <div class="menu-label">Resep Obat</div>
            </a>
            <a href="{{ route('hasil.rekam-medis') }}" class="menu-item">
                <div class="menu-icon">&#128196;</div>
                <div class="menu-label">Rekam Medis</div>
            </a>
            <a href="{{ route('hasil.lab') }}" class="menu-item">
                <div class="menu-icon">&#129514;</div>
                <div class="menu-label">Hasil Lab</div>
            </a>
            <a href="{{ route('hasil.radiologi') }}" class="menu-item">
                <div class="menu-icon">&#129658;</div>
                <div class="menu-label">Radiologi</div>
            </a>
        </div>
    </div>
</div>
@endsection
