# EPasien Mobile

Aplikasi mobile pasien berbasis Android yang dibangun dengan **Laravel** + **NativePHP**. Aplikasi ini menghubungkan pasien dengan data medis mereka langsung dari sistem SIMRS Khanza yang berjalan di rumah sakit atau klinik.

---

## Fitur

### Autentikasi
- Login menggunakan No. Rekam Medis + password
- Session terenkripsi (AES-256)
- Rate limiting login (5 percobaan / 15 menit)
- Migrasi password otomatis dari AES (SIMRS lama) ke bcrypt

### Pendaftaran
- **Jadwal Dokter** — lihat jadwal praktek dokter per poliklinik
- **Antrian** — cek nomor antrian hari ini
- **Daftar Poliklinik** — lihat poli/unit yang tersedia
- **Booking Online** — daftar antrian dari HP untuk tanggal ke depan
- **Tagihan** — lihat rincian tagihan kunjungan

### Hasil Medis
- **Riwayat Periksa** — histori kunjungan pasien
- **Resep** — daftar obat yang diresepkan
- **Rekam Medis** — catatan medis per kunjungan
- **Hasil Laboratorium** — hasil pemeriksaan lab beserta nilai normal
- **Hasil Radiologi** — laporan + foto/PDF hasil radiologi

### Dokumen
- **Surat Kontrol** — surat jadwal kontrol berikutnya
- **Surat Sakit** — surat keterangan sakit
- **Surat Rujukan** — surat rujukan ke RS lain
- **Resume Medis** — ringkasan medis pasien
- **Kartu Berobat** — kartu identitas pasien digital

### Informed Consent (Digital)
- **Persetujuan Tindakan** — tanda tangan digital persetujuan medis
- **Penolakan Tindakan** — tanda tangan digital penolakan medis
- **General Consent** — persetujuan umum pelayanan RS
- **Edukasi Pasien** — materi edukasi dari RS
- **Hak & Kewajiban** — informasi hak dan kewajiban pasien

### Akun
- Lihat dan update profil (alamat, no. HP)
- Ganti password
- Pengumuman dari RS
- Jadwal kontrol berikutnya
- Informasi kontak & jam operasional RS

### Lainnya
- **Notifikasi** — push notification via Firebase FCM
- **Offline page** — halaman fallback saat tidak ada koneksi
- **Deep link** — navigasi langsung via `epasien://act/X`

---

## Arsitektur

```
[Android Device]
  └─ NativePHP menjalankan Laravel app (http://127.0.0.1)
       └─ HTTP calls ──► [Server RS] epasien/api/v2/ (PHP native + MySQL SIMRS Khanza)
```

- **epasien-mobile** — frontend Laravel (Blade views, Controllers sebagai proxy)
- **epasien** — backend PHP native (API v2, langsung konek ke MySQL SIMRS Khanza)

---

## Persyaratan

| Komponen | Versi |
|----------|-------|
| PHP | >= 8.1 |
| Laravel | 10.x |
| NativePHP for Android | latest |
| JDK | 17 (Eclipse Temurin direkomendasikan) |
| Android SDK | API 33+ |
| 7-Zip | untuk build APK |
| SIMRS Khanza | backend wajib ada |

---

## Instalasi & Konfigurasi

### 1. Clone & install dependensi

```bash
git clone <repo-url> epasien-mobile
cd epasien-mobile
composer install
npm install
```

### 2. Konfigurasi `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` sesuaikan:

```env
APP_ENV=production
APP_DEBUG=false

# URL server SIMRS tempat epasien/api/v2/ berjalan
EPASIEN_BASE_URL=http://IP_SERVER_RS

# NativePHP Android
NATIVEPHP_APP_ID=com.namaorganisasi.epasien
NATIVEPHP_APP_VERSION=1.0.0
NATIVEPHP_GRADLE_PATH="path/ke/jdk-17"
NATIVEPHP_ANDROID_SDK_LOCATION="path/ke/android-sdk"

# Keystore untuk signing APK release
ANDROID_KEYSTORE_PATH=nama-release.keystore
ANDROID_KEYSTORE_PASSWORD=password_kuat
ANDROID_KEY_ALIAS=nama_alias
ANDROID_KEY_PASSWORD=password_kuat
```

### 3. Build APK

```bash
php artisan native:build android
```

---

## Backend (epasien)

Aplikasi ini membutuhkan backend **epasien** (PHP native) yang terinstal di server dengan SIMRS Khanza. Lihat repositori `epasien` untuk panduan instalasi backend.

---

## Lisensi & Kredit

**EPasien Mobile** adalah perangkat lunak bebas dan open source.

- Bebas digunakan, dimodifikasi, dan didistribusikan
- **Dilarang keras diperjualbelikan** dalam bentuk apapun, baik source code maupun APK
- Jika dimodifikasi atau didistribusikan ulang, tetap cantumkan kredit asli

---

## Dukungan

Jika aplikasi ini bermanfaat bagi Anda, Anda bisa memberikan dukungan melalui:

**Transfer Bank BSI**
- Atas Nama: **Puja M Alca**
- No. Rekening: **7190075731**

Setiap dukungan sangat berarti dan membantu pengembangan aplikasi ini. Terima kasih!

---

## Tech Stack

- [Laravel 10](https://laravel.com)
- [NativePHP for Android](https://nativephp.com)
- [SIMRS Khanza](https://github.com/mas-elkhanza/SIMRS-Khanza) (backend)
- Vanilla JS + Blade Templates
- Firebase Cloud Messaging (notifikasi)
