@extends('layouts.app')
@section('title', 'Beranda')
@section('content')
<div class="page">
    <div class="topbar">
        <div class="topbar-title">EPasien</div>
    </div>
    <div class="page-content">
        <div class="card" style="margin-bottom:20px;background:var(--color-primary);color:#fff;border:none;">
            <div style="font-size:var(--font-sm);opacity:.8;">Selamat datang</div>
            <div style="font-size:var(--font-lg);font-weight:700;margin:4px 0;">
                {{ $pasien['nm_pasien'] ?? $pasien['nama'] ?? 'Pasien' }}
            </div>
            <div style="font-size:var(--font-sm);opacity:.8;">
                No. RM: {{ $pasien['no_rkm_medis'] ?? $pasien['no_rm'] ?? '-' }}
            </div>
        </div>

        @foreach(config('epasien.menus', []) as $grup)
        <div class="section">
            <div class="section-title">{{ $grup['group'] }}</div>
            <div style="background:var(--color-card);border-radius:var(--radius-md);border:1px solid var(--color-border);overflow:hidden;">
                @foreach($grup['items'] as $item)
                @if($item['act'] !== '__logout__')
                <a href="{{ route('menu.dispatch', ['act' => $item['act']]) }}"
                   class="card-row" style="text-decoration:none;color:var(--color-text);">
                    <span class="card-icon">{{ $item['icon'] }}</span>
                    <span class="card-label">{{ $item['label'] }}</span>
                    <span class="card-chevron">&#8250;</span>
                </a>
                @else
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="card-row" style="width:100%;border:none;background:none;cursor:pointer;text-align:left;">
                        <span class="card-icon">{{ $item['icon'] }}</span>
                        <span class="card-label" style="color:var(--color-danger);">{{ $item['label'] }}</span>
                    </button>
                </form>
                @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
