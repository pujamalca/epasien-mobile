@extends('layouts.app')
@section('title', 'Resep Obat')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Resep Obat</div>
    </div>
    <div class="page-content"><div id="container"></div></div>
</div>
@endsection
@push('scripts')
<script>
(async function () {
    showLoading('container');
    try {
        var data = await apiFetch('/api/hasil/resep');
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data || data.data.length === 0) { showEmpty('container', '&#128138;', 'Belum ada resep'); return; }
        data.data.forEach(function (d) {
            var row = el('div', { className: 'card', style: 'margin-bottom:8px;' });
            row.appendChild(el('div', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);' }, d.tgl_peresepan));
            row.appendChild(el('div', { style: 'font-weight:600;' }, d.nama_brng));
            row.appendChild(el('div', { style: 'font-size:var(--font-sm);' }, d.jml + ' — ' + d.aturan_pakai));
            c.appendChild(row);
        });
    } catch (e) { showError('container', e.message, function () { location.reload(); }); }
})();
</script>
@endpush
