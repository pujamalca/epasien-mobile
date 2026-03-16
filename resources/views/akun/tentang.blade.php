@extends('layouts.app')
@section('title', 'Tentang Aplikasi')
@section('active_tab', 'akun')
@section('content')
<div class="page">
    <div class="topbar">
        <a href="{{ route('akun') }}" class="topbar-back">&#8249;</a>
        <div class="topbar-title">Tentang Aplikasi</div>
    </div>
    <div class="page-content">

        {{-- Logo & nama app --}}
        <div class="card" style="text-align:center;padding:28px 16px;">
            <div style="font-size:52px;margin-bottom:8px;">🏥</div>
            <div style="font-size:20px;font-weight:700;color:var(--color-primary);">EPasien Mobile</div>
            <div style="font-size:13px;color:var(--color-text-muted);margin-top:4px;">
                Versi {{ config('nativephp.version', '1.0.0') }}
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="card" style="margin-top:12px;">
            <div style="font-weight:600;margin-bottom:8px;">Tentang</div>
            <p style="font-size:14px;color:var(--color-text-muted);line-height:1.6;margin:0;">
                EPasien Mobile adalah aplikasi pasien digital yang memudahkan akses ke data medis,
                pendaftaran online, hasil pemeriksaan, dokumen, dan informed consent secara langsung
                dari genggaman tangan Anda.
            </p>
        </div>

        {{-- Lisensi --}}
        <div class="card" style="margin-top:12px;">
            <div style="font-weight:600;margin-bottom:10px;">⚖️ Lisensi</div>
            <div style="font-size:14px;line-height:1.7;color:var(--color-text-muted);">
                <div style="margin-bottom:6px;">✅ Bebas digunakan & dimodifikasi</div>
                <div style="margin-bottom:6px;">✅ Boleh didistribusikan ulang</div>
                <div style="margin-bottom:6px;color:var(--color-danger);font-weight:500;">
                    ❌ Dilarang diperjualbelikan dalam bentuk apapun
                </div>
                <div style="margin-top:8px;font-size:13px;">
                    Jika Anda mendistribusikan ulang atau memodifikasi, tetap cantumkan kredit asli.
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="card" style="margin-top:12px;">
            <div style="font-weight:600;margin-bottom:8px;">© Hak Cipta</div>
            <div style="font-size:14px;color:var(--color-text-muted);line-height:1.6;">
                Copyright © {{ date('Y') }} <strong>Puja M Alca</strong><br>
                Open Source — Gratis untuk digunakan, tidak untuk dijual.
            </div>
        </div>

        {{-- Dukungan --}}
        <div class="card" style="margin-top:12px;border:1.5px solid var(--color-primary-soft);">
            <div style="font-weight:600;margin-bottom:10px;color:var(--color-primary);">💚 Dukung Pengembangan</div>
            <p style="font-size:14px;color:var(--color-text-muted);line-height:1.6;margin:0 0 12px;">
                Jika aplikasi ini bermanfaat, Anda bisa mendukung pengembangan lebih lanjut melalui donasi:
            </p>
            <div style="background:var(--color-primary-soft);border-radius:10px;padding:14px 16px;">
                <div style="font-size:13px;color:var(--color-text-muted);margin-bottom:4px;">Transfer Bank</div>
                <div style="font-weight:700;font-size:16px;color:var(--color-primary);">BSI</div>
                <div style="font-size:15px;font-weight:600;letter-spacing:1px;margin-top:4px;">7190075731</div>
                <div style="font-size:13px;color:var(--color-text-muted);margin-top:2px;">a.n Puja M Alca</div>
            </div>
            <p style="font-size:13px;color:var(--color-text-muted);margin:10px 0 0;text-align:center;">
                Setiap dukungan sangat berarti. Terima kasih! 🙏
            </p>
        </div>

        {{-- Tech stack --}}
        <div class="card" style="margin-top:12px;margin-bottom:24px;">
            <div style="font-weight:600;margin-bottom:8px;">🛠️ Dibangun dengan</div>
            <div style="font-size:13px;color:var(--color-text-muted);line-height:1.8;">
                Laravel 12 · NativePHP for Android<br>
                SIMRS Khanza · Firebase FCM
            </div>
        </div>

    </div>
</div>
@endsection
