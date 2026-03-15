@extends('layouts.app')
@section('title', 'Booking Online')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Booking Online</div>
    </div>
    <div class="page-content">
        <div class="form-group" style="margin-bottom:16px;">
            <label>Poliklinik</label>
            <select id="selPoli"></select>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label>Dokter</label>
            <select id="selDokter"></select>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label>Tanggal</label>
            <input type="date" id="tglDaftar" value="{{ date('Y-m-d') }}">
        </div>
        <button class="btn btn-primary" id="btnBooking" onclick="doBooking()">Booking Sekarang</button>
        <div id="result" style="margin-top:16px;"></div>
    </div>
</div>
@endsection
@push('scripts')
<script>
var jadwalData = [];

async function init() {
    var poli = await apiFetch('/api/pendaftaran/poli-list');
    if (!poli || !poli.data) return;
    var selPoli = document.getElementById('selPoli');
    poli.data.forEach(function (p) {
        selPoli.appendChild(el('option', { value: p.no_poli }, p.nm_poli));
    });
    selPoli.addEventListener('change', loadDokter);
    loadDokter();
}

async function loadDokter() {
    var poli = document.getElementById('selPoli').value;
    var data = await apiFetch('/api/pendaftaran/jadwal?poli=' + encodeURIComponent(poli));
    jadwalData = data ? data.data || [] : [];
    var sel = document.getElementById('selDokter');
    sel.textContent = '';
    var seen = {};
    jadwalData.forEach(function (j) {
        if (!seen[j.kd_dokter]) {
            seen[j.kd_dokter] = true;
            sel.appendChild(el('option', { value: j.kd_dokter || j.nm_dokter }, j.nm_dokter));
        }
    });
}

async function doBooking() {
    var btn = document.getElementById('btnBooking');
    btn.disabled = true;
    try {
        var data = await apiFetch('/api/pendaftaran/booking', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                kd_poli:    document.getElementById('selPoli').value,
                kd_dokter:  document.getElementById('selDokter').value,
                tgl_daftar: document.getElementById('tglDaftar').value,
            })
        });
        var r = document.getElementById('result');
        r.textContent = '';
        if (data && data.success) {
            showToast('Booking berhasil! No. Antrian: ' + data.no_antrian, 'success');
            var info = el('div', { className: 'card' });
            info.appendChild(el('div', { style: 'font-size:var(--font-lg);font-weight:700;color:var(--color-primary);' }, 'Antrian No. ' + data.no_antrian));
            info.appendChild(el('div', { style: 'color:var(--color-text-muted);' }, 'No. Reg: ' + data.no_reg));
            r.appendChild(info);
        } else {
            showToast((data && data.error) || 'Booking gagal', 'error');
        }
    } catch (e) {
        showToast(e.message, 'error');
    } finally {
        btn.disabled = false;
    }
}

</script>
@endpush

@push('init')
<script type="module">init();</script>
@endpush
