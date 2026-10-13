<?php

namespace App\Http\Controllers;

use App\Models\DaftarInformasiLelang;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DaftarPenawaranApiController extends Controller
{
    /**
     * Daftar seluruh penawaran/lelang.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(
            max((int) $request->get('per_page', 10), 1),
            100
        );

        $query = DaftarInformasiLelang::query()
            ->whereNull('daftar_informasi_lelang.deleted_at')
            ->orderByDesc('daftar_informasi_lelang.id_dil');

        if ($request->filled('id_dil')) {
            $query->where(
                'daftar_informasi_lelang.id_dil',
                $request->id_dil
            );
        }

        //card
        $totalData = DaftarInformasiLelang::query()
            ->whereNull('daftar_informasi_lelang.deleted_at')
            ->count();

        $dataMasukHariIni = DaftarInformasiLelang::query()
            ->whereNull('daftar_informasi_lelang.deleted_at')
            ->whereDate(
                'daftar_informasi_lelang.created_at',
                Carbon::today('Asia/Jakarta')
            )
            ->count();

        $paginator = $query->paginate($perPage);

        $items = $paginator->getCollection()->map(
            fn ($lelang) => $this->formatLelang($lelang)
        );

        $paginator->setCollection($items);

        return response()->json([
    'stats' => [
        'data_masuk_hari_ini' => $dataMasukHariIni,
        'total_data' => $totalData,
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
     * Detail satu penawaran/lelang.
     */
    public function show(int $idDil): JsonResponse
    {
        $lelang = DaftarInformasiLelang::query()
            ->where('id_dil', $idDil)
            ->whereNull('deleted_at')
            ->first();

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
     * Menyimpan data tender dari sumber eksternal.
     *
     * Dedup hanya berdasarkan id_tender.
     *
     * TIDAK menggunakan:
     * - id_rup
     * - id_sis_rup
     */
    public function store(Request $request): JsonResponse
{
    /*
     * n8n mengirim:
     *
     * [
     *     {...tender 1...},
     *     {...tender 2...},
     *     {...tender 3...}
     * ]
     *
     * Jadi ambil seluruh JSON sebagai array records.
     */
    $records = $request->json()->all();

    if (!is_array($records)) {
        return response()->json([
            'success' => false,
            'message' => 'Format data harus berupa array tender.',
        ], 422);
    }

    if (count($records) === 0) {
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada data tender.',
        ], 422);
    }

    $created = 0;
    $duplicate = 0;
    $failed = 0;

    /*
     * Proses setiap tender satu per satu.
     */
    foreach ($records as $record) {

        try {

            /*
             * Validasi ID tender.
             */
            if (
                !is_array($record) ||
                empty($record['id'])
            ) {
                $failed++;
                continue;
            }

            $idTender = $record['id'];

            /*
             * Cek apakah tender sudah pernah masuk.
             */
            $existing = DaftarInformasiLelang::withTrashed()
                ->where('id_tender', $idTender)
                ->first();

            if ($existing) {
                $duplicate++;
                continue;
            }

            $location = $record['location'] ?? [];
            $procurement = $record['procurement'] ?? [];
            $budget = $record['budget'] ?? [];

            $currentStageSource = $record['currentStage'] ?? [];
            $stages = $record['stages'] ?? [];

            /*
             * Tentukan metode kualifikasi.
             */
            $method = strtolower(
                (string) ($procurement['method'] ?? '')
            );

            if (str_contains($method, 'prakualifikasi')) {
                $idMetodeKualifikasi = 7;
            } elseif (str_contains($method, 'pascakualifikasi')) {
                $idMetodeKualifikasi = 8;
            } else {
                $idMetodeKualifikasi = null;
            }

            /*
             * Satu tender = satu transaksi.
             *
             * Kalau terjadi error pada tender ini,
             * data tender tersebut tidak setengah masuk.
             */
            DB::transaction(function () use (
                $idTender,
                $record,
                $location,
                $procurement,
                $budget,
                $stages,
                $idMetodeKualifikasi
            ) {

                /*
                 * INSERT DATA UTAMA TENDER
                 */
                $lelang = DaftarInformasiLelang::create([
                    'id_tender' => $idTender,

                    'nama_paket' =>
                        $record['name'] ?? null,

                    'nama_instansi' =>
                        $location['klpd'] ?? null,

                    'lokasi_region' =>
                        $location['region'] ?? null,

                    'lokasi_kota' =>
                        $location['city'] ?? null,

                    'lokasi_provinsi' =>
                        $location['province'] ?? null,

                    'jenis_pengadaan' =>
                        $procurement['type'] ?? null,

                    'nama_metode_pengadaan' =>
                        $procurement['method'] ?? null,

                    'id_metode_kualifikasi' =>
                        $idMetodeKualifikasi,

                    'pagu' =>
                        $budget['package'] ?? null,

                    'nilai_hps' =>
                        $budget['hps'] ?? null,

                    'jenis_kontrak' =>
                        $procurement['contractType'] ?? null,

                    'pembebanan_tahun_anggaran' =>
                        $procurement['budgetYear'] ?? null,

                    'keterangan_lelang' => null,
                ]);

                /*
                 * AMBIL SEMUA STAGES.
                 *
                 * stageHistory TIDAK DIGUNAKAN.
                 */foreach ($stages as $stage) {
    if (!is_array($stage)) {
        continue;
    }

    $stageName = trim((string) ($stage['name'] ?? ''));

    if ($stageName === '') {
        continue;
    }

    // Cari referensi tahap berdasarkan nama dan metode kualifikasi.
    $master = DB::table('data_tahapan_lelang')
        ->where('nama_tahapan_lelang', $stageName)
        ->when(
            $idMetodeKualifikasi !== null,
            function ($query) use ($idMetodeKualifikasi) {
                $query->where(
                    'id_metode_kualifikasi',
                    $idMetodeKualifikasi
                );
            }
        )
        ->first();

    DB::table('tahap_lelang')->insert([
        'id_dil' => $lelang->id_dil,

        'id_data_tahapan_lelang' =>
            $master?->id_data_tahapan_lelang,

        'nama_tahap' => $stageName,

        'waktu_mulai_tahap_lelang' =>
            isset($stage['startDate'])
                ? Carbon::parse($stage['startDate'])
                    ->timezone('Asia/Jakarta')
                    ->format('Y-m-d H:i:s')
                : null,

        'waktu_sampai_tahap_lelang' =>
            isset($stage['endDate'])
                ? Carbon::parse($stage['endDate'])
                    ->timezone('Asia/Jakarta')
                    ->format('Y-m-d H:i:s')
                : null,
    ]);
}
            });

            $created++;

        } catch (\Throwable $e) {

            /*
             * Satu tender gagal tidak menghentikan
             * tender berikutnya.
             */
            $failed++;

            \Log::error(
                'Gagal menyimpan daftar penawaran',
                [
                    'id_tender' => $record['id'] ?? null,
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Batch daftar penawaran berhasil diproses.',
        'total' => count($records),
        'created' => $created,
        'duplicate' => $duplicate,
        'failed' => $failed,
    ]);
}

    /**
     * Format data untuk response API.
     */
    private function formatLelang(
        DaftarInformasiLelang $lelang
    ): array {
        $tahapan = DB::table('tahap_lelang')
            ->leftJoin(
                'data_tahapan_lelang',
                'tahap_lelang.id_data_tahapan_lelang',
                '=',
                'data_tahapan_lelang.id_data_tahapan_lelang'
            )
            ->where(
                'tahap_lelang.id_dil',
                $lelang->id_dil
            )
            ->orderBy('tahap_lelang.id_tahap_lelang')
            ->get([
                'tahap_lelang.id_tahap_lelang',
                'tahap_lelang.id_dil',
                'tahap_lelang.id_data_tahapan_lelang',
                'tahap_lelang.nama_tahap',
                'tahap_lelang.waktu_mulai_tahap_lelang',
                'tahap_lelang.waktu_sampai_tahap_lelang',
                'data_tahapan_lelang.nama_tahapan_lelang',
                'data_tahapan_lelang.urutan',
            ]);

        $kualifikasi = match (
            (int) $lelang->id_metode_kualifikasi
        ) {
            7 => 'Prakualifikasi',
            8 => 'Pascakualifikasi',
            default => 'Tidak diketahui',
        };

        $formattedStages = $tahapan
            ->values()
            ->map(function ($tahap, $index) {
                return [
                    'number' =>
                        $tahap->urutan ?? ($index + 1),

                    'id_tahap_lelang' =>
                        $tahap->id_tahap_lelang,

                    'id_data_tahapan_lelang' =>
                        $tahap->id_data_tahapan_lelang,

                    'name' =>
                        $tahap->nama_tahap
                        ?? $tahap->nama_tahapan_lelang
                        ?? 'Tahapan',

                    'startDate' =>
                        $this->formatDate(
                            $tahap->waktu_mulai_tahap_lelang
                        ),

                    'endDate' =>
                        $this->formatDate(
                            $tahap->waktu_sampai_tahap_lelang
                        ),

                    'status' => null,
                ];
            })
            ->values()
            ->all();

        /*
         * Cari tanggal mulai tender
         * berdasarkan tahap pertama yang memiliki startDate.
         */
        $startDate = null;

        foreach ($tahapan as $tahap) {
            if (!$tahap->waktu_mulai_tahap_lelang) {
                continue;
            }

            $startDate = $this->formatDate(
                $tahap->waktu_mulai_tahap_lelang
            );

            break;
        }

        /*
         * Tentukan tahap berdasarkan waktu sekarang.
         */
        $currentStage = $this->getCurrentStage($tahapan);

        return [
            'id_dil' =>
                $lelang->id_dil,

            'id_tender' =>
                $lelang->id_tender,

            /*
             * Sengaja tidak mengirim id_rup.
             */

            'nama_paket' =>
                $lelang->nama_paket,

            'nama_instansi' =>
                $lelang->nama_instansi,

            'lokasi_region' =>
                $lelang->lokasi_region,

            'lokasi_kota' =>
                $lelang->lokasi_kota,

            'lokasi_provinsi' =>
                $lelang->lokasi_provinsi,

            'jenis_pengadaan' =>
                $lelang->jenis_pengadaan,

            'nama_metode_pengadaan' =>
                $lelang->nama_metode_pengadaan,

            'tahun_anggaran' =>
                $lelang->pembebanan_tahun_anggaran,

            'pagu' =>
                $lelang->pagu,

            'nilai_hps' =>
                $lelang->nilai_hps,

            'jenis_kontrak' =>
                $lelang->jenis_kontrak,

            'id_kategori_dil' =>
                $lelang->id_kategori_dil,

            'pic_dil' =>
                $lelang->pic_dil,

            'input_id' =>
                $lelang->input_id,

            'id_metode_kualifikasi' =>
                $lelang->id_metode_kualifikasi,

            'kualifikasi' =>
                $kualifikasi,

            'id_metode_evaluasi' =>
                $lelang->id_metode_evaluasi,

            'id_metode_dokumen' =>
                $lelang->id_metode_dokumen,

            'id_pokja' =>
                $lelang->id_pokja,

            'keterangan_lelang' =>
                $lelang->keterangan_lelang,

            'timeline' => [
                'startDate' =>
                    $startDate,

                'currentStage' =>
                    $currentStage['name'] ?? null,

                'currentStageDetails' =>
                    $currentStage,

                'stages' =>
                    $formattedStages,
            ],
        ];
    }

    /**
     * Menentukan tahap berdasarkan waktu sekarang.
     */
    private function getCurrentStage($tahapan): array
    {
        $now = Carbon::now('Asia/Jakarta');

        $validStages = $tahapan
            ->filter(function ($tahap) {
                return $tahap->waktu_mulai_tahap_lelang
                    && $tahap->waktu_sampai_tahap_lelang;
            })
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

        /*
         * 1. Cari tahap yang sedang berlangsung.
         */
        foreach ($validStages as $tahap) {
            try {
                $start = Carbon::parse(
                    $tahap->waktu_mulai_tahap_lelang,
                    'Asia/Jakarta'
                );

                $end = Carbon::parse(
                    $tahap->waktu_sampai_tahap_lelang,
                    'Asia/Jakarta'
                );

                if ($now->betweenIncluded($start, $end)) {
                    $name =
                        $tahap->nama_tahap
                        ?? $tahap->nama_tahapan_lelang
                        ?? 'Tahapan';

                    return [
                        'id_tahap_lelang' =>
                            $tahap->id_tahap_lelang,

                        'number' =>
                            $tahap->urutan,

                        'name' =>
                            $name,

                        'startDate' =>
                            $this->formatDate(
                                $tahap->waktu_mulai_tahap_lelang
                            ),

                        'endDate' =>
                            $this->formatDate(
                                $tahap->waktu_sampai_tahap_lelang
                            ),

                        'status' =>
                            'Sedang berlangsung',

                        'isCurrentOn' =>
                            true,

                        'activeStages' => [[
                            'number' =>
                                $tahap->urutan,

                            'name' =>
                                $name,

                            'startDate' =>
                                $this->formatDate(
                                    $tahap->waktu_mulai_tahap_lelang
                                ),

                            'endDate' =>
                                $this->formatDate(
                                    $tahap->waktu_sampai_tahap_lelang
                                ),
                        ]],
                    ];
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        /*
         * 2. Kalau semua tahap sudah selesai,
         *    tampilkan tahap terakhir sebagai tahap terakhir.
         */
        $lastStage = $validStages->last();

        try {
            $lastEnd = Carbon::parse(
                $lastStage->waktu_sampai_tahap_lelang,
                'Asia/Jakarta'
            );

            if ($now->greaterThan($lastEnd)) {
                $name =
                    $lastStage->nama_tahap
                    ?? $lastStage->nama_tahapan_lelang
                    ?? 'Tahapan';

                return [
                    'id_tahap_lelang' =>
                        $lastStage->id_tahap_lelang,

                    'number' =>
                        $lastStage->urutan,

                    'name' =>
                        $name,

                    'startDate' =>
                        $this->formatDate(
                            $lastStage->waktu_mulai_tahap_lelang
                        ),

                    'endDate' =>
                        $this->formatDate(
                            $lastStage->waktu_sampai_tahap_lelang
                        ),

                    'status' =>
                        'Selesai',

                    'isCurrentOn' =>
                        false,

                    'activeStages' =>
                        [],
                ];
            }
        } catch (\Throwable $e) {
            // lanjut
        }

        /*
         * 3. Kalau tender belum dimulai,
         *    tampilkan tahap berikutnya.
         */
        foreach ($validStages as $tahap) {
            try {
                $start = Carbon::parse(
                    $tahap->waktu_mulai_tahap_lelang,
                    'Asia/Jakarta'
                );

                if ($now->lessThan($start)) {
                    $name =
                        $tahap->nama_tahap
                        ?? $tahap->nama_tahapan_lelang
                        ?? 'Tahapan';

                    return [
                        'id_tahap_lelang' =>
                            $tahap->id_tahap_lelang,

                        'number' =>
                            $tahap->urutan,

                        'name' =>
                            $name,

                        'startDate' =>
                            $this->formatDate(
                                $tahap->waktu_mulai_tahap_lelang
                            ),

                        'endDate' =>
                            $this->formatDate(
                                $tahap->waktu_sampai_tahap_lelang
                            ),

                        'status' =>
                            'Belum dimulai',

                        'isCurrentOn' =>
                            false,

                        'activeStages' =>
                            [],
                    ];
                }
            } catch (\Throwable $e) {
                continue;
            }
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

    /**
     * Format tanggal untuk frontend.
     */
    private function formatDate($date): ?string
    {
        if (!$date) {
            return null;
        }

        try {
            return Carbon::parse($date)
                ->timezone('Asia/Jakarta')
                ->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }
}