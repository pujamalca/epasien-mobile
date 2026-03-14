@extends('layouts.app')
@section('title', 'Informasi RS')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Informasi RS</div>
    </div>
    <div class="page-content">
        <div id="stateLoad" class="state-loading"><div class="spinner"></div><div>Memuat...</div></div>
        <div id="stateContent" style="display:none;"></div>
        <div id="stateErr" class="state-error" style="display:none;">
            <div>Gagal memuat informasi RS.</div>
            <button class="btn btn-outline" onclick="loadData()" style="margin-top:12px;width:auto;padding:8px 20px;">Coba Lagi</button>
        </div>
    </div>
</div>
<script>
async function loadData() {
    document.getElementById('stateLoad').style.display='flex';
    document.getElementById('stateContent').style.display='none';
    document.getElementById('stateErr').style.display='none';
    try {
        const res = await apiFetch('/api/akun/info-rs');
        if (!res.ok) throw new Error();
        const d = await res.json();
        const info = d.data ?? d ?? {};
        const wrap = document.getElementById('stateContent');
        while (wrap.firstChild) wrap.removeChild(wrap.firstChild);
        const card = document.createElement('div');
        card.className = 'card';
        card.style.cssText = 'display:flex;flex-direction:column;gap:12px;';
        const fields = [
            ['Nama RS', info.nama_rs ?? info.nama ?? '-'],
            ['Alamat', info.alamat ?? '-'],
            ['Telepon', info.telepon ?? info.no_telp ?? '-'],
            ['Email', info.email ?? '-'],
            ['Website', info.website ?? '-'],
        ];
        fields.forEach(function([label, value]) {
            const row = document.createElement('div');
            row.appendChild(el('div', {style:'font-size:12px;color:var(--color-text-muted);margin-bottom:2px;'}, label));
            row.appendChild(el('div', {style:'font-weight:500;'}, value));
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
loadData();
</script>
@endsection
