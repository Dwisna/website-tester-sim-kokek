<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model `RupRecord`
 *
 * Model ini merepresentasikan tabel `import_rencana_umum_pengadaan` yang
 * menyimpan data RUP (Rencana Umum Pengadaan). Gunakan properti
 * `$fillable` untuk menentukan kolom yang boleh diisi massal.
 */
class RupRecord extends Model
{
    protected $table = 'import_rencana_umum_pengadaan';

    protected $fillable = [
        'id_rup',
        'nama_pekerjaan',
        'pagu',
        'nama_jenis_pengadaan',
        'nama_jenis_produk_rup',
        'nama_jenis_usaha',
        'nama_metode_pengadaan',
        'waktu_pemilihan_penyedia',
        'nama_instansi',
        'nama_organisasi',
        'lokasi_pekerjaan',
        'nama_bidang_pekerjaan',
        'tahun_anggaran',
        'pic',
        'keterangan',
        'is_sirup',
        'is_import',
        'is_pekerjaan_prospek',
        'id_sis_rup',
        'alamat_organisasi',
        'telepon_organisasi',
        'is_status_kirim_penawaran',
        'id_nomor_surat',
        'input_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
