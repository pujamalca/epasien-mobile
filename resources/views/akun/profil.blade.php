@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="page">
    <div class="topbar">
        <button class="topbar-back" onclick="history.back()">&#8592;</button>
        <div class="topbar-title">Profil Saya</div>
    </div>
    <div class="page-content">
        <div id="stateLoad" class="state-loading"><div class="spinner"></div><div>Memuat...</div></div>
        <div id="stateContent" style="display:none;">
            <div class="card" style="margin-bottom:16px;">
                <div id="profilNama" style="font-weight:600;font-size:16px;margin-bottom:4px;"></div>
                <div id="profilNoRm" style="color:var(--color-text-muted);font-size:13px;"></div>
            </div>
            <form id="formProfil" class="card" style="display:flex;flex-direction:column;gap:14px;">
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="tel" id="noHp" name="no_hp" maxlength="15" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" maxlength="200" style="resize:none;border:1px solid var(--color-border);border-radius:var(--radius-sm);padding:11px 14px;font-size:var(--font-md);font-family:inherit;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" id="btnSimpan">Simpan Perubahan</button>
            </form>
        </div>
        <div id="stateErr" style="display:none;" class="state-error">
            <div>Gagal memuat profil.</div>
            <button class="btn btn-outline" onclick="loadProfil()" style="margin-top:12px;width:auto;padding:8px 20px;">Coba Lagi</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function loadProfil() {
    document.getElementById('stateLoad').style.display='flex';
    document.getElementById('stateContent').style.display='none';
    document.getElementById('stateErr').style.display='none';
    try {
        const d = await apiFetch('/api/akun/profil');
        document.getElementById('profilNama').textContent = d.nama ?? '-';
        document.getElementById('profilNoRm').textContent = 'No. RM: ' + (d.no_rm ?? '-');
        document.getElementById('noHp').value = d.no_hp ?? '';
        document.getElementById('alamat').value = d.alamat ?? '';
        document.getElementById('stateLoad').style.display='none';
        document.getElementById('stateContent').style.display='block';
    } catch {
        document.getElementById('stateLoad').style.display='none';
        document.getElementById('stateErr').style.display='block';
    }
}

document.getElementById('formProfil').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (document.getElementById('stateContent').style.display === 'none') return;
    const btn = document.getElementById('btnSimpan');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';
    try {
        const d = await apiFetch('/api/akun/profil', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                no_hp: document.getElementById('noHp').value,
                alamat: document.getElementById('alamat').value,
            })
        });
        showToast('Profil berhasil disimpan', 'success');
    } catch {
        showToast('Gagal menyimpan profil', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Simpan Perubahan';
    }
});

</script>
@endpush

@push('init')
<script type="module">loadProfil();</script>
@endpush
