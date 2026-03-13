# EPasien Mobile — Native Menus Design Spec
**Tanggal:** 2026-03-13
**Status:** Revised v3
**Scope:** Rebuild semua 25 menu EPasien Mobile secara native menggunakan NativePHP Mobile v3

---

## 1. Latar Belakang & Masalah

EPasien Mobile sebelumnya menggunakan WebView (iframe) untuk menampilkan halaman EPasien server. Pendekatan ini gagal karena:

- `SameSite=Lax` pada PHPSESSID cookie memblokir cookie di konteks iframe cross-origin
- `redirect()->away()` memang memperbaiki cookie, tapi menghilangkan NativePHP top bar
- Tidak ada kontrol UI yang konsisten di semua halaman

**Solusi:** Rebuild semua 25 menu sebagai Blade views native di NativePHP, dengan Laravel sebagai API Gateway ke EPasien server.

---

## 2. Prasyarat Implementasi (WAJIB selesai sebelum development)

> **PENTING:** Semua item berikut harus ada sebelum satu pun halaman native bisa render data.

| # | Prasyarat | Keterangan |
|---|-----------|-----------|
| 1 | Buat direktori `epasien/api/v2/` di EPasien server | 34 endpoint baru harus dibuat di sini |
| 2 | Set `session.gc_maxlifetime = 28800` di `php.ini` EPasien server | Agar PHP session hidup 8 jam |
| 3 | Set `session.use_strict_mode = Off` di `php.ini` EPasien server | Agar `session_id($sid)` bisa resume session |
| 4 | SSL certificate tersedia di EPasien server | Untuk HTTPS enforcement + cert pinning |
| 5 | Migrasi tabel `sessions` untuk Laravel session driver | `php artisan session:table && php artisan migrate` — **Gunakan SQLite** (DB_CONNECTION=sqlite, standar NativePHP). Jangan diganti ke MySQL karena SQLite adalah DB lokal di device yang digunakan NativePHP. |
| 6 | Hapus file fallback plaintext di `SessionService.php` | Keamanan: PHPSESSID tidak boleh disimpan di file plaintext |
| 7 | Update `.env`: `SESSION_DRIVER=database`, `SESSION_LIFETIME=480`, `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true` | Saat ini `.env` masih menggunakan `SESSION_DRIVER=file` dan `SESSION_LIFETIME=120` |

---

## 3. Arsitektur

### Overview

```
┌─────────────────────────────────────────────────┐
│  NativePHP APK (Android)                         │
│  Blade Views → apiFetch('/api/...') via JS       │
│  Laravel Controllers (epasien-mobile)            │
│  └── EpasienApiService → HTTP ke EPasien server  │
│  └── SessionService → PHPSESSID (DB session)     │
└─────────────────────────────────────────────────┘
         │ HTTPS
         ▼
┌─────────────────────────────────────────────────┐
│  EPasien Server (PHP/MySQL)                      │
│  epasien/api/v2/*.php  ← 34 endpoint baru        │
│  epasien/api/v2/_auth.php  ← session middleware  │
│  MySQL: database sik                            │
└─────────────────────────────────────────────────┘
```

### Komponen Utama

| Layer | Lokasi | Tanggung Jawab |
|-------|--------|----------------|
| Blade Views | `resources/views/` | UI, form, tampilan data |
| Feature Controllers | `app/Http/Controllers/` | 1 controller per grup menu |
| EpasienApiService | `app/Services/EpasienApiService.php` | Semua HTTP call ke EPasien |
| SessionService | `app/Services/SessionService.php` | Kelola PHPSESSID di DB session |
| EPasien API v2 | `epasien/api/v2/` | Query DB sik, return JSON |

### Controller Groups

```
DashboardController    → Beranda, bottom navigation
PendaftaranController  → Jadwal Dokter, Booking, Antrian, Poliklinik, Tagihan
HasilController        → Lab, Radiologi, Rekam Medis, Riwayat, Resep Obat
DokumenController      → Surat Kontrol, Surat Sakit, Rujukan, Resume, Kartu
ConsentController      → Persetujuan, Penolakan, Edukasi, General Consent, Hak & Kewajiban
AkunController         → Profil, Pengumuman, Jadwal Kontrol, Info RS, Logout
```

---

## 4. Autentikasi & Keamanan

### Model Session (Pilihan Arsitektur)

**PHPSESSID disimpan di Laravel DB session — tidak pernah keluar ke client.**

```
Mobile device menyimpan: Laravel session cookie (httpOnly, Secure)
Laravel DB (tabel sessions) menyimpan: PHPSESSID EPasien
Client (APK) tidak pernah tahu nilai PHPSESSID
```

Implikasi pada `SessionService.php`:
- **Hapus** SecureStorage dan file fallback untuk PHPSESSID
- Simpan PHPSESSID di `session(['epasien_phpsessid' => $phpsessid])`
- Ambil via `session('epasien_phpsessid')`
- `pasien_data` (nama, tgl_lahir, dll — bukan credential) **boleh** tetap di SecureStorage

Konfigurasi wajib di `.env` (saat ini masih `file`/`120`, harus diubah):
```
SESSION_DRIVER=database
SESSION_LIFETIME=480
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
```

**Catatan SQLite:** `DB_CONNECTION=sqlite` adalah konfigurasi yang benar untuk NativePHP — session disimpan di SQLite lokal di device. Jangan diganti ke MySQL.

**Struktur `SessionService` setelah refactor:**
- Hapus semua SecureStorage + file fallback untuk PHPSESSID
- Simpan PHPSESSID: `session(['epasien_phpsessid' => $phpsessid])`
- Ambil PHPSESSID: `session('epasien_phpsessid')`
- Clear PHPSESSID: `session()->forget('epasien_phpsessid')`
- `pasien_data` (nama, tgl lahir — bukan credential): tetap di SecureStorage via `SecureStorage::set(self::KEY_PASIEN, json_encode($data))`
- Class `SessionService` dipertahankan sebagai thin wrapper agar controllers tidak memanggil `session()` langsung

### Validasi HTTPS

Di `AppServiceProvider::boot()` atau constructor `EpasienApiService`:
```php
if (!str_starts_with(config('epasien.base_url'), 'https://')) {
    throw new \RuntimeException('EPASIEN_BASE_URL harus menggunakan HTTPS');
}
```

### Alur Login

```
1. User input No. RM + Password
2. Rate limit Laravel: throttle:5,1 (5x/menit per IP) → 429 jika lewat
3. Brute force check: Cache 'login_attempts_'.$ip, lockout 15 menit setelah 5x gagal → 423
4. EpasienApiService POST → epasien/api/auth.php
   (EPasien auth.php juga punya rate limit sendiri via tabel mobile_rate_limit — ini lapisan ke-3)
5. EPasien return { success, phpsessid, pasien_data }
6. AuthController: session(['epasien_phpsessid' => $phpsessid])
7. pasien_data (bukan credential) → SecureStorage untuk display offline
8. Redirect ke /dashboard
```

**Catatan rate limit berlapis:**
- Lapisan 1: Laravel `throttle:5,1` middleware pada **route** `POST /login` di `routes/mobile.php` — per menit per IP
- Lapisan 2: Laravel Cache brute force di `AuthController::login()` — 5 gagal → lock 15 menit via `Cache::increment('login_attempts_'.$ip)`
- Lapisan 3: EPasien `mobile_rate_limit` tabel — 5 per 15 menit (failsafe jika Laravel bypass)

Penempatan `throttle:5,1` **harus di route definition**, bukan di constructor controller:
```php
// routes/mobile.php
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
```

Ketiga lapisan aktif bersamaan. Lapisan 1 paling ketat, lapisan 3 adalah fallback.

### Alur Request Data

```
Mobile Blade → apiFetch('/api/{endpoint}') [kirim Laravel session cookie]
Laravel AuthMiddleware → cek session valid → ambil session('epasien_phpsessid')
Laravel → POST epasien/api/v2/{endpoint}.php [header: X-Session-Id: {PHPSESSID}]
EPasien _auth.php → session_id($sid); session_start() → cek $_SESSION['ses_pasien']
EPasien endpoint.php → query DB sik → return JSON
Laravel → forward JSON ke Blade
```

### EPasien API v2 Auth Middleware

File `epasien/api/v2/_auth.php` — di-include di setiap endpoint v2:

```php
<?php
// Wajib: session.use_strict_mode = Off di php.ini
// Wajib: session.gc_maxlifetime = 28800 di php.ini
//
// TRADEOFF KEAMANAN: session.use_strict_mode=Off memungkinkan
// session fixation (klien supply ID sembarang). Mitigasi:
// (1) sanitasi ketat pada $sid di bawah
// (2) session_regenerate_id() dipanggil di auth.php setelah login sukses

header('Content-Type: application/json');

$sid = $_SERVER['HTTP_X_SESSION_ID'] ?? '';
$sid = preg_replace('/[^a-zA-Z0-9]/', '', $sid); // sanitasi: hanya alfanumerik

if ($sid === '') {
    http_response_code(401);
    echo json_encode(['error' => 'session_required']);
    exit();
}

session_id($sid);
session_start();

if (empty($_SESSION['ses_pasien'])) {
    http_response_code(401);
    echo json_encode(['error' => 'session_expired']);
    exit();
}

$pasien = $_SESSION['ses_pasien']; // array data pasien aktif
```

**Wajib tambahkan di `epasien/api/auth.php` setelah login sukses:**
```php
session_regenerate_id(true); // cegah session fixation
$newSid = session_id();
// return $newSid ke Laravel (bukan ID lama)
```

### Session Expired Flow

```
EPasien return 401
→ EpasienApiService throw SessionExpiredException
→ Laravel Controller catch → clear session → return 401 JSON
→ apiFetch() detect 401 → window.location.href = '/session-expired'
→ SessionExpiredController: session()->flush() → redirect /login
→ Blade login: flash message "Sesi Anda berakhir, silakan login kembali"
```

### Keepalive Session

EPasien PHP session default 24 menit (1440 detik). Untuk match target 8 jam:
- Set `session.gc_maxlifetime = 28800` di EPasien server `php.ini`
- NativePHP app kirim keepalive `GET /api/ping` setiap 10 menit saat app aktif

### Keamanan Tambahan

| Fitur | Implementasi | Status |
|-------|-------------|--------|
| Rate limiting login | `throttle:5,1` di route `POST /login` dalam `routes/mobile.php` | Harus ditambah |
| Brute force lockout | `Cache::increment('login_attempts_'.$ip)` di `AuthController` | Harus ditambah |
| PHPSESSID server-side | Refactor `SessionService` → DB session (lihat Section 4) | Harus direfactor |
| HTTPS enforcement | `AppServiceProvider::boot()`: cek `str_starts_with(base_url, 'https://')` | Harus ditambah |
| Request timeout | `timeout(30)` di `EpasienApiService` | Sudah ada, verifikasi |
| Session lifetime 8 jam | `SESSION_LIFETIME=480` di `.env` + `gc_maxlifetime=28800` di `php.ini` | Konfigurasi wajib |
| Session fixation | `session_regenerate_id(true)` di `auth.php` setelah login sukses | Harus ditambah |
| Cert pinning | NativePHP `nativephp.php` config | Research API NativePHP v3 dulu |

---

## 5. 25 Menu — API Endpoints & Blade Views

### Total: 34 EPasien PHP endpoint baru + 24 Blade views

Penjelasan selisih 34 vs 25: beberapa menu memerlukan >1 endpoint (contoh: Lab = list + detail = 2 endpoint, Booking = poli-list + POST booking = 2 endpoint, dst).

### Grup 1: Pelayanan

| Menu | Laravel Route | EPasien v2 Endpoint | Blade View | Kompleksitas |
|------|--------------|---------------------|------------|--------------|
| Jadwal Dokter | `GET /api/jadwal-dokter` | `v2/jadwal-dokter.php` | list + filter poli | Medium |
| Booking Online | `GET /api/poli-list`, `POST /api/booking` | `v2/poli-list.php`, `v2/booking.php` | form multi-step | Complex |
| Antrian | `GET /api/antrian` | `v2/antrian.php` | card antrian | Medium |
| Poliklinik | `GET /api/poli-list` | (share dengan Booking) | list card | Simple |
| Info Tagihan | `GET /api/tagihan` | `v2/tagihan.php` | tabel rincian | Medium |

### Grup 2: Hasil Pemeriksaan

| Menu | Laravel Route | EPasien v2 Endpoint | Blade View | Kompleksitas |
|------|--------------|---------------------|------------|--------------|
| Hasil Lab | `GET /api/lab/list`, `GET /api/lab/{id}` | `v2/lab-list.php`, `v2/lab-detail.php` | list → detail | Medium |
| Hasil Radiologi | `GET /api/radiologi/list`, `GET /api/radiologi/{id}` | `v2/radiologi-list.php`, `v2/radiologi-detail.php` | list → foto | Complex |
| Rekam Medis | `GET /api/rekam-medis` | `v2/rekam-medis.php` | timeline | Medium |
| Riwayat Periksa | `GET /api/riwayat-periksa` | `v2/riwayat-periksa.php` | list | Simple |
| Resep Obat | `GET /api/resep` | `v2/resep.php` | list obat | Simple |

### Grup 3: Dokumen

| Menu | Laravel Route | EPasien v2 Endpoint | Blade View | Kompleksitas |
|------|--------------|---------------------|------------|--------------|
| Surat Kontrol | `GET /api/surat-kontrol` | `v2/surat-kontrol.php` | card surat | Simple |
| Surat Sakit | `GET /api/surat-sakit` | `v2/surat-sakit.php` | card surat | Simple |
| Surat Rujukan | `GET /api/surat-rujukan` | `v2/surat-rujukan.php` | card surat | Simple |
| Resume Medis | `GET /api/resume-medis` | `v2/resume-medis.php` | detail medis | Medium |
| Kartu Berobat | `GET /api/kartu-berobat` | `v2/kartu-berobat.php` | kartu digital | Medium |

### Grup 4: Persetujuan & Consent

| Menu | Laravel Route | EPasien v2 Endpoint | Blade View | Kompleksitas |
|------|--------------|---------------------|------------|--------------|
| Persetujuan Tindakan | `GET /api/consent/list`, `POST /api/consent/sign` | `v2/consent-list.php`, `v2/consent-sign.php` | list + canvas TTD | Complex |
| Penolakan Tindakan | `GET /api/penolakan/list`, `POST /api/penolakan/sign` | `v2/penolakan-list.php`, `v2/penolakan-sign.php` | list + canvas TTD | Complex |
| Edukasi Pasien | `GET /api/edukasi` | `v2/edukasi.php` | list artikel | Simple |
| General Consent | `GET /api/general-consent`, `POST /api/general-consent/sign` | `v2/general-consent.php`, `v2/general-consent-sign.php` | form + canvas TTD | Complex |
| Hak & Kewajiban | `GET /api/hak-kewajiban` | `v2/hak-kewajiban.php` | teks statis | Simple |

### Grup 5: Lainnya

| Menu | Laravel Route | EPasien v2 Endpoint | Blade View | Kompleksitas |
|------|--------------|---------------------|------------|--------------|
| Profil Saya | `GET /api/profil`, `POST /api/profil` | `v2/profil-get.php`, `v2/profil-update.php` | form edit | Medium |
| Pengumuman | `GET /api/pengumuman` | `v2/pengumuman.php` | list card | Simple |
| Jadwal Kontrol | `GET /api/jadwal-kontrol` | `v2/jadwal-kontrol.php` | list | Simple |
| Info RS | `GET /api/info-rs` | `v2/info-rs.php` | halaman info | Simple |
| Logout | `POST /auth/logout` | — | clear session + redirect | — |

**Catatan Profil:** Menggunakan `POST /api/profil` (bukan PUT) untuk kompatibilitas penuh dengan Blade form tanpa JS dependency. Tidak diperlukan `@method('PUT')` spoofing.

---

## 6. Foto Radiologi — Proxy Route

Foto radiologi disimpan di EPasien server. Untuk menghindari cross-origin image request:

```
GET /api/radiologi/foto/{filename}
→ RadiologyProxyController::image($filename)
→ Validasi session aktif
→ Validasi filename: hanya karakter aman (preg_replace)
→ EpasienApiService::streamImage($filename)
→ HTTP GET ke epasien/api/v2/radiologi-foto.php?file={filename}
→ Pipe binary response ke client
→ Forward Content-Type dari response EPasien (jangan hardcode)
→ Cache-Control: private, max-age=300 (5 menit)
```

Endpoint `v2/radiologi-foto.php` di EPasien: include `_auth.php`, baca file dari path konfigurasi RS, stream binary dengan `Content-Type` asli file.

**Content-Type detection di Laravel proxy:**
```php
$response = Http::withHeaders([...])->get($epasienUrl . '/v2/radiologi-foto.php?file=' . $filename);
$contentType = $response->header('Content-Type') ?? 'image/jpeg';
return response($response->body(), 200)->header('Content-Type', $contentType);
```

**Scope foto:** Hanya JPEG dan PNG yang di-support. DICOM **di luar scope** — jika RS menyimpan file DICOM, tampilkan pesan "Format tidak didukung, hubungi RS".

---

## 7. Tanda Tangan Digital (Consent)

Tiga menu memerlukan tanda tangan digital: Persetujuan Tindakan, Penolakan Tindakan, General Consent.

**Format submit:**
```json
{
    "no_reg": "2024-001234",
    "tanda_tangan": "data:image/png;base64,iVBOR...",
    "timestamp": "2026-03-13T10:30:00Z",
    "confirmed": true
}
```

**Validasi minimum canvas:**
- Minimum 20 stroke points (tidak boleh kosong/satu titik saja)
- Ukuran canvas: 100% width × 200px height
- Tombol "Hapus" untuk reset canvas

**EPasien endpoint** menerima base64 PNG, simpan ke storage path RS, catat di tabel consent/persetujuan sesuai modul SIMRS Khanza.

---

## 8. UI Design System

### Design Tokens

```css
--color-primary:      #0B6EFD;
--color-primary-soft: #EBF3FF;
--color-success:      #16A34A;
--color-danger:       #DC2626;
--color-warning:      #D97706;
--color-text:         #1E293B;
--color-text-muted:   #64748B;
--color-bg:           #F8FAFC;
--color-card:         #FFFFFF;
--color-border:       #E2E8F0;
--radius-sm: 8px; --radius-md: 12px; --radius-lg: 16px;
--spacing-page: 16px;
```

### Komponen Reusable

1. **Top Bar** — tombol back + judul + opsional menu konteks
2. **Card Data** — icon + label + nilai + chevron kanan
3. **Status Badge** — hijau/kuning/merah sesuai status
4. **Form Input** — border muted, focus ring primary, inline error di bawah field
5. **Bottom Navigation** — 5 tab dengan route native baru:
   - Beranda → `/dashboard`
   - Daftar → `/pendaftaran` (Jadwal, Booking, Antrian)
   - Hasil → `/hasil` (Lab, Radiologi, Rekam Medis)
   - Dokumen → `/dokumen`
   - Akun → `/akun` (Profil, Logout)
6. **Loading / Empty / Error State** — spinner, ilustrasi kosong, tombol "Coba Lagi"
7. **Toast Notification** — tengah layar, overlay semi-transparan, auto-dismiss

**Catatan:** Bottom nav yang ada di `dashboard.blade.php` (4 tab lama → WebView routes) akan di-replace seluruhnya dengan 5 tab native baru.

### Struktur CSS

```
resources/css/
├── app.css          ← import semua
├── tokens.css       ← design tokens
├── components.css   ← card, badge, form, topbar, toast
├── layout.css       ← bottom nav, page wrapper
└── pages/           ← override per halaman jika perlu
```

---

## 9. Error Handling & Offline Strategy

### Error Handling Matrix

| Skenario | HTTP Code | Tampilan |
|----------|-----------|----------|
| Session expired | 401 | Redirect login + pesan flash |
| Brute force lockout | 423 | "Akun terkunci 15 menit" |
| Rate limit | 429 | "Terlalu banyak percobaan, tunggu sebentar" |
| Server EPasien down | 503/timeout | Card error + tombol "Coba Lagi" |
| No internet | — | Banner offline + data cache (jika ada) |
| Data tidak ditemukan | 404 | Empty state + ilustrasi |
| Validasi form | 422 | Inline error per field |
| Server error | 500 | "Terjadi kesalahan, coba lagi" |

### Global Fetch Wrapper

File `resources/js/api.js` — di-include semua Blade views via layout master.

**Setup `window.CSRF_TOKEN`** di layout master Blade (`resources/views/layouts/app.blade.php`):
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>window.CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');</script>
```

```javascript
// Simpan response di localStorage untuk cache offline
function setCachedData(url, data) {
    localStorage.setItem('cache_' + url, JSON.stringify({ data, ts: Date.now() }));
}

function getCachedData(url, maxAgeMs) {
    var raw = localStorage.getItem('cache_' + url);
    if (!raw) return null;
    var parsed = JSON.parse(raw);
    if (Date.now() - parsed.ts > maxAgeMs) return null;
    return parsed.data;
}

function showOfflineBanner() {
    var el = document.getElementById('offlineBanner');
    if (el) el.style.display = 'block';
}

async function apiFetch(url, options = {}) {
    try {
        const res = await fetch(url, {
            ...options,
            headers: {
                'X-CSRF-TOKEN': window.CSRF_TOKEN,
                'Accept': 'application/json',
                ...(options.headers || {})
            }
        });

        if (res.status === 401) {
            window.location.href = '/session-expired';
            return null;
        }

        if (!res.ok) {
            // Coba parse JSON, fallback ke text jika gagal
            let msg = 'Terjadi kesalahan server';
            try {
                const data = await res.json();
                msg = data.message || msg;
            } catch (_) {}
            throw new Error(msg);
        }

        const data = await res.json();
        return data;

    } catch (err) {
        if (!navigator.onLine) {
            showOfflineBanner();
            return null; // caller bertanggung jawab cek cache
        }
        throw err;
    }
}
```

### Cache Strategy

| Data | Di-cache? | Durasi | Catatan |
|------|-----------|--------|---------|
| Info RS, Hak & Kewajiban | Ya | 24 jam | Data statis |
| Jadwal Dokter, Poli List | Ya | 1 jam | Berubah harian |
| Profil Pasien | Ya | 30 menit | Non-sensitif |
| Hasil Lab, Radiologi | **Tidak** | — | Data medis sensitif |
| Tagihan, Consent | **Tidak** | — | Wajib real-time |

### Retry Mechanism

- Auto-retry 1x setelah 3 detik untuk timeout (non-sensitif)
- Tombol "Coba Lagi" manual di semua error state
- Pull-to-refresh di semua halaman list (touch event swipe down)

---

## 10. Implementasi Bertahap

### Chunk 1 — Foundation (Auth + Dashboard)
- Update `.env`: `SESSION_DRIVER=database`, `SESSION_LIFETIME=480`, `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`
- Jalankan `php artisan session:table && php artisan migrate` (SQLite)
- Refactor `SessionService`: hapus SecureStorage + file fallback untuk PHPSESSID → ganti ke `session()`. Pertahankan SecureStorage hanya untuk `pasien_data`
- Tambah `throttle:5,1` middleware ke route `POST /login` di `routes/mobile.php`
- Tambah brute force lockout di `AuthController::login()` via `Cache::increment`
- Tambah `session_regenerate_id(true)` di `epasien/api/auth.php` setelah login sukses
- Tambah HTTPS validation di `AppServiceProvider::boot()`
- Buat `resources/views/layouts/app.blade.php` dengan `window.CSRF_TOKEN` setup
- Buat `resources/js/api.js` (global fetch wrapper + cache helpers + offline banner)
- Buat design tokens + komponen CSS (`tokens.css`, `components.css`, `layout.css`)
- **Replace** bottom nav di `dashboard.blade.php`: hapus 4-tab WebView lama, implement 5-tab native: Beranda(`/dashboard`), Daftar(`/pendaftaran`), Hasil(`/hasil`), Dokumen(`/dokumen`), Akun(`/akun`)
- Buat `DashboardController` dengan route baru

### Chunk 2 — Pelayanan
- Buat `epasien/api/v2/_auth.php`
- `PendaftaranController` + 5 Blade views
- EPasien endpoints: jadwal-dokter, poli-list, antrian, tagihan, booking

### Chunk 3 — Hasil Pemeriksaan
- `HasilController` + 5 Blade views
- EPasien endpoints: riwayat, resep, rekam-medis, lab-list/detail, radiologi-list/detail/foto
- `RadiologyProxyController` untuk stream foto

### Chunk 4 — Dokumen
- `DokumenController` + 5 Blade views
- EPasien endpoints: surat-kontrol, surat-sakit, surat-rujukan, resume-medis, kartu-berobat

### Chunk 5 — Consent & Lainnya
- `ConsentController` + 5 Blade views (termasuk canvas signature)
- `AkunController` + 4 Blade views
- EPasien endpoints: edukasi, hak-kewajiban, consent/penolakan/general-consent, profil, pengumuman, jadwal-kontrol, info-rs

---

## 11. Asumsi & Batasan

- EPasien server menggunakan PHP session (`$_SESSION['ses_pasien']`)
- `session.use_strict_mode = Off` diset di EPasien server `php.ini`
- `session.gc_maxlifetime = 28800` diset di EPasien server `php.ini`
- Database `sik` diakses dari EPasien PHP (bukan dari Laravel langsung)
- NativePHP Mobile v3 mendukung `httpOnly` + `Secure` cookie untuk Laravel session
- Tanda tangan digital: canvas touch → base64 PNG, simpan ke storage RS
- Foto radiologi di-proxy Laravel, bukan langsung dari client ke EPasien server
- Tidak ada push notification dalam scope ini
- Certificate pinning: dikonfigurasi jika NativePHP v3 mendukung API-nya (perlu riset)
