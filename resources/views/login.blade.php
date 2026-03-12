<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Login — EPasien</title>
    @vite(['resources/css/app.css'])
</head>
<body>
<div class="login-screen">
    <div class="login-header">
        <div class="login-logo" id="loginLogo">🏥</div>
        <div class="login-rs-nama" id="loginNamaRS">Rumah Sakit</div>
        <div class="login-sub">Selamat Datang</div>
    </div>

    <div class="login-form">
        @if(!empty($error))
            <div class="alert-error">{{ $error }}</div>
        @endif
        @if(session('info'))
            <div class="alert-info">{{ session('info') }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div style="display:flex;flex-direction:column;gap:16px;">
                <div class="form-group">
                    <label for="no_rm">No. Rekam Medis</label>
                    <input type="text" id="no_rm" name="no_rm"
                           value="{{ $no_rm ?? '' }}"
                           placeholder="Contoh: 00-01-23"
                           autocomplete="username"
                           required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••"
                           autocomplete="current-password"
                           required>
                </div>
                <button type="submit" class="btn-login">MASUK</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
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
                img.alt = 'Logo RS';
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
