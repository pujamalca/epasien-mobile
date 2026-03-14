<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Login — EPasien</title>
    @vite(['resources/css/app.css'])
</head>
<body>

@if(session('info'))
<div style="background:var(--color-primary-soft);color:var(--color-primary);padding:10px 16px;font-size:13px;text-align:center;">
    {{ session('info') }}
</div>
@endif

<!-- Overlay & Toast -->
<div id="toastOverlay" class="toast-overlay"></div>
<div id="toast" class="toast" role="alert" aria-live="assertive">
    <div class="toast-icon" id="toastIcon"></div>
    <div class="toast-msg"  id="toastMsg"></div>
</div>

<div class="login-screen">
    <div class="login-header">
        <div class="login-logo" id="loginLogo">🏥</div>
        <div class="login-rs-nama" id="loginNamaRS">Rumah Sakit</div>
        <div class="login-sub">Selamat Datang</div>
    </div>

    <div class="login-form">
        <form id="loginForm">
            @csrf
            <div style="display:flex;flex-direction:column;gap:16px;">
                <div class="form-group">
                    <label for="no_rm">No. Rekam Medis</label>
                    <input type="text" id="no_rm" name="no_rm"
                           placeholder="Contoh: 00-01-23"
                           autocomplete="username"
                           required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-password-wrap">
                        <input type="password" id="password" name="password"
                               placeholder="••••••••"
                               autocomplete="current-password"
                               required>
                        <button type="button" id="togglePassword" class="btn-eye" aria-label="Lihat password">
                            <span id="eyeIcon">👁️</span>
                        </button>
                    </div>
                </div>
                <button type="submit" id="btnLogin" class="btn-login">MASUK</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var CSRF = document.querySelector('input[name="_token"]').value;

    /* ── Toast ── */
    var ICONS = { success: '✅', error: '❌' };
    function showToast(msg, type) {
        var el      = document.getElementById('toast');
        var overlay = document.getElementById('toastOverlay');
        document.getElementById('toastIcon').textContent = ICONS[type] || '❕';
        document.getElementById('toastMsg').textContent  = msg;
        el.className      = 'toast toast-' + type + ' toast-show';
        overlay.className = 'toast-overlay active';
        clearTimeout(el._t);
        el._t = setTimeout(function () {
            el.className      = 'toast';
            overlay.className = 'toast-overlay';
        }, type === 'success' ? 1400 : 3000);
    }

    /* ── Toggle password ── */
    document.getElementById('togglePassword').addEventListener('click', function () {
        var inp = document.getElementById('password');
        var icon = document.getElementById('eyeIcon');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.textContent = '🙈';
        } else {
            inp.type = 'password';
            icon.textContent = '👁️';
        }
    });

    /* ── Login via fetch ── */
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var btn   = document.getElementById('btnLogin');
        var noRm  = document.getElementById('no_rm').value.trim();
        var pass  = document.getElementById('password').value;

        btn.disabled    = true;
        btn.textContent = 'Memproses...';

        fetch('{{ route("login.post") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ no_rm: noRm, password: pass })
        })
        .then(function (r) {
            return r.json().then(function (data) {
                return { status: r.status, data: data };
            });
        })
        .then(function (res) {
            if (res.status === 422) {
                // Validasi Laravel — ambil pesan pertama
                var errs = res.data.errors || {};
                var keys = Object.keys(errs);
                var msg  = keys.length ? errs[keys[0]][0] : (res.data.message || 'Data tidak valid.');
                showToast(msg, 'error');
                btn.disabled    = false;
                btn.textContent = 'MASUK';
                return;
            }
            if (res.data.success) {
                showToast('Login berhasil!', 'success');
                setTimeout(function () {
                    window.location.href = '{{ route("dashboard") }}';
                }, 1200);
            } else {
                showToast(res.data.message || 'No. RM atau password salah.', 'error');
                btn.disabled    = false;
                btn.textContent = 'MASUK';
            }
        })
        .catch(function () {
            showToast('Gagal terhubung ke server.', 'error');
            btn.disabled    = false;
            btn.textContent = 'MASUK';
        });
    });

    /* ── Ambil nama & logo RS ── */
    var baseUrl = '{{ config("epasien.base_url") }}';
    fetch(baseUrl + '/epasien/api/settings.php', { signal: AbortSignal.timeout(5000) })
        .then(function (r) { return r.ok ? r.json() : null; })
        .then(function (data) {
            if (!data || !data.success) return;
            if (data.nama_rs) {
                document.getElementById('loginNamaRS').textContent = data.nama_rs;
                document.title = 'Login — ' + data.nama_rs;
            }
            if (data.logo_url) {
                var img = document.createElement('img');
                img.src = data.logo_url;
                img.alt = 'Logo';
                var logoEl = document.getElementById('loginLogo');
                logoEl.textContent = '';
                logoEl.appendChild(img);
            }
        })
        .catch(function () {});
})();
</script>
</body>
</html>
