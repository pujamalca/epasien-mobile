@extends('layouts.app')
@section('title', 'Edukasi Pasien')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Edukasi Pasien</div>
    </div>
    <div class="page-content"><div id="container"></div></div>
</div>
@endsection
@push('scripts')
<script>
(async function () {
    showLoading('container');
    try {
        var data = await apiFetch('/api/consent/edukasi');
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data || data.data.length === 0) { showEmpty('container', '&#128218;', 'Belum ada materi edukasi'); return; }
        data.data.forEach(function (d) {
            var row = el('div', { className: 'card', style: 'margin-bottom:8px;' });
            row.appendChild(el('div', { style: 'font-weight:600;' }, d.judul));
            row.appendChild(el('div', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);margin-top:4px;' }, d.tgl_upload));
            row.appendChild(el('div', { style: 'margin-top:8px;line-height:1.5;' }, d.isi));
            c.appendChild(row);
        });
    } catch (e) { showError('container', e.message, function () { location.reload(); }); }
})();
</script>
@endpush
