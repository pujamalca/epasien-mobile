@extends('layouts.app')
@section('title', 'Detail Lab')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Detail Lab</div>
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
        var data = await apiFetch('/api/hasil/lab/detail?no_rawat=' + encodeURIComponent(noRawat));
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data || data.data.length === 0) { showEmpty('container', '&#129514;', 'Tidak ada detail'); return; }
        data.data.forEach(function (d) {
            var row = el('div', { className: 'card', style: 'margin-bottom:8px;' });
            row.appendChild(el('div', { style: 'font-weight:600;' }, d.nama_item));
            var hasil = el('div', { style: 'display:flex;justify-content:space-between;margin-top:4px;' });
            hasil.appendChild(el('span', {}, d.hasil + ' ' + (d.satuan || '')));
            hasil.appendChild(el('span', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);' }, 'Rujukan: ' + (d.nilai_rujukan || '-')));
            row.appendChild(hasil);
            c.appendChild(row);
        });
    } catch (e) { showError('container', e.message, function () { location.reload(); }); }
})();
</script>
@endpush
