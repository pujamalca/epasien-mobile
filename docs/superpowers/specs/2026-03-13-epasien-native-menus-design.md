# EPasien Mobile — Native Menus Design Spec
**Tanggal:** 2026-03-13
**Status:** Draft
**Scope:** Rebuild semua 25 menu EPasien Mobile secara native menggunakan NativePHP Mobile v3

---

## 1. Latar Belakang & Masalah

EPasien Mobile sebelumnya menggunakan WebView (iframe) untuk menampilkan halaman EPasien server. Pendekatan ini gagal karena:

- `SameSite=Lax` pada PHPSESSID cookie memblokir cookie di konteks iframe cross-origin
- `redirect()->away()` memang memperbaiki cookie, tapi menghilangkan NativePHP top bar
- Tidak ada kontrol UI yang konsisten di semua halaman

**Solusi:** Rebuild semua 25 menu sebagai Blade views native di NativePHP, dengan Laravel sebagai API Gateway ke EPasien server.

---

## 2. Arsitektur

### Overview

```
┌─────────────────────────────────────────────────┐
│  NativePHP APK (Android)                         │
│  Blade Views → fetch('/api/...') via apiFetch()  │
│  Laravel Controllers (epasien-mobile)            │
│  └── EpasienApiService → HTTP ke EPasien server  │
│  └── SessionService → PHPSESSID (server-side)    │
└─────────────────────────────────────────────────┘
         │ HTTPS
         ▼
┌─────────────────────────────────────────────────┐
│  EPasien Server (PHP/MySQL)                      │
│  epasien/api/v2/*.php  ← endpoint baru           │
│  MySQL: database sik                            │
└─────────────────────────────────────────────────┘
```

### Komponen Utama

| Layer | Lokasi | Tanggung Jawab |
|-------|--------|----------------|
| Blade Views | `resources/views/` | UI, form, tampilan data |
| Feature Controllers | `app/Http/Controllers/` | 1 controller per grup menu |
| EpasienApiService | `app/Services/EpasienApiService.php` | Semua HTTP call ke EPasien |
| SessionService | `app/Services/SessionService.php` | Kelola PHPSESSID server-side |
| EPasien API v2 | `epasien/api/v2/` | Query DB, return JSON |

### Controller Groups

```
DashboardController   → Beranda, navigasi utama
PendaftaranController → Jadwal Dokter, Booking, Antrian, Poliklinik, Tagihan
HasilController       → Lab, Radiologi, Rekam Medis, Riwayat, Resep Obat
DokumenController     → Surat Kontrol, Surat Sakit, Rujukan, Resume, Kartu
ConsentController     → Persetujuan, Penolakan, Edukasi, General Consent, Hak & Kewajiban
AkunController        → Profil, Pengumuman, Jadwal Kontrol, Info RS, Logout
```

---

## 3. Autentikasi & Keamanan

### Alur Login

```
1. User input No. RM + Password
2. Rate limit: throttle:5,1 (max 5x/menit per IP)
3. Brute force check: Cache login_attempts_{ip}, lockout 15 menit setelah 5x gagal
4. EpasienApiService POST → epasien/api/auth.php
5. EPasien return { success, phpsessid, pasien_data }
6. PHPSESSID disimpan di Laravel server-side session (TIDAK dikirim ke client)
7. pasien_data → SecureStorage (Android Keystore)
8. Mobile hanya terima Laravel Session Cookie (httpOnly)
```

### Prinsip Keamanan

- **PHPSESSID tidak pernah menyentuh client** — hanya ada di server Laravel
- **Rate limiting** — `throttle:5,1` middleware di `/auth/login`
- **Brute force lockout** — 5x gagal → lock 15 menit via Laravel Cache
- **Certificate pinning** — NativePHP config: trust only cert RS
- **Request timeout** — 30 detik untuk semua HTTP call
- **HTTPS only** — validasi `epasien.base_url` wajib `https://`
- **Session lifetime** — 8 jam, auto-clear on close

### Alur Request Data

```
Mobile → GET /api/{endpoint} (Laravel session cookie)
Laravel → ambil PHPSESSID dari server-side session
Laravel → POST epasien/api/v2/{endpoint}.php (X-Session-Id: PHPSESSID)
EPasien → validasi session → query DB → return JSON
Laravel → return JSON ke Mobile Blade
```

### EPasien API v2 Auth Middleware

Semua endpoint `epasien/api/v2/` include `_auth.php`:

```php
// epasien/api/v2/_auth.php
$sid = $_SERVER['HTTP_X_SESSION_ID'] ?? '';
session_id($sid);
session_start();
if (empty($_SESSION['ses_pasien'])) {
    http_response_code(401);
    echo json_encode(['error' => 'session_expired']);
    exit();
}
```

### Session Expired Flow

```
EPasien return 401 → Laravel detect → clear server session
→ Return 401 ke mobile → apiFetch() intercept → redirect /session-expired
→ Blade pesan "Sesi berakhir, silakan login kembali" → redirect /login
```

---

## 4. 25 Menu — API Endpoints & Blade Views

### Grup 1: Pelayanan

| Menu | Laravel Route | EPasien Endpoint | Blade View | Kompleksitas |
|------|--------------|-----------------|------------|--------------|
| Jadwal Dokter | `GET /api/jadwal-dokter` | `v2/jadwal-dokter.php` | list + filter poli | Medium |
| Booking Online | `GET /api/poli-list`, `POST /api/booking` | `v2/poli-list.php`, `v2/booking.php` | form multi-step | Complex |
| Antrian | `GET /api/antrian` | `v2/antrian.php` | card antrian | Medium |
| Poliklinik | `GET /api/poli-list` | `v2/poli-list.php` | list card | Simple |
| Info Tagihan | `GET /api/tagihan` | `v2/tagihan.php` | tabel rincian | Medium |

### Grup 2: Hasil Pemeriksaan

| Menu | Laravel Route | EPasien Endpoint | Blade View | Kompleksitas |
|------|--------------|-----------------|------------|--------------|
| Hasil Lab | `GET /api/lab/list`, `GET /api/lab/{id}` | `v2/lab-list.php`, `v2/lab-detail.php` | list → detail | Medium |
| Hasil Radiologi | `GET /api/radiologi/list`, `GET /api/radiologi/{id}` | `v2/radiologi-list.php`, `v2/radiologi-detail.php` | list → foto | Complex |
| Rekam Medis | `GET /api/rekam-medis` | `v2/rekam-medis.php` | timeline | Medium |
| Riwayat Periksa | `GET /api/riwayat-periksa` | `v2/riwayat-periksa.php` | list | Simple |
| Resep Obat | `GET /api/resep` | `v2/resep.php` | list obat | Simple |

### Grup 3: Dokumen

| Menu | Laravel Route | EPasien Endpoint | Blade View | Kompleksitas |
|------|--------------|-----------------|------------|--------------|
| Surat Kontrol | `GET /api/surat-kontrol` | `v2/surat-kontrol.php` | card surat | Simple |
| Surat Sakit | `GET /api/surat-sakit` | `v2/surat-sakit.php` | card surat | Simple |
| Surat Rujukan | `GET /api/surat-rujukan` | `v2/surat-rujukan.php` | card surat | Simple |
| Resume Medis | `GET /api/resume-medis` | `v2/resume-medis.php` | detail medis | Medium |
| Kartu Berobat | `GET /api/kartu-berobat` | `v2/kartu-berobat.php` | kartu digital | Medium |

### Grup 4: Persetujuan & Consent

| Menu | Laravel Route | EPasien Endpoint | Blade View | Kompleksitas |
|------|--------------|-----------------|------------|--------------|
| Persetujuan Tindakan | `GET /api/consent/list`, `POST /api/consent/sign` | `v2/consent-list.php`, `v2/consent-sign.php` | list + tanda tangan | Complex |
| Penolakan Tindakan | `GET /api/penolakan/list`, `POST /api/penolakan/sign` | `v2/penolakan-list.php`, `v2/penolakan-sign.php` | list + form | Complex |
| Edukasi Pasien | `GET /api/edukasi` | `v2/edukasi.php` | list artikel | Simple |
| General Consent | `GET /api/general-consent`, `POST /api/general-consent/sign` | `v2/general-consent.php` | form + tanda tangan | Complex |
| Hak & Kewajiban | `GET /api/hak-kewajiban` | `v2/hak-kewajiban.php` | teks statis | Simple |

### Grup 5: Lainnya

| Menu | Laravel Route | EPasien Endpoint | Blade View | Kompleksitas |
|------|--------------|-----------------|------------|--------------|
| Profil Saya | `GET /api/profil`, `PUT /api/profil` | `v2/profil.php` | form edit | Medium |
| Pengumuman | `GET /api/pengumuman` | `v2/pengumuman.php` | list card | Simple |
| Jadwal Kontrol | `GET /api/jadwal-kontrol` | `v2/jadwal-kontrol.php` | list | Simple |
| Info RS | `GET /api/info-rs` | `v2/info-rs.php` | halaman info | Simple |
| Logout | `POST /auth/logout` | — | clear + redirect | — |

**Total: 34 EPasien PHP endpoint baru + 24 Blade views**

---

## 5. UI Design System

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
--radius-sm: 8px;   --radius-md: 12px;   --radius-lg: 16px;
--spacing-page: 16px;
```

### Komponen Reusable

1. **Top Bar** — tombol back + judul + opsional menu konteks
2. **Card Data** — icon + label + nilai + chevron
3. **Status Badge** — hijau/kuning/merah sesuai status
4. **Form Input** — border muted, focus ring primary, inline error
5. **Bottom Navigation** — 5 tab: Beranda, Daftar, Hasil, Dokumen, Akun
6. **Loading/Empty/Error State** — spinner, ilustrasi, tombol retry
7. **Toast Notification** — tengah layar, overlay, auto-dismiss

### Struktur CSS

```
resources/css/
├── app.css          ← import semua
├── tokens.css       ← design tokens
├── components.css   ← komponen reusable
├── layout.css       ← bottom nav, page wrapper
└── pages/           ← style per halaman (jika perlu override)
```

---

## 6. Error Handling & Offline Strategy

### Error Handling Matrix

| Skenario | HTTP Code | Tampilan |
|----------|-----------|----------|
| Session expired | 401 | Redirect login + pesan |
| Brute force lockout | 423 | "Akun terkunci 15 menit" |
| Rate limit | 429 | "Terlalu banyak percobaan" |
| Server EPasien down | 503/timeout | Card error + retry |
| No internet | — | Banner offline + cache |
| Data tidak ditemukan | 404 | Empty state |
| Validasi form | 422 | Inline error per field |
| Server error | 500 | "Terjadi kesalahan, coba lagi" |

### Offline / Cache Strategy

| Data | Di-cache? | Durasi |
|------|-----------|--------|
| Info RS, Hak & Kewajiban | Ya | 24 jam |
| Jadwal Dokter, Poli List | Ya | 1 jam |
| Profil Pasien | Ya | 30 menit |
| Hasil Lab, Radiologi | Tidak | — |
| Tagihan, Consent | Tidak | — |

### Global Fetch Wrapper

File `resources/js/api.js` di-include semua Blade views:

```javascript
async function apiFetch(url, options = {}) {
    try {
        const res = await fetch(url, {
            ...options,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                ...options.headers
            }
        });
        if (res.status === 401) {
            window.location.href = '/session-expired';
            return;
        }
        if (!res.ok) {
            const data = await res.json();
            throw new Error(data.message || 'Terjadi kesalahan server');
        }
        return res.json();
    } catch (err) {
        if (!navigator.onLine) {
            showOfflineBanner();
            return getCachedData(url);
        }
        throw err;
    }
}
```

### Retry Mechanism

- Auto-retry 1x setelah 3 detik jika timeout (non-sensitif)
- Tombol "Coba Lagi" manual di semua error state
- Pull-to-refresh di semua halaman list

---

## 7. Implementasi Bertahap

### Chunk 1 — Foundation (Auth + Dashboard)
- Perkuat auth: rate limit, brute force, server-side session
- Dashboard dengan bottom navigation
- Profil Saya

### Chunk 2 — Pelayanan
- Jadwal Dokter, Poliklinik, Antrian, Info Tagihan
- Booking Online (form multi-step)

### Chunk 3 — Hasil Pemeriksaan
- Riwayat Periksa, Resep Obat (simple)
- Rekam Medis (timeline)
- Hasil Lab (list + detail)
- Hasil Radiologi (dengan foto)

### Chunk 4 — Dokumen
- Surat Kontrol, Sakit, Rujukan (simple)
- Resume Medis, Kartu Berobat (medium)

### Chunk 5 — Consent & Lainnya
- Edukasi, Hak & Kewajiban (simple)
- Persetujuan, Penolakan, General Consent (complex, tanda tangan)
- Pengumuman, Jadwal Kontrol, Info RS, Logout

---

## 8. Asumsi & Batasan

- EPasien server menggunakan PHP session (`$_SESSION['ses_pasien']`)
- Database `sik` dapat diquery langsung dari EPasien API v2
- NativePHP Mobile v3 mendukung `httpOnly` cookie untuk Laravel session
- Tanda tangan digital menggunakan canvas touch (tidak perlu hardware signature pad)
- Foto radiologi diambil via URL yang di-proxy oleh Laravel (tidak langsung dari EPasien server)
- Tidak ada push notification dalam scope ini
