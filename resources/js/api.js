// resources/js/api.js
(function () {

    // ── Safe DOM helper — gunakan ini SELALU, jangan innerHTML dengan data server ──
    window.el = function (tag, props, text) {
        var node = document.createElement(tag);
        if (props) Object.assign(node, props);
        if (text !== undefined) node.textContent = text;
        return node;
    };

    // ── Cache helpers ──
    window.setCachedData = function (url, data) {
        try { localStorage.setItem('cache_' + url, JSON.stringify({ data: data, ts: Date.now() })); } catch (_) {}
    };
    window.getCachedData = function (url, maxAgeMs) {
        try {
            var raw = localStorage.getItem('cache_' + url);
            if (!raw) return null;
            var p = JSON.parse(raw);
            return (Date.now() - p.ts <= maxAgeMs) ? p.data : null;
        } catch (_) { return null; }
    };

    // ── Offline banner ──
    window.showOfflineBanner = function () {
        var el = document.getElementById('offlineBanner');
        if (el) el.style.display = 'block';
    };

    // ── Toast ──
    window.showToast = function (msg, type) {
        var icons   = { success: '\u2705', error: '\u274C', info: '\u2139\uFE0F', warning: '\u26A0\uFE0F' };
        var toast   = document.getElementById('toast');
        var overlay = document.getElementById('toastOverlay');
        if (!toast) return;
        document.getElementById('toastIcon').textContent = icons[type] || '\u2755';
        document.getElementById('toastMsg').textContent  = msg;
        toast.className   = 'toast toast-show';
        overlay.className = 'toast-overlay active';
        clearTimeout(toast._t);
        toast._t = setTimeout(function () {
            toast.className   = 'toast';
            overlay.className = 'toast-overlay';
        }, type === 'success' ? 1400 : 3000);
    };

    // ── State helpers ──
    window.showLoading = function (containerId) {
        var c = document.getElementById(containerId);
        if (!c) return;
        c.textContent = '';
        var wrap = el('div', { className: 'state-loading' });
        wrap.appendChild(el('div', { className: 'spinner' }));
        wrap.appendChild(el('div', {}, 'Memuat...'));
        c.appendChild(wrap);
    };

    window.showEmpty = function (containerId, icon, msg) {
        var c = document.getElementById(containerId);
        if (!c) return;
        c.textContent = '';
        var wrap = el('div', { className: 'state-empty' });
        wrap.appendChild(el('div', { className: 'state-icon' }, icon));
        wrap.appendChild(el('div', {}, msg));
        c.appendChild(wrap);
    };

    window.showError = function (containerId, msg, retryFn) {
        var c = document.getElementById(containerId);
        if (!c) return;
        c.textContent = '';
        var wrap = el('div', { className: 'state-error' });
        wrap.appendChild(el('div', { className: 'state-icon' }, '\u274C'));
        wrap.appendChild(el('div', {}, msg));
        if (retryFn) {
            var btn = el('button', { className: 'btn btn-outline', style: 'margin-top:12px;width:auto;padding:8px 20px;' }, 'Coba Lagi');
            btn.addEventListener('click', retryFn);
            wrap.appendChild(btn);
        }
        c.appendChild(wrap);
    };

    // ── apiFetch ──
    window.apiFetch = async function (url, options) {
        options = options || {};
        try {
            var res = await fetch(url, Object.assign({}, options, {
                headers: Object.assign({
                    'X-CSRF-TOKEN': window.CSRF_TOKEN,
                    'Accept': 'application/json',
                }, options.headers || {})
            }));

            if (res.status === 401) {
                window.location.href = '/session-expired';
                return null;
            }

            if (!res.ok) {
                var msg = 'Terjadi kesalahan server';
                try { var d = await res.json(); msg = d.message || msg; } catch (_) {}
                throw new Error(msg);
            }

            return await res.json();

        } catch (err) {
            if (!navigator.onLine) {
                window.showOfflineBanner();
                return null;
            }
            throw err;
        }
    };

})();
