@extends('layouts.app')
@section('title', 'Pengumuman')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Pengumuman</div>
    </div>
    <div class="page-content">
        <div id="stateLoad" class="state-loading"><div class="spinner"></div><div>Memuat...</div></div>
        <div id="stateList" style="display:none;"></div>
        <div id="stateEmpty" class="state-empty" style="display:none;">
            <div class="state-icon">&#128226;</div>
            <div>Belum ada pengumuman</div>
        </div>
        <div id="stateErr" class="state-error" style="display:none;">
            <div>Gagal memuat pengumuman.</div>
            <button class="btn btn-outline" onclick="loadData()" style="margin-top:12px;width:auto;padding:8px 20px;">Coba Lagi</button>
        </div>
    </div>
</div>
<script>
async function loadData() {
    document.getElementById('stateLoad').style.display='flex';
    document.getElementById('stateList').style.display='none';
    document.getElementById('stateEmpty').style.display='none';
    document.getElementById('stateErr').style.display='none';
    try {
        const res = await apiFetch('/api/akun/pengumuman');
        if (!res.ok) throw new Error();
        const data = await res.json();
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
            row.style.padding = '14px 16px';
            row.style.borderBottom = '1px solid var(--color-border)';
            const title = el('div', {style:'font-weight:600;margin-bottom:4px;'}, item.judul ?? item.title ?? '-');
            const body = el('div', {style:'font-size:13px;color:var(--color-text-muted);'}, item.isi ?? item.content ?? '');
            row.appendChild(title);
            row.appendChild(body);
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
