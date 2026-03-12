<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server EPasien
    |--------------------------------------------------------------------------
    */
    'base_url' => env('EPASIEN_BASE_URL', 'http://localhost'),
    'timeout'  => (int) env('EPASIEN_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Menu Dashboard — 5 grup, 25 item
    | act → parameter ?act= di indexuser.php
    |--------------------------------------------------------------------------
    */
    'menus' => [
        [
            'group' => 'Pelayanan',
            'items' => [
                ['label' => 'Jadwal Dokter',   'act' => 'JadwalDokter',      'icon' => '📅'],
                ['label' => 'Booking Online',  'act' => 'FormBooking',       'icon' => '📋'],
                ['label' => 'Antrian',         'act' => 'AntrianPasien',     'icon' => '🎫'],
                ['label' => 'Poliklinik',      'act' => 'DaftarPoli',        'icon' => '🏥'],
                ['label' => 'Info Tagihan',    'act' => 'InfoTagihan',       'icon' => '💳'],
            ],
        ],
        [
            'group' => 'Hasil Pemeriksaan',
            'items' => [
                ['label' => 'Hasil Lab',       'act' => 'HasilLab',          'icon' => '🧪'],
                ['label' => 'Hasil Radiologi', 'act' => 'HasilRadiologi',    'icon' => '🔬'],
                ['label' => 'Rekam Medis',     'act' => 'RekamMedis',        'icon' => '📊'],
                ['label' => 'Riwayat Periksa', 'act' => 'RiwayatPeriksa',   'icon' => '📖'],
                ['label' => 'Resep Obat',      'act' => 'ResepObat',         'icon' => '💊'],
            ],
        ],
        [
            'group' => 'Dokumen',
            'items' => [
                ['label' => 'Surat Kontrol',   'act' => 'SuratKontrol',      'icon' => '📄'],
                ['label' => 'Surat Sakit',     'act' => 'SuratSakit',        'icon' => '📝'],
                ['label' => 'Surat Rujukan',   'act' => 'SuratRujukan',      'icon' => '📨'],
                ['label' => 'Resume Medis',    'act' => 'ResumeMedis',       'icon' => '📑'],
                ['label' => 'Kartu Berobat',   'act' => 'KartuBerobat',      'icon' => '🪪'],
            ],
        ],
        [
            'group' => 'Persetujuan & Consent',
            'items' => [
                ['label' => 'Persetujuan Tindakan',  'act' => 'PersetujuanTindakan', 'icon' => '✍️'],
                ['label' => 'Penolakan Tindakan',    'act' => 'PenolakanTindakan',   'icon' => '🚫'],
                ['label' => 'Edukasi Pasien',        'act' => 'EdukasiPasien',       'icon' => '📚'],
                ['label' => 'General Consent',       'act' => 'GeneralConsent',      'icon' => '📜'],
                ['label' => 'Hak & Kewajiban',       'act' => 'HakKewajiban',        'icon' => '⚖️'],
            ],
        ],
        [
            'group' => 'Lainnya',
            'items' => [
                ['label' => 'Profil Saya',     'act' => 'ProfilPasien',      'icon' => '👤'],
                ['label' => 'Pengumuman',      'act' => 'listpengumuman',    'icon' => '📢'],
                ['label' => 'Jadwal Kontrol',  'act' => 'JadwalKontrol',     'icon' => '🗓️'],
                ['label' => 'Info RS',         'act' => 'InformasiRS',       'icon' => 'ℹ️'],
                ['label' => 'Logout',          'act' => '__logout__',        'icon' => '🚪'],
            ],
        ],
    ],

];
