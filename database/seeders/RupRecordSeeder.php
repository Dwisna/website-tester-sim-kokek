<?php

namespace Database\Seeders;

use App\Models\RupRecord;
use Illuminate\Database\Seeder;

class RupRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RupRecord::insert([
            [
                'id_rup' => 'RUP-001',
                'nama_pekerjaan' => 'Pengadaan Server dan Infrastruktur',
                'pagu' => '1.250.000.000',
                'nama_jenis_pengadaan' => 'Barang',
                'nama_jenis_produk_rup' => 'IT Infrastruktur',
                'nama_jenis_usaha' => 'Perusahaan Teknologi',
                'nama_metode_pengadaan' => 'Tender',
                'waktu_pemilihan_penyedia' => '2026-07-20',
                'nama_instansi' => 'Kementerian Kominfo',
                'nama_organisasi' => 'Direktorat Jenderal Aplikasi Informatika',
                'lokasi_pekerjaan' => 'Jakarta Pusat',
                'nama_bidang_pekerjaan' => 'Teknologi Informasi',
                'tahun_anggaran' => '2026',
                'pic' => 'Andi Pratama',
                'keterangan' => 'Data demo untuk dashboard RUP Intelligence.',
                'is_sirup' => 1,
                'is_import' => 0,
                'is_pekerjaan_prospek' => 1,
                'id_sis_rup' => 'SIS-001',
                'alamat_organisasi' => 'Jl. Merdeka No. 10',
                'telepon_organisasi' => '021-123456',
                'is_status_kirim_penawaran' => 1,
                'id_nomor_surat' => 1001,
                'input_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_rup' => 'RUP-002',
                'nama_pekerjaan' => 'Pembangunan Aplikasi Monitoring',
                'pagu' => '950.000.000',
                'nama_jenis_pengadaan' => 'Jasa Konsultansi',
                'nama_jenis_produk_rup' => 'Pengembangan Aplikasi',
                'nama_jenis_usaha' => 'Software House',
                'nama_metode_pengadaan' => 'Seleksi',
                'waktu_pemilihan_penyedia' => '2026-08-01',
                'nama_instansi' => 'Badan Pusat Statistik',
                'nama_organisasi' => 'Bidang Transformasi Digital',
                'lokasi_pekerjaan' => 'Bandung',
                'nama_bidang_pekerjaan' => 'Digital Transformation',
                'tahun_anggaran' => '2026',
                'pic' => 'Bunga Lestari',
                'keterangan' => 'Proyek prioritas untuk transformasi data.',
                'is_sirup' => 1,
                'is_import' => 1,
                'is_pekerjaan_prospek' => 0,
                'id_sis_rup' => 'SIS-002',
                'alamat_organisasi' => 'Jl. Cikutra No. 77',
                'telepon_organisasi' => '022-765432',
                'is_status_kirim_penawaran' => 0,
                'id_nomor_surat' => 1002,
                'input_id' => 2,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
        ]);
    }
}
