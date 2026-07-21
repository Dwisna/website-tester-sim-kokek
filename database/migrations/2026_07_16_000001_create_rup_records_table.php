<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('import_rencana_umum_pengadaan', function (Blueprint $table) {
            $table->id();
            $table->string('id_rup')->nullable();
            $table->text('nama_pekerjaan')->nullable();
            $table->text('pagu')->nullable();
            $table->text('nama_jenis_pengadaan')->nullable();
            $table->text('nama_jenis_produk_rup')->nullable();
            $table->text('nama_jenis_usaha')->nullable();
            $table->text('nama_metode_pengadaan')->nullable();
            $table->text('waktu_pemilihan_penyedia')->nullable();
            $table->text('nama_instansi')->nullable();
            $table->text('nama_organisasi')->nullable();
            $table->longText('lokasi_pekerjaan')->nullable();
            $table->text('nama_bidang_pekerjaan')->nullable();
            $table->text('tahun_anggaran')->nullable();
            $table->text('pic')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('is_sirup')->nullable();
            $table->integer('is_import')->nullable();
            $table->unsignedInteger('is_pekerjaan_prospek')->nullable();
            $table->string('id_sis_rup')->nullable();
            $table->text('alamat_organisasi')->nullable();
            $table->text('telepon_organisasi')->nullable();
            $table->integer('is_status_kirim_penawaran')->nullable();
            $table->unsignedBigInteger('id_nomor_surat')->nullable();
            $table->bigInteger('input_id')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_rencana_umum_pengadaan');
    }
};
