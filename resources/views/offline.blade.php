<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Tidak Ada Koneksi — EPasien</title>
    @vite(['resources/css/app.css'])
</head>
<body>
<div class="offline-screen">
    <div class="offline-icon">📵</div>
    <div class="offline-title">Tidak Ada Koneksi</div>
    <div class="offline-sub">
        Pastikan perangkat Anda terhubung ke internet,<br>
        kemudian coba lagi.
    </div>
    <button class="btn-retry" id="btnRetry" onclick="coba()">Coba Lagi</button>
</div>

<script>
    function coba() {
        var btn = document.getElementById('btnRetry');
        btn.textContent = 'Memeriksa...';
        btn.disabled = true;

        fetch('/api/health')
            .then(function (r) {
                if (r.ok) {
                    window.location.href = '/';
                } else {
                    throw new Error('not ok');
                }
            })
            .catch(function () {
                btn.textContent = 'Coba Lagi';
                btn.disabled = false;
                alert('Server masih tidak dapat dijangkau. Periksa koneksi internet Anda.');
            });
    }
</script>
</body>
</html>
