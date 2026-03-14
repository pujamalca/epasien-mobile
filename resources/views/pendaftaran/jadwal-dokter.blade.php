@extends('layouts.app')
@section('title', 'Jadwal Dokter')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Jadwal Dokter</div>
    </div>
    <div class="page-content">
        <div class="form-group" style="margin-bottom:16px;">
            <label for="filterPoli">Filter Poliklinik</label>
            <select id="filterPoli"></select>
        </div>
        <div id="container"></div>
    </div>
</div>
@endsection
@push('scripts')
<script>
var HARI = ['','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

async function loadPoli() {
    var cached = getCachedData('/api/pendaftaran/poli-list', 3600000);
    var data   = cached || await apiFetch('/api/pendaftaran/poli-list');
    if (data && data.success) setCachedData('/api/pendaftaran/poli-list', data);
    if (!data || !data.data) return;
    var sel = document.getElementById('filterPoli');
    sel.appendChild(el('option', { value: '' }, 'Semua Poliklinik'));
    data.data.forEach(function (p) {
        sel.appendChild(el('option', { value: p.no_poli }, p.nm_poli));
    });
    sel.addEventListener('change', loadJadwal);
}

async function loadJadwal() {
    var poli = document.getElementById('filterPoli').value;
    showLoading('container');
    try {
        var data = await apiFetch('/api/pendaftaran/jadwal?poli=' + encodeURIComponent(poli));
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data || data.data.length === 0) {
            showEmpty('container', '&#128197;', 'Tidak ada jadwal');
            return;
        }
        data.data.forEach(function (j) {
            var row  = el('div', { className: 'card', style: 'margin-bottom:8px;' });
            var nm   = el('div', { style: 'font-weight:600;' }, j.nm_dokter);
            var poli = el('div', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);' }, j.nm_poli);
            var jam  = el('div', { style: 'font-size:var(--font-sm);' }, (HARI[j.hari_kerja] || j.hari_kerja) + ' \u00B7 ' + j.jam_mulai + ' \u2013 ' + j.jam_selesai);
            row.appendChild(nm);
            row.appendChild(poli);
            row.appendChild(jam);
            c.appendChild(row);
        });
    } catch (e) {
        showError('container', e.message, loadJadwal);
    }
}

loadPoli();
loadJadwal();
</script>
@endpush
