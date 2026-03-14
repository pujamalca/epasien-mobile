@extends('layouts.app')
@section('title', 'Penolakan Tindakan')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" id="backBtn">&#8592;</button>
        <div class="topbar-title">Penolakan Tindakan</div>
    </div>
    <div class="page-content">
        <div id="listView">
            <div id="container"></div>
        </div>
        <div id="signView" style="display:none;">
            <div class="card" style="margin-bottom:16px;">
                <div id="signTitle" style="font-weight:600;margin-bottom:4px;"></div>
                <div style="font-size:var(--font-sm);color:var(--color-text-muted);">Tanda tangan di bawah ini</div>
            </div>
            <canvas id="signCanvas" width="320" height="200"
                style="width:100%;height:200px;border:2px dashed var(--color-border);border-radius:var(--radius-md);touch-action:none;background:#fff;display:block;margin-bottom:12px;"></canvas>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                <button class="btn btn-outline" id="clearBtn" style="width:auto;padding:10px 20px;">Hapus</button>
                <label style="display:flex;align-items:center;gap:6px;font-size:var(--font-sm);cursor:pointer;">
                    <input type="checkbox" id="confirmCheck"> Saya menolak tindakan ini
                </label>
            </div>
            <button class="btn btn-primary" id="btnSign">Tolak &amp; Kirim</button>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function () {
    var currentNoReg = null;
    var strokes = 0;
    var canvas  = document.getElementById('signCanvas');
    var ctx     = canvas.getContext('2d');
    ctx.strokeStyle = '#1E293B'; ctx.lineWidth = 2; ctx.lineCap = 'round';

    function getPos(e) {
        var rect = canvas.getBoundingClientRect();
        var src  = e.touches ? e.touches[0] : e;
        return {
            x: (src.clientX - rect.left) * (canvas.width  / rect.width),
            y: (src.clientY - rect.top)  * (canvas.height / rect.height)
        };
    }
    var drawing = false;
    canvas.addEventListener('mousedown',  function (e) { drawing = true; ctx.beginPath(); var p = getPos(e); ctx.moveTo(p.x, p.y); });
    canvas.addEventListener('mousemove',  function (e) { if (!drawing) return; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); strokes++; });
    canvas.addEventListener('mouseup',    function ()  { drawing = false; });
    canvas.addEventListener('touchstart', function (e) { e.preventDefault(); drawing = true; ctx.beginPath(); var p = getPos(e); ctx.moveTo(p.x, p.y); }, { passive: false });
    canvas.addEventListener('touchmove',  function (e) { e.preventDefault(); if (!drawing) return; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); strokes++; }, { passive: false });
    canvas.addEventListener('touchend',   function ()  { drawing = false; });

    document.getElementById('clearBtn').addEventListener('click', function () {
        ctx.clearRect(0, 0, canvas.width, canvas.height); strokes = 0;
    });

    document.getElementById('backBtn').addEventListener('click', function () {
        if (document.getElementById('signView').style.display !== 'none') {
            showListView();
        } else {
            history.back();
        }
    });

    function showListView() {
        document.getElementById('signView').style.display = 'none';
        document.getElementById('listView').style.display = 'block';
    }

    function showSignView(noReg, title) {
        currentNoReg = noReg;
        document.getElementById('signTitle').textContent = title;
        document.getElementById('listView').style.display = 'none';
        document.getElementById('signView').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        strokes = 0;
        document.getElementById('confirmCheck').checked = false;
    }

    document.getElementById('btnSign').addEventListener('click', async function () {
        if (strokes < 20) { showToast('Tanda tangan terlalu singkat', 'error'); return; }
        if (!document.getElementById('confirmCheck').checked) { showToast('Centang persetujuan terlebih dahulu', 'error'); return; }
        var btn = this; btn.disabled = true; btn.textContent = 'Mengirim...';
        try {
            var ttd  = canvas.toDataURL('image/png');
            var data = await apiFetch('/api/consent/sign/penolakan', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ no_reg: currentNoReg, tanda_tangan: ttd, confirmed: true })
            });
            if (data && data.success) {
                showToast('Penolakan berhasil disimpan', 'success');
                setTimeout(function () { showListView(); loadList(); }, 1200);
            } else {
                showToast((data && data.error) || 'Gagal menyimpan', 'error');
            }
        } catch (e) { showToast(e.message, 'error'); }
        btn.disabled = false; btn.textContent = 'Tolak & Kirim';
    });

    async function loadList() {
        showLoading('container');
        try {
            var data = await apiFetch('/api/consent/list/penolakan');
            var c = document.getElementById('container');
            c.textContent = '';
            if (!data || !data.data || data.data.length === 0) {
                showEmpty('container', '&#9997;&#65039;', 'Tidak ada penolakan tindakan');
                return;
            }
            data.data.forEach(function (d) {
                var row  = el('div', { className: 'card', style: 'margin-bottom:8px;cursor:pointer;' });
                var nm   = el('div', { style: 'font-weight:600;' }, d.nama_tindakan);
                var dok  = el('div', { style: 'font-size:var(--font-sm);color:var(--color-text-muted);' }, d.nm_dokter);
                var badgeCls = d.status === 'Tolak' ? 'badge badge-danger' : 'badge badge-warning';
                var badge = el('span', { className: badgeCls, style: 'margin-top:6px;' }, d.status === 'Tolak' ? 'Sudah Ditolak' : 'Menunggu TTD');
                row.appendChild(nm);
                row.appendChild(dok);
                row.appendChild(badge);
                if (d.status !== 'Tolak') {
                    row.addEventListener('click', function () { showSignView(d.no_reg, d.nama_tindakan); });
                }
                c.appendChild(row);
            });
        } catch (e) {
            showError('container', e.message, loadList);
        }
    }

    loadList();
})();
</script>
@endpush
