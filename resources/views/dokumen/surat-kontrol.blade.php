@extends('layouts.app')
@section('title', 'Surat Kontrol')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Surat Kontrol</div>
    </div>
    <div class="page-content"><div id="container"></div></div>
</div>
@endsection
@push('init')
<script type="module">
(async function () {
    showLoading('container');
    try {
        var data = await apiFetch('/api/dokumen/surat-kontrol');
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data || data.data.length === 0) { showEmpty('container', '📅', 'Belum ada surat kontrol'); return; }
        data.data.forEach(function (d) {
            var row = el('div', { className: 'card', style: 'margin-bottom:8px;' });
            row.appendChild(el('div', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);' }, 'Kontrol: ' + d.tgl_kontrol));
            row.appendChild(el('div', { style: 'font-weight:600;' }, d.nm_poli));
            row.appendChild(el('div', { style: 'font-size:var(--font-sm);' }, d.nm_dokter));
            if (d.keterangan) row.appendChild(el('div', { style: 'font-size:var(--font-sm);margin-top:4px;' }, d.keterangan));
            c.appendChild(row);
        });
    } catch (e) { showError('container', e.message, function () { location.reload(); }); }
})();
</script>
@endpush
