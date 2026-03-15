@extends('layouts.app')
@section('title', 'Hasil Lab')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Hasil Lab</div>
    </div>
    <div class="page-content"><div id="container"></div></div>
</div>
@endsection
@push('init')
<script type="module">
(async function () {
    showLoading('container');
    try {
        var data = await apiFetch('/api/hasil/lab/list');
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data || data.data.length === 0) { showEmpty('container', '🧪', 'Belum ada hasil lab'); return; }
        data.data.forEach(function (d) {
            var a = el('a', { href: '/hasil/lab/detail?no_rawat=' + encodeURIComponent(d.no_rawat), style: 'display:flex;align-items:center;gap:12px;padding:14px;background:var(--color-card);border-radius:var(--radius-md);border:1px solid var(--color-border);margin-bottom:8px;text-decoration:none;color:var(--color-text);' });
            a.appendChild(el('span', { style: 'font-size:22px;' }, '🧪'));
            var info = el('div', { style: 'flex:1;' });
            info.appendChild(el('div', { style: 'font-weight:600;' }, d.tgl_periksa));
            info.appendChild(el('div', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);' }, d.nm_dokter));
            a.appendChild(info);
            a.appendChild(el('span', { style: 'color:var(--color-text-muted);' }, '\u203A'));
            c.appendChild(a);
        });
    } catch (e) { showError('container', e.message, function () { location.reload(); }); }
})();
</script>
@endpush
