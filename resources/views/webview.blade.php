<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>EPasien</title>
    @vite(['resources/css/app.css'])
    {{--
        NativePHP WebView Implementation Notes:
        - Native WebView load: NativePHP\Mobile\Facades\WebView::load($url)
        - Cookie injection: WebView::setCookie($domain, 'PHPSESSID=' . $sessionId)
        - URL intercept: WebView::onUrlChange(fn($url) => ...)
        - Back button: WebView::goBack() / Native::back()
        Semua logika native di-handle oleh NativePHP service provider / AppServiceProvider,
        bukan di sini. View ini hanya berisi UI top bar.
    --}}
</head>
<body>
<div class="webview-container">

    {{-- Top bar --}}
    <div class="webview-topbar">
        <button class="webview-back-btn" onclick="goBack()" aria-label="Kembali">&#8592;</button>
        <div class="webview-domain">{{ $domain ?? 'domain-rs.com' }}</div>
    </div>

    {{--
        Frame ini digunakan sebagai FALLBACK jika NativePHP WebView native API
        belum tersedia di environment saat ini (misal: development di browser biasa).
        Di APK sesungguhnya, NativePHP akan menggantikan ini dengan native WebView.
    --}}
    <iframe
        id="epasienFrame"
        class="webview-frame"
        src="{{ $url }}"
        title="EPasien"
        allow="camera; microphone; geolocation"
    ></iframe>

</div>

<script>
    var sessionId = '{{ $sessionId }}';
    var targetUrl = '{{ $url }}';
    var domain    = '{{ $domain }}';

    // Inject PHPSESSID cookie ke domain RS (fallback browser dev mode)
    // Di APK: NativePHP handle ini via WebView::setCookie() native API
    if (sessionId) {
        document.cookie = 'PHPSESSID=' + sessionId
            + '; domain=' + domain
            + '; path=/; SameSite=Lax';
    }

    function goBack() {
        // Di APK: NativePHP handle WebView::goBack() via native bridge
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/dashboard';
        }
    }

    // Handle hardware back button (Android)
    document.addEventListener('backbutton', function (e) {
        e.preventDefault();
        goBack();
    });
</script>
</body>
</html>
