@extends('layouts.app')
@section('title', 'Detail Radiologi')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Detail Radiologi</div>
    </div>
    <div class="page-content"><div id="container"></div></div>
</div>
@endsection
@push('scripts')
<script>
var noRawat = new URLSearchParams(location.search).get('no_rawat') || '';
(async function () {
    showLoading('container');
    try {
        var data = await apiFetch('/api/hasil/radiologi/detail?no_rawat=' + encodeURIComponent(noRawat));
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data) { showEmpty('container', '&#129658;', 'Data tidak ditemukan'); return; }
        var d = data.data;
        var card = el('div', { className: 'card' });
        card.appendChild(el('div', { style: 'font-weight:600;font-size:var(--font-lg);margin-bottom:8px;' }, d.jenis_periksa));
        card.appendChild(el('div', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);margin-bottom:4px;' }, 'Dokter Radiologi: ' + (d.dokter_radiologi || '-')));
        if (d.hasil_periksa) {
            card.appendChild(el('div', { style: 'margin-top:8px;white-space:pre-wrap;' }, d.hasil_periksa));
        }
        if (d.file_foto) {
            var img = el('img', { src: '/api/hasil/radiologi/foto/' + encodeURIComponent(d.file_foto), style: 'width:100%;border-radius:var(--radius-md);margin-top:12px;', alt: 'Foto Radiologi' });
            card.appendChild(img);
        }
        c.appendChild(card);
    } catch (e) { showError('container', e.message, function () { location.reload(); }); }
})();
</script>
@endpush
