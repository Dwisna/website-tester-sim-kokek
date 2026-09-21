<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RupRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProspekApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->input('per_page', 10);
            $search  = $request->input('search');

            // Query dasar data prospek
            $baseQuery = RupRecord::query()
                ->where('is_status_spse', 1)
                ->whereNull('deleted_at');

            // 1. Hitung Statistik berdasarkan kapan data masuk prospek (prospek_at)
            $now = now();
            $stats = [
                'total'      => (clone $baseQuery)->count(),
                'hari_ini'   => (clone $baseQuery)->whereDate('prospek_at', $now->toDateString())->count(),
                'minggu_ini' => (clone $baseQuery)->whereBetween('prospek_at', [
                    $now->copy()->startOfWeek(), 
                    $now->copy()->endOfWeek()
                ])->count(),
                'bulan_ini'  => (clone $baseQuery)->whereYear('prospek_at', $now->year)
                                                  ->whereMonth('prospek_at', $now->month)
                                                  ->count(),
            ];

            // 2. Query untuk tabel dengan filter pencarian
            $query = clone $baseQuery;

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_pekerjaan', 'like', "%{$search}%")
                      ->orWhere('nama_instansi', 'like', "%{$search}%")
                      ->orWhere('nama_organisasi', 'like', "%{$search}%")
                      ->orWhere('id_sis_rup', 'like', "%{$search}%")
                      ->orWhere('tahap', 'like', "%{$search}%");
                });
            }

            if ($request->filled('tahun_anggaran')) {
                $query->where('tahun_anggaran', $request->input('tahun_anggaran'));
            }

            $records = $query->orderByDesc('prospek_at')->orderByDesc('id')->paginate($perPage);

            $prospekData = $records->map(function ($item) {
                return [
                    'id'                        => $item->id,
                    'id_rup'                    => $item->id_rup,
                    'id_sis_rup'                => $item->id_sis_rup,
                    'nama_pekerjaan'            => $item->nama_pekerjaan,
                    'pagu'                      => $item->pagu,
                    'nama_jenis_pengadaan'      => $item->nama_jenis_pengadaan,
                    'nama_metode_pengadaan'     => $item->nama_metode_pengadaan,
                    'waktu_pemilihan_penyedia'  => $item->waktu_pemilihan_penyedia,
                    'nama_instansi'             => $item->nama_instansi,
                    'nama_organisasi'           => $item->nama_organisasi,
                    'lokasi_pekerjaan'          => $item->lokasi_pekerjaan,
                    'tahun_anggaran'            => $item->tahun_anggaran,
                    'status'                    => 'Daftar',
                    // Data kolom tahapan SPSE
                    'tahap'                     => $item->tahap,
                    'tahap_mulai'               => $item->tahap_mulai,
                    'tahap_sampai'              => $item->tahap_sampai,
                    'tahap_perubahan'           => $item->tahap_perubahan ?? 'Tidak Ada',
                    'created_at'                => $item->created_at?->toDateTimeString(),
                    'prospek_at'                => $item->prospek_at?->toDateTimeString(),
                ];
            });

            return response()->json([
                'success' => true,
                'stats'   => $stats,
                'data'    => $prospekData,
                'meta'    => [
                    'current_page' => $records->currentPage(),
                    'last_page'    => $records->lastPage(),
                    'per_page'     => $records->perPage(),
                    'total'        => $records->total(),
                    'from'         => $records->firstItem(),
                    'to'           => $records->lastItem(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('ProspekApiController error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => basename($e->getFile()),
            ], 500);
        }
    }

    /**
     * Endpoint untuk import data hasil scraping SPSE ke data prospek
     */
    public function importSpse(Request $request): JsonResponse
    {
        set_time_limit(300);

        try {
            $payload = $request->all();

            // Ekstrak data (format n8n {records: [...]}, array [...], atau single {...})
            if (isset($payload['records']) && is_array($payload['records'])) {
                $items = $payload['records'];
            } elseif (is_array($payload) && !isset($payload[0])) {
                $items = [$payload];
            } else {
                $items = (array) $payload;
            }

            if (empty($items)) {
                return response()->json(['success' => false, 'message' => 'Data payload kosong.'], 400);
            }

            // 1. Ambil semua identitas pencarian dari batch
            $sisRupIds = [];
            $rupIds    = [];
            $jobNames  = [];

            foreach ($items as $item) {
                if (!is_array($item)) continue;
                if (!empty($item['id_sis_rup']))     $sisRupIds[] = (string) $item['id_sis_rup'];
                if (!empty($item['id_rup']))         $rupIds[]     = (string) $item['id_rup'];
                if (!empty($item['nama_pekerjaan'])) $jobNames[]   = trim($item['nama_pekerjaan']);
            }

            // 2. Ambil data eksisting dari database dalam 1 query
            $existingRecords = RupRecord::query()
                ->where(function ($q) use ($sisRupIds, $rupIds, $jobNames) {
                    if (!empty($sisRupIds)) $q->orWhereIn('id_sis_rup', $sisRupIds);
                    if (!empty($rupIds))     $q->orWhereIn('id_rup', $rupIds);
                    if (!empty($jobNames))   $q->orWhereIn('nama_pekerjaan', $jobNames);
                })
                ->get();

            // Helper untuk membuat kunci unik pencocokan (nama_pekerjaan + tahun_anggaran)
            $makeKey = fn($name, $year) => strtolower(trim($name ?? '')) . '_' . trim($year ?? '');

            // Indeks data di memori PHP
            $bySisRup  = $existingRecords->whereNotNull('id_sis_rup')->keyBy('id_sis_rup');
            $byRup     = $existingRecords->whereNotNull('id_rup')->keyBy('id_rup');
            $byJobName = $existingRecords->keyBy(fn($r) => $makeKey($r->nama_pekerjaan, $r->tahun_anggaran));

            $now = now();
            $newRecords = [];
            $updatedCount = 0;

            // 3. Eksekusi penyimpanan dengan DB Transaction
            DB::transaction(function () use ($items, $bySisRup, $byRup, $byJobName, $makeKey, $now, &$newRecords, &$updatedCount) {
                foreach ($items as $item) {
                    if (!is_array($item)) continue;

                    $record = null;
                    $itemKey = $makeKey($item['nama_pekerjaan'] ?? '', $item['tahun_anggaran'] ?? '');

                    // Prioritas 1: Cocokkan via id_sis_rup
                    if (!empty($item['id_sis_rup']) && isset($bySisRup[(string) $item['id_sis_rup']])) {
                        $record = $bySisRup[(string) $item['id_sis_rup']];
                    }
                    // Prioritas 2: Cocokkan via id_rup
                    elseif (!empty($item['id_rup']) && isset($byRup[(string) $item['id_rup']])) {
                        $record = $byRup[(string) $item['id_rup']];
                    }
                    // Prioritas 3: Fallback ke kombinasi nama + tahun anggaran (case-insensitive)
                    elseif (!empty($item['nama_pekerjaan']) && isset($byJobName[$itemKey])) {
                        $record = $byJobName[$itemKey];
                    }

                    if ($record) {
                        // Data sudah ada -> Update field
                        $record->nama_pekerjaan           = $item['nama_pekerjaan'] ?? $record->nama_pekerjaan;
                        $record->nama_instansi            = $item['nama_instansi'] ?? $record->nama_instansi;
                        $record->nama_organisasi          = $item['nama_organisasi'] ?? $record->nama_organisasi;
                        $record->pagu                     = isset($item['pagu']) ? (float) $item['pagu'] : $record->pagu;
                        $record->nama_jenis_pengadaan     = $item['nama_jenis_pengadaan'] ?? $record->nama_jenis_pengadaan;
                        $record->nama_metode_pengadaan    = $item['nama_metode_pengadaan'] ?? $record->nama_metode_pengadaan;
                        $record->waktu_pemilihan_penyedia = $item['waktu_pemilihan_penyedia'] ?? $record->waktu_pemilihan_penyedia;
                        $record->lokasi_pekerjaan         = $item['lokasi_pekerjaan'] ?? $record->lokasi_pekerjaan;
                        $record->tahun_anggaran           = $item['tahun_anggaran'] ?? $record->tahun_anggaran;
                        
                        // Update tahapan tender
                        $record->tahap                    = $item['tahap'] ?? $record->tahap;
                        $record->tahap_mulai              = $item['tahap_mulai'] ?? $record->tahap_mulai;
                        $record->tahap_sampai             = $item['tahap_sampai'] ?? $record->tahap_sampai;
                        $record->tahap_perubahan          = $item['tahap_perubahan'] ?? $record->tahap_perubahan ?? 'Tidak Ada';

                        if (!empty($item['id_sis_rup'])) {
                            $record->id_sis_rup = $item['id_sis_rup'];
                        }

                        $record->is_status_spse = 1;
                        $record->is_scrapping   = 1;

                        if (is_null($record->prospek_at)) {
                            $record->prospek_at = $now;
                        }

                        $record->save();
                        $updatedCount++;
                    } else {
                        // Data baru -> Kumpulkan untuk bulk insert
                        $newRecords[] = [
                            'id_rup'                   => !empty($item['id_rup']) ? $item['id_rup'] : (string) Str::uuid(),
                            'id_sis_rup'               => $item['id_sis_rup'] ?? null,
                            'nama_pekerjaan'           => $item['nama_pekerjaan'] ?? null,
                            'nama_instansi'            => $item['nama_instansi'] ?? null,
                            'nama_organisasi'          => $item['nama_organisasi'] ?? null,
                            'pagu'                     => isset($item['pagu']) ? (float) $item['pagu'] : 0,
                            'nama_jenis_pengadaan'     => $item['nama_jenis_pengadaan'] ?? null,
                            'nama_metode_pengadaan'    => $item['nama_metode_pengadaan'] ?? null,
                            'waktu_pemilihan_penyedia' => $item['waktu_pemilihan_penyedia'] ?? null,
                            'lokasi_pekerjaan'         => $item['lokasi_pekerjaan'] ?? null,
                            'tahun_anggaran'           => $item['tahun_anggaran'] ?? null,
                            'nama_jenis_produk_rup'    => $item['nama_jenis_produk_rup'] ?? '-',
                            'nama_jenis_usaha'         => $item['nama_jenis_usaha'] ?? '-',
                            'is_status_spse'           => 1,
                            'is_scrapping'             => 1,
                            'prospek_at'               => $now,
                            'created_at'               => $now,
                            'updated_at'               => $now,
                            // Tambahan kolom tahapan SPSE
                            'tahap'                    => $item['tahap'] ?? null,
                            'tahap_mulai'              => $item['tahap_mulai'] ?? null,
                            'tahap_sampai'             => $item['tahap_sampai'] ?? null,
                            'tahap_perubahan'          => $item['tahap_perubahan'] ?? 'Tidak Ada',
                        ];
                    }
                }

                // Bulk insert data baru
                if (!empty($newRecords)) {
                    RupRecord::insert($newRecords);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memproses ' . count($items) . ' data SPSE (' . count($newRecords) . ' baru, ' . $updatedCount . ' diperbarui).',
                'total'   => count($items),
            ]);

        } catch (\Throwable $e) {
            Log::error('ProspekApiController importSpse error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => basename($e->getFile()),
            ], 500);
        }
    }
}