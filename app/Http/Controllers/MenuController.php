<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    private const MAP = [
        'JadwalDokter'        => 'pendaftaran.jadwal',
        'FormBooking'         => 'pendaftaran.booking',
        'AntrianPasien'       => 'pendaftaran.antrian',
        'DaftarPoli'          => 'pendaftaran.poli',
        'InfoTagihan'         => 'pendaftaran.tagihan',
        'HasilLab'            => 'hasil.lab',
        'HasilRadiologi'      => 'hasil.radiologi',
        'RekamMedis'          => 'hasil.rekam-medis',
        'RiwayatPeriksa'      => 'hasil.riwayat',
        'ResepObat'           => 'hasil.resep',
        'SuratKontrol'        => 'dokumen.surat-kontrol',
        'SuratSakit'          => 'dokumen.surat-sakit',
        'SuratRujukan'        => 'dokumen.surat-rujukan',
        'ResumeMedis'         => 'dokumen.resume-medis',
        'KartuBerobat'        => 'dokumen.kartu-berobat',
        'PersetujuanTindakan' => 'consent.persetujuan',
        'PenolakanTindakan'   => 'consent.penolakan',
        'EdukasiPasien'       => 'consent.edukasi',
        'GeneralConsent'      => 'consent.general-consent',
        'HakKewajiban'        => 'consent.hak-kewajiban',
        'ProfilPasien'        => 'akun.profil',
        'listpengumuman'      => 'akun.pengumuman',
        'JadwalKontrol'       => 'akun.jadwal-kontrol',
        'InformasiRS'         => 'akun.info-rs',
    ];

    public function dispatch(Request $request, string $act)
    {
        $act = preg_replace('/[^a-zA-Z0-9_\-]/', '', $act);
        if (isset(self::MAP[$act])) {
            return redirect()->route(self::MAP[$act]);
        }
        abort(404);
    }
}
