@extends('layouts.app')
@section('title', 'Kartu Berobat')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Kartu Berobat</div>
    </div>
    <div class="page-content"><div id="container"></div></div>
</div>
@endsection
@push('init')
<script type="module">
(async function () {
    showLoading('container');
    try {
        var data = await apiFetch('/api/dokumen/kartu-berobat');
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data) { showEmpty('container', '&#128179;', 'Data tidak ditemukan'); return; }
        var d = data.data;
        var card = el('div', { className: 'card' });
        card.appendChild(el('div', { style: 'font-size:var(--font-lg);font-weight:700;margin-bottom:12px;color:var(--color-primary);' }, 'Kartu Berobat'));
        var fields = [
            ['No. RM', d.no_rkm_medis],
            ['Nama', d.nm_pasien],
            ['Tgl Lahir', d.tgl_lahir],
            ['Jenis Kelamin', d.jk],
            ['Alamat', d.alamat],
            ['No. HP', d.no_hp],
        ];
        fields.forEach(function (f) {
            var row = el('div', { style: 'display:flex;gap:8px;margin-bottom:8px;' });
            row.appendChild(el('div', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);width:100px;flex-shrink:0;' }, f[0]));
            row.appendChild(el('div', { style: 'flex:1;' }, f[1] || '-'));
            card.appendChild(row);
        });
        c.appendChild(card);
    } catch (e) { showError('container', e.message, function () { location.reload(); }); }
})();
</script>
@endpush
