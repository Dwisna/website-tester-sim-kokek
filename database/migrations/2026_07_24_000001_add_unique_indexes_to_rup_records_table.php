<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add a stored generated `dupe_hash` column and a unique index on it.
        // This avoids trying to index TEXT columns directly.
        $columnExists = (bool) DB::selectOne("SELECT COUNT(*) AS c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'import_rencana_umum_pengadaan' AND COLUMN_NAME = 'dupe_hash'")->c;
        if (! $columnExists) {
            try {
                DB::statement("ALTER TABLE import_rencana_umum_pengadaan ADD COLUMN dupe_hash VARCHAR(64) GENERATED ALWAYS AS (MD5(CONCAT(LOWER(TRIM(COALESCE(nama_pekerjaan,''))), '|', LOWER(TRIM(COALESCE(nama_instansi,''))), '|', COALESCE(tahun_anggaran,'')))) STORED");
            } catch (\Throwable $e) {
                // ignore failures (e.g., older MySQL that doesn't support generated columns)
            }
        }

        // If dupe_hash column exists, remove existing duplicate rows (keep earliest id)
        try {
            $hasDupeHash = (bool) DB::selectOne("SELECT COUNT(*) AS c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'import_rencana_umum_pengadaan' AND COLUMN_NAME = 'dupe_hash'")->c;
            if ($hasDupeHash) {
                // Delete rows where another row with same dupe_hash has a smaller id (keep earliest)
                DB::statement(<<<'SQL'
                    DELETE r1 FROM import_rencana_umum_pengadaan r1
                    JOIN (
                        SELECT dupe_hash, MIN(id) AS keep_id
                        FROM import_rencana_umum_pengadaan
                        WHERE dupe_hash IS NOT NULL
                        GROUP BY dupe_hash
                        HAVING COUNT(*) > 1
                    ) dup ON r1.dupe_hash = dup.dupe_hash AND r1.id <> dup.keep_id
                SQL
                );
            }
        } catch (\Throwable $e) {
            // ignore cleanup errors to avoid blocking migrations; operator can run artisan rup:clean-duplicates later
        }

        // Create unique index on dupe_hash if missing
        $idx = (bool) DB::selectOne("SELECT COUNT(*) AS c FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'import_rencana_umum_pengadaan' AND INDEX_NAME = 'ruprecord_dupe_hash_unique'")->c;
        if (! $idx) {
            try {
                DB::statement("CREATE UNIQUE INDEX ruprecord_dupe_hash_unique ON import_rencana_umum_pengadaan (dupe_hash)");
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // Ensure unique index on id_rup exists (id_rup is VARCHAR so safe)
        $idIdx = (bool) DB::selectOne("SELECT COUNT(*) AS c FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'import_rencana_umum_pengadaan' AND INDEX_NAME = 'ruprecord_id_rup_unique'")->c;
        if (! $idIdx) {
            try {
                Schema::table('import_rencana_umum_pengadaan', function (Blueprint $table) {
                    $table->unique('id_rup', 'ruprecord_id_rup_unique');
                });
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }

    public function down(): void
    {
        // Drop indexes and column if they exist
        try {
            DB::statement("DROP INDEX IF EXISTS ruprecord_dupe_hash_unique ON import_rencana_umum_pengadaan");
        } catch (\Throwable $e) {
        }

        try {
            Schema::table('import_rencana_umum_pengadaan', function (Blueprint $table) {
                if (Schema::hasColumn('import_rencana_umum_pengadaan', 'dupe_hash')) {
                    $table->dropColumn('dupe_hash');
                }
            });
        } catch (\Throwable $e) {
        }

        try {
            Schema::table('import_rencana_umum_pengadaan', function (Blueprint $table) {
                $table->dropUnique('ruprecord_id_rup_unique');
            });
        } catch (\Throwable $e) {
        }
    }
};
