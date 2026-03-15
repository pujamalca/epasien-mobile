@extends('layouts.app')
@section('title', 'Daftar Poliklinik')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Poliklinik</div>
    </div>
    <div class="page-content">
        <div id="container"></div>
    </div>
</div>
@endsection
@push('scripts')
<script>
async function loadPoli() {
    var cached = getCachedData('/api/pendaftaran/poli-list', 3600000);
    showLoading('container');
    try {
        var data = cached || await apiFetch('/api/pendaftaran/poli-list');
        if (data && data.success) setCachedData('/api/pendaftaran/poli-list', data);
        var c = document.getElementById('container');
        c.textContent = '';
        if (!data || !data.data || data.data.length === 0) {
            showEmpty('container', '&#127973;', 'Tidak ada poliklinik');
            return;
        }
        var list = el('div', { style: 'background:var(--color-card);border-radius:var(--radius-md);border:1px solid var(--color-border);overflow:hidden;' });
        data.data.forEach(function (p) {
            var row = el('div', { className: 'card-row' });
            row.appendChild(el('span', { className: 'card-icon' }, '&#127973;'));
            row.appendChild(el('span', { className: 'card-label' }, p.nm_poli));
            list.appendChild(row);
        });
        c.appendChild(list);
    } catch (e) {
        showError('container', e.message, loadPoli);
    }
}
</script>
@endpush

@push('init')
<script type="module">loadPoli();</script>
@endpush
