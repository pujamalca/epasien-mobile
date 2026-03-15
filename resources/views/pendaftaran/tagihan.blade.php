@extends('layouts.app')
@section('title', 'Info Tagihan')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Info Tagihan</div>
    </div>
    <div class="page-content">
        <div id="container"></div>
    </div>
</div>
@endsection
@push('scripts')
<script>
async function loadTagihan() {
    showLoading('container');
    try {
        var data = await apiFetch('/api/pendaftaran/tagihan');
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data || data.data.length === 0) {
            showEmpty('container', '&#128179;', 'Belum ada tagihan');
            return;
        }
        data.data.forEach(function (t) {
            var row = el('div', { className: 'card', style: 'margin-bottom:8px;' });
            var tgl  = el('div', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);' }, t.tgl_registrasi);
            var poli = el('div', { style: 'font-weight:600;' }, t.nm_poli);
            var dok  = el('div', { style: 'font-size:var(--font-sm);' }, t.nm_dokter);
            var biaya = el('div', { style: 'font-weight:700;color:var(--color-primary);margin-top:4px;' }, 'Rp ' + Number(t.biaya_reg).toLocaleString('id-ID'));
            var bayar = el('span', { className: t.status_bayar === 'Sudah' ? 'badge badge-success' : 'badge badge-warning' }, t.status_bayar || 'Belum');
            row.appendChild(tgl);
            row.appendChild(poli);
            row.appendChild(dok);
            row.appendChild(biaya);
            row.appendChild(bayar);
            c.appendChild(row);
        });
    } catch (e) {
        showError('container', e.message, loadTagihan);
    }
}
</script>
@endpush

@push('init')
<script type="module">loadTagihan();</script>
@endpush
