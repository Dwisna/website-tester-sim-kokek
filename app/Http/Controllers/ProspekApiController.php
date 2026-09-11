<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RupRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProspekApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->input('per_page', 10);
            $search  = $request->input('search');

            // Filter data yang ditandai sebagai prospek
            $query = RupRecord::query()
                ->where('is_status_spse', 1)
                ->whereNull('deleted_at');

            // Filter Pencarian
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_pekerjaan', 'like', "%{$search}%")
                      ->orWhere('nama_instansi', 'like', "%{$search}%")
                      ->orWhere('nama_organisasi', 'like', "%{$search}%")
                      ->orWhere('id_sis_rup', 'like', "%{$search}%");
                });
            }

            // Filter tambahan opsional
            if ($request->filled('tahun_anggaran')) {
                $query->where('tahun_anggaran', $request->input('tahun_anggaran'));
            }

            $records = $query->orderByDesc('id')->paginate($perPage);
        // Stats
            $statsBase = RupRecord::query()
                ->where('is_status_spse', 1)
                ->whereNull('deleted_at');

            $totalAll = (clone $statsBase)->count();
            $hariIni = (clone $statsBase)->whereDate('created_at', \Carbon\Carbon::today())->count();
            $mingguIni = (clone $statsBase)->where('created_at', '>=', \Carbon\Carbon::now()->startOfWeek())->count();
            $bulanIni = (clone $statsBase)->where('created_at', '>=', \Carbon\Carbon::now()->startOfMonth())->count();

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
                    'created_at'                => $item->created_at?->toDateTimeString(),
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $prospekData,
                'meta'    => [
                    'current_page' => $records->currentPage(),
                    'last_page'    => $records->lastPage(),
                    'per_page'     => $records->perPage(),
                    'total'        => $records->total(),
                    'from'         => $records->firstItem(),
                    'to'           => $records->lastItem(),
                ],
                'stats'   => [
                    'total'      => $totalAll,
                    'hari_ini'   => $hariIni,
                    'minggu_ini' => $mingguIni,
                    'bulan_ini'  => $bulanIni,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('ProspekApiController error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => basename($e->getFile()),
                'prospekStats' => [
                    ['label' => 'Total RUP', 'value' => 0, 'icon' => 'speedometer', 'subtitle' => 'Seluruh data yang tersimpan', 'tone' => 'primary'],
                    ['label' => 'Hari Ini', 'value' => 0, 'icon' => 'speedometer', 'subtitle' => 'Ringkasan data', 'tone' => 'accent'],
                    ['label' => 'Minggu Ini', 'value' => 0, 'icon' => 'speedometer', 'subtitle' => 'Ringkasan data', 'tone' => 'success'],
                    ['label' => 'Bulan Ini', 'value' => 0, 'icon' => 'speedometer', 'subtitle' => 'Ringkasan data', 'tone' => 'warning'],
                ],
            ], 500);
        }
    }

    /**
     * Endpoint untuk import data hasil scraping SPSE ke data prospek
     */
    public function importSpse(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id_sis_rup'               => 'nullable|string',
                'id_rup'                   => 'nullable|string',
                'nama_pekerjaan'           => 'nullable|string',
                'nama_instansi'            => 'nullable|string',
                'pagu'                     => 'nullable|numeric',
                'nama_jenis_pengadaan'     => 'nullable|string',
                'nama_metode_pengadaan'    => 'nullable|string',
                'waktu_pemilihan_penyedia' => 'nullable|string',
                'nama_organisasi'          => 'nullable|string',
                'lokasi_pekerjaan'         => 'nullable|string',
                'tahun_anggaran'           => 'nullable|string',
                'nama_jenis_produk_rup'    => 'nullable|string',
                'nama_jenis_usaha'         => 'nullable|string',
            ]);

            // 1. Cari data yang sudah ada (berdasarkan id_sis_rup atau id_rup)
            $record = null;

            if (!empty($validated['id_sis_rup'])) {
                $record = RupRecord::where('id_sis_rup', $validated['id_sis_rup'])->first();
            }

            if (!$record && !empty($validated['id_rup'])) {
                $record = RupRecord::where('id_rup', $validated['id_rup'])->first();
            }

            // Fallback cari via nama pekerjaan & instansi
            if (!$record && !empty($validated['nama_pekerjaan']) && !empty($validated['nama_instansi'])) {
                $record = RupRecord::where('nama_pekerjaan', 'like', '%' . trim($validated['nama_pekerjaan']) . '%')
                    ->where('nama_instansi', 'like', '%' . trim($validated['nama_instansi']) . '%')
                    ->first();
            }

            // 2. Jika belum ada di database, inisialisasi baris baru dengan kolom wajib
            if (!$record) {
                $record = new RupRecord();
                $record->id_rup = (string) Str::uuid();
                
                // Isi 2 kolom wajib agar tidak ditolak database
                $record->nama_jenis_produk_rup = $validated['nama_jenis_produk_rup'] ?? '-';
                $record->nama_jenis_usaha      = $validated['nama_jenis_usaha'] ?? '-';
            }

            // 3. Simpan data SPSE dan tandai status prospek
            $record->fill(array_filter($validated));
            $record->is_status_spse = 1;
            $record->is_scrapping   = 1;
            $record->save();

            return response()->json([
                'success' => true,
                'message' => 'Data scraping SPSE berhasil masuk ke data prospek.',
                'data'    => $record,
            ]);
        } catch (\Throwable $e) {
            Log::error('ProspekApiController importSpse error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}