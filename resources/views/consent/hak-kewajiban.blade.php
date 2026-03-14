@extends('layouts.app')
@section('title', 'Hak & Kewajiban Pasien')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Hak &amp; Kewajiban</div>
    </div>
    <div class="page-content"><div id="container"></div></div>
</div>
@endsection
@push('scripts')
<script>
(async function () {
    showLoading('container');
    try {
        var data = await apiFetch('/api/consent/hak-kewajiban');
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data) { showEmpty('container', '&#9878;&#65039;', 'Data tidak tersedia'); return; }
        var d = data.data;
        if (d.isi_hak) {
            var hakCard = el('div', { className: 'card', style: 'margin-bottom:16px;' });
            hakCard.appendChild(el('div', { style: 'font-weight:700;margin-bottom:8px;' }, 'Hak Pasien'));
            hakCard.appendChild(el('div', { style: 'line-height:1.6;' }, d.isi_hak));
            c.appendChild(hakCard);
        }
        if (d.isi_kewajiban) {
            var kwjCard = el('div', { className: 'card' });
            kwjCard.appendChild(el('div', { style: 'font-weight:700;margin-bottom:8px;' }, 'Kewajiban Pasien'));
            kwjCard.appendChild(el('div', { style: 'line-height:1.6;' }, d.isi_kewajiban));
            c.appendChild(kwjCard);
        }
    } catch (e) { showError('container', e.message, function () { location.reload(); }); }
})();
</script>
@endpush
