<?php

namespace App\Http\Controllers;

use App\Models\DaftarInformasiLelang;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DaftarPenawaranApiController extends Controller
{
    /**
     * Daftar seluruh penawaran/lelang (Super Cepat & Bebas Timeout).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->get('per_page', 10), 1), 100);

        // Murni query ke tabel fisik tanpa JOIN ke View agar response < 50ms
        $query = DaftarInformasiLelang::query()->orderByDesc('id_dil');

        if ($request->filled('id_dil')) {
            $query->where('id_dil', $request->id_dil);
        }

        // Untuk pencarian seluruh database (Aman, Cepat & Bebas Error 500)
        if ($request->filled('search')) {
            $keyword = trim($request->search);

            // 1. Cari id_rup dari tabel master RUP yang nama pekerjaannya cocok
            $matchedRupIds = DB::table('import_rencana_umum_pengadaan')
                ->where('nama_pekerjaan', 'LIKE', "%{$keyword}%")
                ->orWhere('id_sis_rup', 'LIKE', "%{$keyword}%")
                ->pluck('id_rup')
                ->toArray();

            // 2. Filter query utama berdasarkan id_rup yang ditemukan atau id_dil langsung
            $query->where(function ($q) use ($keyword, $matchedRupIds) {
                $q->whereIn('id_rup', $matchedRupIds)
                  ->orWhere('id_dil', 'LIKE', "%{$keyword}%");
            });
        }

        // Hitung Statistik berdasarkan WIB
        $now = Carbon::now('Asia/Jakarta');

        $totalData = DaftarInformasiLelang::query()->count();
        $dataHariIni = DaftarInformasiLelang::query()
            ->whereDate('created_at', $now->toDateString())
            ->count();
        $dataMingguIni = DaftarInformasiLelang::query()
            ->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
            ->count();
        $dataBulanIni = DaftarInformasiLelang::query()
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $paginator = $query->paginate($perPage);

        // PENCEGAHAN TIMEOUT: Ambil Data View dan Tahapan secara Batch (Menghindari N+1 Query)
        $idDils = $paginator->getCollection()->pluck('id_dil')->toArray();
        
        $viewData = !empty($idDils) 
            ? DB::table('view_data_dil_marketing')->whereIn('id_dil', $idDils)->get()->keyBy('id_dil')
            : collect();

        $allTahapan = !empty($idDils)
            ? DB::table('tahap_lelang')
                ->leftJoin('data_tahapan_lelang', 'tahap_lelang.id_data_tahapan_lelang', '=', 'data_tahapan_lelang.id_data_tahapan_lelang')
                ->whereIn('tahap_lelang.id_dil', $idDils)
                ->orderBy('tahap_lelang.id_tahap_lelang')
                ->get()
                ->groupBy('id_dil')
            : collect();

        $items = $paginator->getCollection()->map(function ($lelangFisik) use ($viewData, $allTahapan) {
            // Gunakan data dari View jika ada, fallback ke fisik jika tidak ada
            $lelang = $viewData->get($lelangFisik->id_dil) ?? $lelangFisik;
            $tahapan = $allTahapan->get($lelangFisik->id_dil) ?? collect();
            
            return $this->formatLelang($lelang, $tahapan);
        });

        $paginator->setCollection($items);

        return response()->json([
            'stats' => [
                'total_data' => $totalData,
                'hari_ini' => $dataHariIni,
                'minggu_ini' => $dataMingguIni,
                'bulan_ini' => $dataBulanIni,
            ],
            'current_page' => $paginator->currentPage(),
            'data' => $paginator->items(),
            'first_page_url' => $paginator->url(1),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'last_page_url' => $paginator->url($paginator->lastPage()),
            'links' => $paginator->linkCollection(),
            'next_page_url' => $paginator->nextPageUrl(),
            'path' => $paginator->path(),
            'per_page' => $paginator->perPage(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ]);
    }

    /**
     * Detail satu penawaran/lelang (Gunakan View hanya saat membuka Halaman Detail).
     */
    public function show(int $idDil): JsonResponse
    {
        $lelang = DB::table('view_data_dil_marketing')
            ->where('id_dil', $idDil)
            ->first();

        if (!$lelang) {
            $lelang = DaftarInformasiLelang::query()->where('id_dil', $idDil)->first();
        }

        if (!$lelang) {
            return response()->json([
                'message' => 'Data daftar penawaran tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $this->formatLelang($lelang),
        ]);
    }

    /**
     * Menyimpan data tender dari sumber eksternal (Sesuai skema instruksi atasan).
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $records = $request->input('records', [$request->all()]);
            $userId = $request->input('user_id') ?? 1;

            $processedCount = 0;
            $duplicatesCount = 0;
            $createdItems = [];

            foreach ($records as $item) {
                if (!is_array($item)) continue;

                // 1. CEK DUPLIKASI BERDASARKAN id_sis_rup DI VIEW
                $idSisRup = $item['id_sis_rup'] ?? $item['id_rup'] ?? $item['id'] ?? null;
                if (!$idSisRup) continue;

                if (DB::table('view_data_dil_marketing')->where('id_sis_rup', (string) $idSisRup)->exists()) {
                    $duplicatesCount++;
                    continue;
                }

                // 2. AMBIL TEKS METODE PENGADAAN (Pastikan huruf kecil semua untuk pencocokan)
                $metodeStr = strtolower(
                    $item['nama_metode_pengadaan'] 
                    ?? $item['metode_pengadaan'] 
                    ?? ($item['procurement']['method'] ?? '')
                );

                // A. DETEKSI ID METODE KUALIFIKASI[cite: 5]
                $idMetodeKualifikasi = str_contains($metodeStr, 'prakualifikasi') ? 7 : 8;

                // B. DETEKSI ID METODE DOKUMEN[cite: 3]
                $idMetodeDokumen = null;
                if (str_contains($metodeStr, 'dua file')) {
                    $idMetodeDokumen = 11;
                } elseif (str_contains($metodeStr, 'satu file')) {
                    $idMetodeDokumen = 10;
                }

                // C. DETEKSI ID METODE EVALUASI[cite: 4]
                $idMetodeEvaluasi = null;
                if (str_contains($metodeStr, 'kualitas dan biaya')) {
                    $idMetodeEvaluasi = 12; // Cek yang paling panjang dulu agar tidak overlap dengan 'kualitas'
                } elseif (str_contains($metodeStr, 'pagu anggaran')) {
                    $idMetodeEvaluasi = 10;
                } elseif (str_contains($metodeStr, 'sistem gugur')) {
                    $idMetodeEvaluasi = 11;
                } elseif (str_contains($metodeStr, 'biaya terendah')) {
                    $idMetodeEvaluasi = 14;
                } elseif (str_contains($metodeStr, 'kualitas')) {
                    $idMetodeEvaluasi = 13;
                }

                DB::transaction(function () use ($item, $idSisRup, $idMetodeKualifikasi, $idMetodeDokumen, $idMetodeEvaluasi, &$processedCount, &$createdItems) {
                    $now = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');

                    // 1. CEK ATAU INSERT KE TABEL MASTER (import_rencana_umum_pengadaan)
                    $masterRup = DB::table('import_rencana_umum_pengadaan')
                        ->where('id_sis_rup', $idSisRup)
                        ->first();

                    if ($masterRup && $masterRup->id_rup) {
                        // Jika sudah ada di master RUP, gunakan UUID yang sudah ada
                        $uuidRup = $masterRup->id_rup;
                    } else {
                        // Jika belum ada, buat UUID baru dan insert sebagai data baru
                        $uuidRup = (string) Str::uuid();
                        
                        DB::table('import_rencana_umum_pengadaan')->insert([
                            'id_rup'                    => $uuidRup,
                            'id_sis_rup'                => $idSisRup,
                            'nama_pekerjaan'            => $item['nama_pekerjaan'] ?? '-',
                            'pagu'                      => $item['pagu'] ?? 0,
                            'nama_instansi'             => $item['nama_instansi'] ?? '-',
                            'nama_metode_pengadaan'     => $item['nama_metode_pengadaan'] ?? '-',
                            'tahun_anggaran'            => $item['tahun_anggaran'] ?? '-',
                            'nama_jenis_pengadaan'      => $item['procurement']['type'] ?? '-',
                            'nama_jenis_produk_rup'     => '-', // Wajib diisi karena struktur NOT NULL
                            'nama_jenis_usaha'          => '-', // Wajib diisi karena struktur NOT NULL
                            'is_import'                 => 1,
                            'is_scrapping'              => 1,
                            'is_sirup'                  => 1,
                            'is_pekerjaan_prospek'      => 1, // Langsung ditandai prospek
                            'created_at'                => $now,
                            'updated_at'                => $now,
                        ]);
                    }
                    
                    // 1.5 MENCARI ID USER BERDASARKAN NAMA PIC DI VIEW RUP
                    $namaPic = DB::table('view_rencana_umum_pengadaan')
                        //->where('id_rup', $uuidRup)
                        ->where('id_sis_rup', $idSisRup)
                        ->value('pic'); 

                    $userIdDinamic = 5; // Default Andry

                    if (!empty($namaPic)) {
                        $userMatches = DB::table('users')
                            ->where('name', 'LIKE', '%' . trim($namaPic) . '%')
                            ->first();

                        if ($userMatches) {
                            $userIdDinamic = $userMatches->id;
                        }
                    }

                    // 2. INSERT KE daftar_informasi_lelang (Tabel Operasional)
                    $idDil = DB::table('daftar_informasi_lelang')->insertGetId([
                        'id_kategori_dil'           => 1,
                        'id_rup'                    => $uuidRup, // Menautkan ke Master RUP
                        'is_scraping'               => 1,
                        'jenis_kontrak'             => $item['jenis_kontrak'] ?? null,
                        'nilai_hps'                 => $item['nilai_hps'] ?? $item['pagu'] ?? null,

                        //'pic_dil'                   => 5,
                        //'input_id'                  => 5,
                        'pic_dil'                   => $userIdDinamic,
			            'input_id'                  => $userIdDinamic,
                        'id_metode_kualifikasi'     => $idMetodeKualifikasi,
                        'id_metode_dokumen'         => $idMetodeDokumen,
                        'id_metode_evaluasi'        => $idMetodeEvaluasi,
                        'created_at'                => $now,
                        'updated_at'                => $now,
                    ]);

                    // 3. INSERT KE hasil_lelang
                    DB::table('hasil_lelang')->insert([
                        'id_dil'        => $idDil,
                        'status_lelang' => 7,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ]);

                    // 4. INSERT KE tahap_lelang (KODE BARU)
                    $scrapedStages = $item['stages'] ?? ($item['timeline']['stages'] ?? []);
                    
                    // Sistem mengambil 23 atau 16 tahapan mutlak dari master
                    $masterTahapanList = DB::table('data_tahapan_lelang')
                        ->where('id_metode_kualifikasi', $idMetodeKualifikasi)
                        ->orderBy('id_data_tahapan_lelang')
                        ->get();

                    if ($masterTahapanList->isNotEmpty()) {
                        $tahapanData = [];
                        
                        foreach ($masterTahapanList as $master) {
                            $matchedStage = null;
                            
                            // Sistem mencari tanggal tahapan tersebut di dalam JSON
                            if (is_array($scrapedStages)) {
                                foreach ($scrapedStages as $stage) {
                                    $stageName = trim($stage['name'] ?? '');
                                    if (strtolower($stageName) === strtolower($master->nama_tahapan_lelang)) {
                                        $matchedStage = $stage;
                                        break;
                                    }
                                }
                            }

                            $tahapanData[] = [
                                'id_dil'                    => $idDil,
                                'id_data_tahapan_lelang'    => $master->id_data_tahapan_lelang,
                                'nama_tahap'                => $master->nama_tahapan_lelang, // Teks baku dan rapi dari database
                                'waktu_mulai_tahap_lelang'  => !empty($matchedStage['startDate']) 
                                    ? Carbon::parse($matchedStage['startDate'])->timezone('Asia/Jakarta')->format('Y-m-d H:i:s') 
                                    : null,
                                'waktu_sampai_tahap_lelang' => !empty($matchedStage['endDate']) 
                                    ? Carbon::parse($matchedStage['endDate'])->timezone('Asia/Jakarta')->format('Y-m-d H:i:s') 
                                    : null,
                            ];
                        }

                        if (!empty($tahapanData)) {
                            DB::table('tahap_lelang')->insert($tahapanData);
                        }
                    }

                    $processedCount++;
                    $createdItems[] = ['id_dil' => $idDil, 'id_rup' => $uuidRup];
                });
            }

            return response()->json([
                'success' => true,
                'message' => "Proses selesai. Disimpan: {$processedCount}, Duplikat: {$duplicatesCount}",
                'data'    => $createdItems,
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'success'      => false,
                'message'      => 'Terjadi kesalahan pada server.',
                'error_detail' => $e->getMessage(),
                'file'         => $e->getFile(),
                'line'         => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Format data untuk response API (Menerima parameter opsional $preloadedTahapan untuk mencegah N+1 Query).
     */
    private function formatLelang($lelang, $preloadedTahapan = null): array
    {
        $idDil = is_object($lelang) ? $lelang->id_dil : $lelang['id_dil'];

        if ($preloadedTahapan !== null) {
            $tahapan = collect($preloadedTahapan);
        } else {
            $tahapan = DB::table('tahap_lelang')
                ->leftJoin('data_tahapan_lelang', 'tahap_lelang.id_data_tahapan_lelang', '=', 'data_tahapan_lelang.id_data_tahapan_lelang')
                ->where('tahap_lelang.id_dil', $idDil)
                ->orderBy('tahap_lelang.id_tahap_lelang')
                ->get();
        }

        $idMetodeKualifikasi = is_object($lelang) 
            ? ($lelang->id_metode_kualifikasi ?? null) 
            : ($lelang['id_metode_kualifikasi'] ?? null);

        $kualifikasi = match ((int) $idMetodeKualifikasi) {
            7 => 'Prakualifikasi',
            8 => 'Pascakualifikasi',
            default => 'Tidak diketahui',
        };
        
        $idMetodeDokumen = is_object($lelang) ? ($lelang->id_metode_dokumen ?? null) : ($lelang['id_metode_dokumen'] ?? null);
        $dokumen = match ((int) $idMetodeDokumen) {
            10 => 'Satu File',
            11 => 'Dua File',
            default => '-',
        };

        $idMetodeEvaluasi = is_object($lelang) ? ($lelang->id_metode_evaluasi ?? null) : ($lelang['id_metode_evaluasi'] ?? null);
        $evaluasi = match ((int) $idMetodeEvaluasi) {
            10 => 'Pagu Anggaran',
            11 => 'Sistem Gugur',
            12 => 'Kualitas dan Biaya',
            13 => 'Kualitas',
            14 => 'Biaya Terendah',
            default => '-',
        };

        $formattedStages = $tahapan->values()->map(function ($tahap, $index) {
            return [
                'number' => $index + 1,
                'id_tahap_lelang' => $tahap->id_tahap_lelang,
                'id_data_tahapan_lelang' => $tahap->id_data_tahapan_lelang,
                'name' => $tahap->nama_tahap ?? $tahap->nama_tahapan_lelang ?? 'Tahapan',
                'startDate' => $this->formatDate($tahap->waktu_mulai_tahap_lelang),
                'endDate' => $this->formatDate($tahap->waktu_sampai_tahap_lelang),
            ];
        })->values()->all();

        $getVal = fn($key) => is_object($lelang) ? ($lelang->$key ?? null) : ($lelang[$key] ?? null);

        $rawId = $getVal('id_sis_rup') ?? $getVal('id_tender') ?? $getVal('id_rup') ?? $getVal('id_dil');
        
        $cleanId = str_replace('tender_', '', (string) $rawId);
        if (str_contains($cleanId, '-')) {
            $cleanId = preg_replace('/[^0-9]/', '', $cleanId);
        }
        $idTender = !empty($cleanId) ? $cleanId : $getVal('id_dil');

        $pagu = $getVal('pagu') ?? $getVal('nilai_pekerjaan') ?? $getVal('nilai_hps');

        $pemberiKerja = $getVal('pemberi_kerja') 
            ?? $getVal('agency_name') 
            ?? $getVal('nama_instansi') 
            ?? '-';

        $metodePengadaan = $getVal('nama_metode_pengadaan') ?? $getVal('metode_pengadaan') ?? '-';

        $namaPekerjaan = $getVal('nama_pekerjaan') 
            ?? $getVal('nama_paket') 
            ?? $getVal('keterangan_lelang') 
            ?? '-';

        return [
            'id_dil' => $getVal('id_dil'),
            'id_tender' => $idTender,
            'id_sis_rup' => $idTender,
            'id_rup' => $getVal('id_rup'),
            
            'id_pic_dil'            => $getVal('id_pic_dil'),
            'nama_pic_dil'          => $getVal('nama_pic_dil') ?? $getVal('pic') ?? '-',
            'pic'                   => $getVal('nama_pic_dil') ?? $getVal('pic') ?? '-',
            'nama_pic'              => $getVal('nama_pic_dil') ?? $getVal('pic') ?? '-',

            'nama_pekerjaan' => $namaPekerjaan,
            'nama_paket' => $namaPekerjaan,
            
            'pemberi_kerja' => $pemberiKerja,
            'nama_instansi' => $pemberiKerja,

            'metode_pengadaan' => $metodePengadaan,
            'nama_metode_pengadaan' => $metodePengadaan,

            'pagu' => $pagu,
            'nilai_pekerjaan' => $pagu,
            'nilai_hps' => $getVal('nilai_hps') ?? $pagu,

            'melalui_rup' => !empty($getVal('id_rup')) ? 'Ya' : 'Tidak',
            'tahun_anggaran' => $getVal('pembebanan_tahun_anggaran') ?? $getVal('tahun_anggaran'),
            'jenis_kontrak' => $getVal('jenis_kontrak'),
            'id_metode_kualifikasi' => $getVal('id_metode_kualifikasi'),
            'kualifikasi' => $kualifikasi,
            
            'id_metode_dokumen' => $getVal('id_metode_dokumen'),
            'dokumen' => $dokumen,
            'id_metode_evaluasi' => $getVal('id_metode_evaluasi'),
            'evaluasi' => $evaluasi,

            'hasil_lelang' => $getVal('nama_hasil_lelang') ?? '-',
            'keterangan_lelang' => '-',

            'timeline' => [
                'startDate' => $this->formatDate($tahapan->first()?->waktu_mulai_tahap_lelang),
                'currentStage' => $this->getCurrentStage($tahapan)['name'] ?? null,
                'currentStageDetails' => $this->getCurrentStage($tahapan),
                'stages' => $formattedStages,
            ],
        ];
    }

    private function getCurrentStage($tahapan): array
    {
        $now = Carbon::now('Asia/Jakarta');

        $validStages = $tahapan
            ->filter(fn($t) => $t->waktu_mulai_tahap_lelang && $t->waktu_sampai_tahap_lelang)
            ->values();

        if ($validStages->isEmpty()) {
            return [
                'name' => null,
                'startDate' => null,
                'endDate' => null,
                'status' => 'Tidak tersedia',
                'isCurrentOn' => false,
                'activeStages' => [],
            ];
        }

        foreach ($validStages as $index => $tahap) {
            try {
                $start = Carbon::parse($tahap->waktu_mulai_tahap_lelang, 'Asia/Jakarta');
                $end = Carbon::parse($tahap->waktu_sampai_tahap_lelang, 'Asia/Jakarta');

                if ($now->betweenIncluded($start, $end)) {
                    $name = $tahap->nama_tahap ?? $tahap->nama_tahapan_lelang ?? 'Tahapan';

                    return [
                        'id_tahap_lelang' => $tahap->id_tahap_lelang,
                        'number' => $index + 1,
                        'name' => $name,
                        'startDate' => $this->formatDate($tahap->waktu_mulai_tahap_lelang),
                        'endDate' => $this->formatDate($tahap->waktu_sampai_tahap_lelang),
                        'status' => 'Sedang berlangsung',
                        'isCurrentOn' => true,
                        'activeStages' => [[
                            'number' => $index + 1,
                            'name' => $name,
                            'startDate' => $this->formatDate($tahap->waktu_mulai_tahap_lelang),
                            'endDate' => $this->formatDate($tahap->waktu_sampai_tahap_lelang),
                        ]],
                    ];
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        $lastStage = $validStages->last();
        try {
            $lastEnd = Carbon::parse($lastStage->waktu_sampai_tahap_lelang, 'Asia/Jakarta');
            if ($now->greaterThan($lastEnd)) {
                $name = $lastStage->nama_tahap ?? $lastStage->nama_tahapan_lelang ?? 'Tahapan';

                return [
                    'id_tahap_lelang' => $lastStage->id_tahap_lelang,
                    'number' => $validStages->count(),
                    'name' => $name,
                    'startDate' => $this->formatDate($lastStage->waktu_mulai_tahap_lelang),
                    'endDate' => $this->formatDate($lastStage->waktu_sampai_tahap_lelang),
                    'status' => 'Selesai',
                    'isCurrentOn' => false,
                    'activeStages' => [],
                ];
            }
        } catch (\Throwable $e) {
            // lanjut
        }

        return [
            'name' => null,
            'startDate' => null,
            'endDate' => null,
            'status' => 'Tidak tersedia',
            'isCurrentOn' => false,
            'activeStages' => [],
        ];
    }

    private function formatDate($date): ?string
    {
        if (!$date) return null;

        try {
            return Carbon::parse($date)->timezone('Asia/Jakarta')->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }
}