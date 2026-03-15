@extends('layouts.app')
@section('title', 'Jadwal Kontrol')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Jadwal Kontrol</div>
    </div>
    <div class="page-content">
        <div id="stateLoad" class="state-loading"><div class="spinner"></div><div>Memuat...</div></div>
        <div id="stateList" style="display:none;"></div>
        <div id="stateEmpty" class="state-empty" style="display:none;">
            <div class="state-icon">📅</div>
            <div>Tidak ada jadwal kontrol</div>
        </div>
        <div id="stateErr" class="state-error" style="display:none;">
            <div>Gagal memuat jadwal kontrol.</div>
            <button class="btn btn-outline" onclick="loadData()" style="margin-top:12px;width:auto;padding:8px 20px;">Coba Lagi</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function loadData() {
    document.getElementById('stateLoad').style.display='flex';
    document.getElementById('stateList').style.display='none';
    document.getElementById('stateEmpty').style.display='none';
    document.getElementById('stateErr').style.display='none';
    try {
        const data = await apiFetch('/api/akun/jadwal-kontrol');
        const list = data.data ?? data ?? [];
        if (!list.length) {
            document.getElementById('stateLoad').style.display='none';
            document.getElementById('stateEmpty').style.display='block';
            return;
        }
        const wrap = document.getElementById('stateList');
        while (wrap.firstChild) wrap.removeChild(wrap.firstChild);
        const card = document.createElement('div');
        card.className = 'card';
        card.style.padding = '0';
        card.style.overflow = 'hidden';
        list.forEach(function(item) {
            const row = document.createElement('div');
            row.style.cssText = 'padding:14px 16px;border-bottom:1px solid var(--color-border);';
            row.appendChild(el('div', {style:'font-weight:600;margin-bottom:4px;'}, item.tgl_kontrol ?? item.tanggal ?? '-'));
            row.appendChild(el('div', {style:'font-size:13px;'}, item.nama_poli ?? item.poli ?? '-'));
            row.appendChild(el('div', {style:'font-size:13px;color:var(--color-text-muted);'}, item.nama_dokter ?? item.dokter ?? ''));
            card.appendChild(row);
        });
        wrap.appendChild(card);
        document.getElementById('stateLoad').style.display='none';
        wrap.style.display='block';
    } catch {
        document.getElementById('stateLoad').style.display='none';
        document.getElementById('stateErr').style.display='block';
    }
}
</script>
@endpush

@push('init')
<script type="module">loadData();</script>
@endpush
