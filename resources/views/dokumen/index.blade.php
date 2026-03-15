@extends('layouts.app')
@section('title', 'Dokumen')
@section('content')
<div class="page">
    <div class="topbar">
        <div class="topbar-title">Dokumen</div>
    </div>
    <div class="page-content">
        <div class="menu-grid">
            <a href="{{ route('dokumen.surat-kontrol') }}" class="menu-item">
                <div class="menu-icon">📅</div>
                <div class="menu-label">Surat Kontrol</div>
            </a>
            <a href="{{ route('dokumen.surat-sakit') }}" class="menu-item">
                <div class="menu-icon">&#127973;</div>
                <div class="menu-label">Surat Sakit</div>
            </a>
            <a href="{{ route('dokumen.surat-rujukan') }}" class="menu-item">
                <div class="menu-icon">&#128228;</div>
                <div class="menu-label">Surat Rujukan</div>
            </a>
            <a href="{{ route('dokumen.resume-medis') }}" class="menu-item">
                <div class="menu-icon">📋</div>
                <div class="menu-label">Resume Medis</div>
            </a>
            <a href="{{ route('dokumen.kartu-berobat') }}" class="menu-item">
                <div class="menu-icon">&#128179;</div>
                <div class="menu-label">Kartu Berobat</div>
            </a>
        </div>
    </div>
</div>
@endsection
