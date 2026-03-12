<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Dashboard — EPasien</title>
    @vite(['resources/css/app.css'])
</head>
<body>
<div class="dashboard">

    {{-- Header pasien --}}
    <div class="dashboard-header">
        <div class="salam">Selamat datang,</div>
        <div class="nama-pasien">{{ $pasien['nama'] ?? 'Pasien' }}</div>
        <div class="no-rm">No. RM: {{ $pasien['no_rm'] ?? '-' }}</div>
    </div>

    {{-- Menu per grup --}}
    @foreach ($menus as $grup)
        <div class="menu-section">
            <div class="menu-section-title">{{ $grup['group'] }}</div>
            <div class="menu-grid">
                @foreach ($grup['items'] as $item)
                    @if ($item['act'] === '__logout__')
                        <form method="POST" action="{{ route('logout') }}" style="display:contents;">
                            @csrf
                            <button type="submit" class="menu-item logout-item">
                                <span class="menu-icon">{{ $item['icon'] }}</span>
                                <span class="menu-label">{{ $item['label'] }}</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('webview', ['act' => $item['act']]) }}" class="menu-item">
                            <span class="menu-icon">{{ $item['icon'] }}</span>
                            <span class="menu-label">{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach

</div>

{{-- Bottom Navigation --}}
<nav class="bottom-nav">
    <a href="{{ route('dashboard') }}" class="nav-item active">
        <span class="nav-icon">🏠</span>
        <span>Home</span>
    </a>
    <a href="{{ route('webview', ['act' => 'FormBooking']) }}" class="nav-item">
        <span class="nav-icon">📋</span>
        <span>Booking</span>
    </a>
    <a href="{{ route('webview', ['act' => 'listpengumuman']) }}" class="nav-item">
        <span class="nav-icon">🔔</span>
        <span>Info</span>
    </a>
    <a href="{{ route('webview', ['act' => 'ProfilPasien']) }}" class="nav-item">
        <span class="nav-icon">👤</span>
        <span>Profil</span>
    </a>
</nav>
</body>
</html>
