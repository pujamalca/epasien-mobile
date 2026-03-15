@extends('layouts.app')
@section('title', 'Resume Medis')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Resume Medis</div>
    </div>
    <div class="page-content"><div id="container"></div></div>
</div>
@endsection
@push('init')
<script type="module">
(async function () {
    showLoading('container');
    try {
        var data = await apiFetch('/api/dokumen/resume-medis');
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data || data.data.length === 0) { showEmpty('container', '📋', 'Belum ada resume medis'); return; }
        data.data.forEach(function (d) {
            var row = el('div', { className: 'card', style: 'margin-bottom:8px;' });
            row.appendChild(el('div', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);' }, d.tgl_registrasi));
            row.appendChild(el('div', { style: 'font-weight:600;' }, d.nm_poli + ' — ' + d.nm_dokter));
            if (d.anamnesa) row.appendChild(el('div', { style: 'font-size:var(--font-sm);margin-top:4px;' }, 'Anamnesa: ' + d.anamnesa));
            if (d.diagnosa_awal) row.appendChild(el('div', { style: 'font-size:var(--font-sm);' }, 'Diagnosa: ' + d.diagnosa_awal));
            if (d.tindakan) row.appendChild(el('div', { style: 'font-size:var(--font-sm);' }, 'Tindakan: ' + d.tindakan));
            c.appendChild(row);
        });
    } catch (e) { showError('container', e.message, function () { location.reload(); }); }
})();
</script>
@endpush
