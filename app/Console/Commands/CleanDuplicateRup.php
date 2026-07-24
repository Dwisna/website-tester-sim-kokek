<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Perintah artisan `rup:clean-duplicates`.
 *
 * Perintah ini memindai tabel impor dan mengidentifikasi duplikat berdasarkan
 * `id_rup` atau kombinasi `nama_pekerjaan + nama_instansi + tahun_anggaran`.
 * Opsi `--dry-run` hanya menampilkan hasil tanpa menghapus.
 */
class CleanDuplicateRup extends Command
{
    protected $signature = 'rup:clean-duplicates {--dry-run}';
    protected $description = 'Detect and optionally remove duplicate RUP records based on id_rup or name+instansi+tahun';

    /**
     * Menjalankan pemeriksaan dan penghapusan duplikat.
     *
     * Jika opsi `--dry-run` disertakan, perintah hanya menampilkan grup duplikat
     * tanpa menghapus baris apa pun.
     *
     * @return int
     */
    public function handle()
    {
        $dry = $this->option('dry-run');

        $this->info('Scanning for duplicate id_rup...');
        $dups = DB::table('import_rencana_umum_pengadaan')
            ->select('id_rup', DB::raw('count(*) as cnt'))
            ->whereNotNull('id_rup')
            ->groupBy('id_rup')
            ->havingRaw('count(*) > 1')
            ->get();

        if ($dups->isEmpty()) {
            $this->info('No duplicate id_rup found.');
        } else {
            foreach ($dups as $d) {
                $this->line("id_rup {$d->id_rup} -> {$d->cnt} records");
                if (! $dry) {
                    // keep the earliest id, delete others
                    $keep = DB::table('import_rencana_umum_pengadaan')->where('id_rup', $d->id_rup)->orderBy('created_at')->first();
                    DB::table('import_rencana_umum_pengadaan')->where('id_rup', $d->id_rup)->where('id', '!=', $keep->id)->delete();
                    $this->line("Removed duplicates for id_rup {$d->id_rup}");
                }
            }
        }

        $this->info('Scanning for duplicate name+instansi+tahun...');
        $dups2 = DB::table('import_rencana_umum_pengadaan')
            ->select(DB::raw('LOWER(TRIM(nama_pekerjaan)) as np, LOWER(TRIM(nama_instansi)) as ni, tahun_anggaran, count(*) as cnt'))
            ->groupBy('np', 'ni', 'tahun_anggaran')
            ->havingRaw('count(*) > 1')
            ->get();

        if ($dups2->isEmpty()) {
            $this->info('No duplicate name+instansi+tahun found.');
        } else {
            foreach ($dups2 as $d) {
                $this->line("Name+Instansi+Tahun ({$d->np}|{$d->ni}|{$d->tahun_anggaran}) -> {$d->cnt} records");
                if (! $dry) {
                    $keep = DB::table('import_rencana_umum_pengadaan')
                        ->whereRaw('LOWER(TRIM(nama_pekerjaan)) = ?', [$d->np])
                        ->whereRaw('LOWER(TRIM(nama_instansi)) = ?', [$d->ni])
                        ->where('tahun_anggaran', $d->tahun_anggaran)
                        ->orderBy('created_at')
                        ->first();

                    DB::table('import_rencana_umum_pengadaan')
                        ->whereRaw('LOWER(TRIM(nama_pekerjaan)) = ?', [$d->np])
                        ->whereRaw('LOWER(TRIM(nama_instansi)) = ?', [$d->ni])
                        ->where('tahun_anggaran', $d->tahun_anggaran)
                        ->where('id', '!=', $keep->id)
                        ->delete();

                    $this->line("Removed duplicates for group ({$d->np}|{$d->ni}|{$d->tahun_anggaran})");
                }
            }
        }

        $this->info('Done.');
        return 0;
    }
}
