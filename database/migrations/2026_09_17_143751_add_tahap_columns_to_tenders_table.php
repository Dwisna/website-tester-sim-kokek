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
    Schema::table('tenders', function (Blueprint $table) {
        $table->string('tahap_aktif')->nullable()->after('lokasi_pekerjaan');
        $table->string('tahap_jadwal_teks')->nullable()->after('tahap_aktif');
        $table->timestamp('tahap_mulai')->nullable()->after('tahap_jadwal_teks');
        $table->timestamp('tahap_sampai')->nullable()->after('tahap_mulai');
    });
}

public function down(): void
{
    Schema::table('tenders', function (Blueprint $table) {
        $table->dropColumn(['tahap_aktif', 'tahap_jadwal_teks', 'tahap_mulai', 'tahap_sampai']);
    });
    }
};