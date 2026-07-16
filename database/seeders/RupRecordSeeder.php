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
        $sample = [];

        for ($i = 1; $i <= 35; $i++) {
            $sample[] = [
                'id_rup' => 'RUP-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_pekerjaan' => 'Proyek Pengadaan ' . ($i % 5 === 0 ? 'Sistem' : 'Perangkat'),
                'pagu' => number_format(rand(500_000_000, 2_500_000_000), 0, ',', '.'),
                'nama_jenis_pengadaan' => $i % 2 ? 'Barang' : 'Jasa',
                'nama_jenis_produk_rup' => $i % 3 === 0 ? 'Aplikasi' : 'Infrastruktur',
                'nama_jenis_usaha' => $i % 4 === 0 ? 'Perusahaan Teknologi' : 'UMKM',
                'nama_metode_pengadaan' => $i % 3 === 0 ? 'Tender' : 'Seleksi',
                'waktu_pemilihan_penyedia' => now()->addDays($i)->format('Y-m-d'),
                'nama_instansi' => $i % 2 ? 'Kementerian Kominfo' : 'Badan Pusat Statistik',
                'nama_organisasi' => 'Organisasi ' . $i,
                'lokasi_pekerjaan' => 'Jakarta ' . $i,
                'nama_bidang_pekerjaan' => $i % 2 ? 'Teknologi Informasi' : 'Manajemen Proyek',
                'tahun_anggaran' => '2026',
                'pic' => 'PIC ' . $i,
                'keterangan' => 'Seed data untuk testing dashboard RUP.',
                'is_sirup' => $i % 2,
                'is_import' => $i % 3 === 0 ? 1 : 0,
                'is_pekerjaan_prospek' => $i % 4 === 0 ? 1 : 0,
                'id_sis_rup' => 'SIS-' . $i,
                'alamat_organisasi' => 'Jl. Demo No. ' . $i,
                'telepon_organisasi' => '021-0000' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'is_status_kirim_penawaran' => $i % 5 === 0 ? 1 : 0,
                'id_nomor_surat' => rand(1000, 9999),
                'input_id' => $i,
                'created_at' => now()->subDays(35 - $i),
                'updated_at' => now()->subDays(35 - $i),
            ];
        }

        RupRecord::insert($sample);
    }
}
