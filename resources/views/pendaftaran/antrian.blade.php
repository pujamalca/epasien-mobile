@extends('layouts.app')
@section('title', 'Antrian Hari Ini')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Antrian Hari Ini</div>
    </div>
    <div class="page-content">
        <div id="container"></div>
    </div>
</div>
@endsection
@push('scripts')
<script>
async function loadAntrian() {
    showLoading('container');
    try {
        var data = await apiFetch('/api/pendaftaran/antrian');
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data || data.data.length === 0) {
            showEmpty('container', '&#127967;', 'Belum ada antrian hari ini');
            return;
        }
        data.data.forEach(function (a) {
            var row = el('div', { className: 'card', style: 'margin-bottom:8px;' });
            var noA = el('div', { style: 'font-size:32px;font-weight:700;color:var(--color-primary);' }, 'No. ' + a.no_antrian);
            var poli = el('div', { style: 'font-weight:600;' }, a.nm_poli);
            var dok  = el('div', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);' }, a.nm_dokter);
            var stts = el('div', { className: 'badge badge-info', style: 'margin-top:8px;' }, a.stts_daftar);
            row.appendChild(noA);
            row.appendChild(poli);
            row.appendChild(dok);
            row.appendChild(stts);
            c.appendChild(row);
        });
    } catch (e) {
        showError('container', e.message, loadAntrian);
    }
}
</script>
@endpush

@push('init')
<script type="module">loadAntrian();</script>
@endpush
