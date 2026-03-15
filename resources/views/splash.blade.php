<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>EPasien</title>
    @vite(['resources/css/app.css'])
</head>
<body>
<div class="splash-screen" id="splash">
    <div class="splash-logo" id="splashLogo">🏥</div>
    <div class="splash-nama" id="splashNama">EPasien</div>
    <div class="splash-sub">Portal Kesehatan Anda</div>
    <div class="splash-loader"></div>
</div>

<script>
(function () {
    var isLoggedIn = {{ app(\App\Services\SessionService::class)->isLoggedIn() ? 'true' : 'false' }};

    function redirect(path) {
        window.location.href = path;
    }

    fetch('/api/health')
        .then(function (r) { return r.ok ? r.json() : null; })
        .then(function (data) {
            if (data && data.success) {
                if (data.nama_rs) {
                    document.getElementById('splashNama').textContent = data.nama_rs;
                }
                if (data.logo_url) {
                    var img = document.createElement('img');
                    img.src = data.logo_url;
                    img.alt = 'Logo RS';
                    var logoEl = document.getElementById('splashLogo');
                    logoEl.textContent = '';
                    logoEl.appendChild(img);
                }
            }
        })
        .catch(function () {
            setTimeout(function () { redirect('/offline'); }, 500);
            return;
        })
        .finally(function () {
            setTimeout(function () {
                redirect(isLoggedIn ? '/dashboard' : '/login');
            }, 2000);
        });
})();
</script>
</body>
</html>
