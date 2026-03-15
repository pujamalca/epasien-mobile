@extends('layouts.app')
@section('title', 'Riwayat Periksa')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Riwayat Periksa</div>
    </div>
    <div class="page-content"><div id="container"></div></div>
</div>
@endsection
@push('init')
<script type="module">
(async function () {
    showLoading('container');
    try {
        var data = await apiFetch('/api/hasil/riwayat');
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data || data.data.length === 0) { showEmpty('container', '📋', 'Belum ada riwayat periksa'); return; }
        data.data.forEach(function (d) {
            var row = el('div', { className: 'card', style: 'margin-bottom:8px;' });
            row.appendChild(el('div', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);' }, d.tgl_registrasi));
            row.appendChild(el('div', { style: 'font-weight:600;' }, d.nm_poli));
            row.appendChild(el('div', { style: 'font-size:var(--font-sm);' }, d.nm_dokter));
            row.appendChild(el('span', { className: 'badge badge-info', style: 'margin-top:6px;' }, d.stts_daftar));
            c.appendChild(row);
        });
    } catch (e) { showError('container', e.message, function () { location.reload(); }); }
})();
</script>
@endpush
