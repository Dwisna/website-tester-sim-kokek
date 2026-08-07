<?php

namespace App\Http\Controllers;

use App\Exports\RupExport;
use App\Models\N8nWebhookLog;
use App\Models\RupRecord;
use App\Models\SystemNotification;
use App\Repositories\SirupRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Halaman utama dashboard.
     *
     * Menampilkan statistik ringkas, daftar RUP, dan filter tahun.
     */
    public function index(Request $request)
    {
        $query = RupRecord::query();

        if ($request->filled('search')) {
            $query->where('nama_pekerjaan', 'like', '%'.$request->search.'%')
                ->orWhere('nama_instansi', 'like', '%'.$request->search.'%')
                ->orWhere('id_rup', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('tahun_anggaran')) {
            $query->where('tahun_anggaran', $request->tahun_anggaran);
        }

        try {
            $records = $query->orderByDesc('created_at')->paginate(request('per_page', 10))->withQueryString();
            $minYear = RupRecord::whereNotNull('tahun_anggaran')->min('tahun_anggaran');
            $maxYear = RupRecord::whereNotNull('tahun_anggaran')->max('tahun_anggaran');
            if ($minYear && $maxYear) {
                $years = collect(range($maxYear, $minYear, -1));
            } else {
                $years = collect(range(2028, 2021, -1));
            }

            $stats = $this->buildStats();
            $totalRecords = RupRecord::count();
            $chartSeries = $this->buildChartSeries();
            $monthlySeries = $this->buildMonthlySeries();
            $statusBreakdown = $this->buildStatusBreakdown();
            $dashboardSummary = $this->buildDashboardSummary();

            return view('dashboard', compact('stats', 'records', 'years', 'chartSeries', 'monthlySeries', 'statusBreakdown', 'totalRecords', 'dashboardSummary'));
        } catch (\Throwable $e) {
            Log::error('Dashboard index error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            $page = (int) $request->get('page', 1);
            $perPage = 10;
            $records = new LengthAwarePaginator([], 0, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
            $years = collect();
            $stats = [];
            $totalRecords = 0;
            $chartSeries = [];
            $monthlySeries = [];
            $statusBreakdown = [];
            $dashboardSummary = [];

            return view('dashboard', compact('stats', 'records', 'years', 'chartSeries', 'monthlySeries', 'statusBreakdown', 'totalRecords', 'dashboardSummary'))
                ->with('error_message', 'Terjadi kesalahan saat memuat dashboard: '.$e->getMessage());
        }
    }

    /**
     * API untuk data dashboard (dipanggil oleh frontend untuk update realtime).
     * Mengembalikan statistik, entri terbaru, dan seri chart.
     */
    public function dashboardApi(Request $request): JsonResponse
    {
        try {
            $query = RupRecord::query();

            // search across nama_pekerjaan, nama_instansi, id_rup
            if ($request->filled('search')) {
                $q = $request->input('search');
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama_pekerjaan', 'like', "%{$q}%")
                        ->orWhere('nama_instansi', 'like', "%{$q}%")
                        ->orWhere('id_rup', 'like', "%{$q}%");
                });
            }

            if ($request->filled('tahun_anggaran')) {
                $query->where('tahun_anggaran', $request->input('tahun_anggaran'));
            }

            // range filter: today/week/month/all
            $range = $request->input('range', 'all');
            if (in_array($range, ['today', 'week', 'month'], true)) {
                $now = now();
                if ($range === 'today') {
                    $query->whereDate('created_at', $now->toDateString());
                } elseif ($range === 'week') {
                    $start = $now->startOfWeek();
                    $end = $now->endOfWeek();
                    $query->whereBetween('created_at', [$start, $end]);
                } elseif ($range === 'month') {
                    $start = $now->startOfMonth();
                    $end = $now->endOfMonth();
                    $query->whereBetween('created_at', [$start, $end]);
                }
            }

            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);
            $allowed = [10, 25, 50, 100];
            if (!in_array($perPage, $allowed, true)) {
                $perPage = 10;
            }
            $records = $query->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page)->withQueryString();

            $recordsData = $records->map(function ($record) {
                return [
                    'id' => $record->id,
                    'id_rup' => $record->id_rup,
                    'nama_pekerjaan' => $record->nama_pekerjaan,
                    'pagu' => $record->pagu,
                    'nama_metode_pengadaan' => $record->nama_metode_pengadaan,
                    'nama_instansi' => $record->nama_instansi,
                    'tahun_anggaran' => $record->tahun_anggaran,
                    'created_at' => $record->created_at?->toDateTimeString(),
                    'created_at_display' => $record->created_at?->setTimezone('Asia/Jakarta')->format('d M Y H:i'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $this->buildStats(),
                    'summary' => $this->buildDashboardSummary(),
                    'records' => $recordsData,
                    'pagination' => [
                        'current_page' => $records->currentPage(),
                        'last_page' => $records->lastPage(),
                        'per_page' => $records->perPage(),
                        'total' => $records->total(),
                        'from' => $records->firstItem(),
                        'to' => $records->lastItem(),
                        'next_page_url' => $records->nextPageUrl(),
                        'prev_page_url' => $records->previousPageUrl(),
                    ],
                    'chart_series' => $this->buildChartSeries(),
                    'monthly_series' => $this->buildMonthlySeries(),
                    'status_breakdown' => $this->buildStatusBreakdown(),
                    'unread_notifications' => SystemNotification::where('is_read', 0)->count(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('dashboardApi error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data dashboard: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan halaman detail untuk satu record RUP.
     */
    public function showRecord(RupRecord $record)
    {
        return view('record-detail', compact('record'));
    }

    /**
     * Halaman mock OpenClaw untuk demo integrasi scraping dan preview data.
     */
    public function openclawPage()
    {
        $lastRecord = RupRecord::latest('created_at')->first();
        $mockData = [
            'status' => RupRecord::count() ? 'Connected • data ready' : 'Connected • no data',
            'last_sync' => $lastRecord ? $lastRecord->created_at->format('d M Y, H:i') : now()->format('d M Y, H:i'),
            'items' => RupRecord::count(),
            'summary' => 'Data RUP diambil langsung dari tabel utama, siap dikirim ke n8n dan diproses lebih lanjut.',
        ];

        $chatMessages = [
            ['role' => 'assistant', 'text' => 'Halo! Saya OpenClaw mock. Silakan tanyakan mengenai data RUP atau status import.'],
            ['role' => 'user', 'text' => 'Tampilkan ringkasan data terbaru.'],
        ];

        return view('openclaw', compact('mockData', 'chatMessages'));
    }

    /**
     * Endpoint API sederhana untuk chat demo.
     * Menerima pesan dan mengembalikan balasan teks (mock).
     */
    public function chatApi(Request $request): JsonResponse
    {
        $message = $request->input('message', 'Halo');
        $responseText = $this->buildChatResponse($message);

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $responseText,
            ],
        ]);
    }

    /**
     * Halaman history yang menampilkan log webhook n8n terbaru.
     */
    public function historyPage()
    {
        try {
            $history = N8nWebhookLog::latest('created_at')->take(20)->get();
            return view('history', compact('history'));
        } catch (\Throwable $e) {
            Log::error('historyPage error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $history = collect();
            return view('history', compact('history'))->with('error_message', 'Terjadi kesalahan saat memuat history: '.$e->getMessage());
        }
    }

    /**
     * API yang mengembalikan ringkasan history untuk konsumsi frontend.
     */
    public function historyApi(): JsonResponse
    {
        try {
            $history = N8nWebhookLog::latest('created_at')->take(10)->get()->map(function ($item) {
                return [
                    'event' => $item->event ?? 'webhook',
                    'detail' => $item->message ?? 'Pesan diterima',
                    'timestamp' => $item->created_at?->toDateTimeString(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Throwable $e) {
            Log::error('historyApi error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil history: '.$e->getMessage()], 500);
        }
    }

    /**
     * API placeholder untuk mendownload/menyiapkan file export dashboard.
     */
    public function downloadApi(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'file' => 'dashboard-export.xlsx',
                'url' => '/api/download/preview',
                'message' => 'File siap diunduh setelah diproses.',
            ],
        ]);
    }

    /**
     * API yang mengembalikan daftar notifikasi sistem terbaru.
     */
    public function notificationsApi(): JsonResponse
    {
        try {
            $notifications = SystemNotification::latest('created_at')->take(20)->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'message' => $item->message,
                    'type' => $item->type,
                    'priority' => $item->priority,
                    'link' => $item->link,
                    'is_read' => $item->is_read,
                    'created_at' => $item->created_at?->toDateTimeString(),
                    'created_at_display' => $item->created_at?->setTimezone('Asia/Jakarta')->format('d M Y H:i'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $notifications,
            ]);
        } catch (\Throwable $e) {
            Log::error('notificationsApi error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil notifikasi: '.$e->getMessage()], 500);
        }
    }

    /**
     * Halaman notifikasi untuk melihat daftar notifikasi sistem.
     */
    public function notificationsPage()
    {
        try {
            $notifications = SystemNotification::latest('created_at')->take(20)->get();
            return view('notifications', compact('notifications'));
        } catch (\Throwable $e) {
            Log::error('notificationsPage error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $notifications = collect();
            return view('notifications', compact('notifications'))->with('error_message', 'Terjadi kesalahan saat memuat notifikasi: '.$e->getMessage());
        }
    }

    /**
     * Endpoint untuk menerima webhook dari n8n.
     * Mencatat payload ke log dan membuat SystemNotification.
     */
    public function n8nWebhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            $message = $payload['message'] ?? 'Pesan masuk tanpa isi';
            $customer = $payload['customer'] ?? 'unknown';
            $event = $payload['event'] ?? 'customer_message';
            $channel = $payload['channel'] ?? 'web';

            N8nWebhookLog::create([
                'source' => $payload['source'] ?? 'n8n',
                'event' => $event,
                'channel' => $channel,
                'payload' => $payload,
                'message' => $message,
                'customer' => $customer,
                'status' => 'accepted',
            ]);
            $notification = SystemNotification::where('source', $payload['source'] ?? 'n8n')
    ->latest()
    ->first();

if ($notification) {
    // Update notifikasi yang sudah ada
    $notification->update([
        'title' => $payload['title'] ?? 'Sinkronisasi Data',
        'message' => $message,
        'type' => 'n8n',
        'priority' => $payload['priority'] ?? 'medium',
        'link' => $payload['link'] ?? null,
        'payload' => $payload,
        'is_read' => false,
    ]);
} else {
    // Buat notifikasi pertama
    SystemNotification::create([
        'title' => $payload['title'] ?? 'Sinkronisasi Data',
        'message' => $message,
        'type' => 'n8n',
        'priority' => $payload['priority'] ?? 'medium',
        'link' => $payload['link'] ?? null,
        'source' => $payload['source'] ?? 'n8n',
        'payload' => $payload,
        'is_read' => false,
    ]);
}

            // SystemNotification::create([
            //     'title' => $payload['title'] ?? ucfirst(str_replace('_', ' ', $event)),
            //     'message' => $message,
            //     'type' => 'n8n',
            //     'priority' => $payload['priority'] ?? 'medium',
            //     'link' => $payload['link'] ?? null,
            //     'source' => $payload['source'] ?? 'n8n',
            //     'payload' => $payload,
            //     'is_read' => false,
            // ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'accepted',
                    'message' => 'Webhook diterima dan disimpan sebelum diproses lebih lanjut.',
                    'event' => $event,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('n8nWebhook error: '.$e->getMessage(), ['trace' => $e->getTraceAsString(), 'payload' => $request->all()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memproses webhook: '.$e->getMessage()], 500);
        }
    }

    /**
     * Endpoint utama untuk n8n inject data RUP ke database.
     *
     * Body JSON:
     * {
     *   "source": "n8n",
     *   "event": "n8n_import",
     *   "records": [ { "id_rup": "...", "nama_pekerjaan": "...", ... } ]
     * }
     *
     * Atau kirim file (multipart/form-data) di field "file" (.json / .csv)
     */
    /**
     * Memproses import data RUP dari n8n atau upload file.
     *
     * Fungsionalitas:
     * - Normalisasi field
     * - Deteksi duplikat (id_rup atau nama+instansi+tahun)
     * - Update / create record sesuai kondisi
     * - Dibungkus dalam transaksi DB untuk atomicitas
     */
    public function n8nImport(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            $recordPayload = $payload['records'] ?? null;

            // Kalau records dikirim sebagai JSON string (bukan array), decode dulu
            if (is_string($recordPayload)) {
                $decoded = json_decode($recordPayload, true);
                $recordPayload = is_array($decoded) ? $decoded : [];
            }

            // Kalau field "records" tidak dikirim sama sekali, tapi payload
            // punya "id_rup" langsung di root, anggap itu 1 record tunggal (flat object).
            if ($recordPayload === null && isset($payload['id_rup'])) {
                $recordPayload = [$payload];
            }

            $recordPayload = $recordPayload ?? [];

            if ($request->hasFile('file')) {
                $recordPayload = array_merge($recordPayload, $this->parseN8nImportFile($request->file('file')));
            }

            if (!is_array($recordPayload) || count($recordPayload) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payload tidak berisi data RUP untuk diimpor. Pastikan mengirim field "records" berupa array of object.',
                ], 422);
            }

            $created = 0;
            $updated = 0;
            $skipped = 0;
            $errors = [];

            try {
                foreach ($recordPayload as $i => $recordData) {
                    try {
                        if (!is_array($recordData)) {
                            $skipped++;
                            $errors[] = "Index {$i}: data bukan object/array, dilewati.";
                            continue;
                        }

                        $normalized = $this->normalizeRupData($recordData);

                        // Validasi minimal: harus ada id_rup atau (nama_pekerjaan + nama_instansi)
                        if (empty($normalized['id_rup']) && (empty($normalized['nama_pekerjaan']) || empty($normalized['nama_instansi']))) {
                            $skipped++;
                            $errors[] = "Index {$i}: field 'id_rup' kosong dan tidak cukup data untuk deteksi/penyimpanan, dilewati.";
                            continue;
                        }

                        if (!empty($normalized['id_rup'])) {
                            $existingById = RupRecord::where('id_rup', $normalized['id_rup'])->first();
                            if ($existingById) {
                                $existingById->fill($normalized);
                                if ($existingById->isDirty()) {
                                    $existingById->save();
                                    $updated++;
                                }
                                continue;
                            }
                        }

                        // Jika belum ada id_rup, cek duplikat berdasar nama+instansi+tahun
                        $dupQuery = RupRecord::query();
                        if (!empty($normalized['nama_pekerjaan']) && !empty($normalized['nama_instansi'])) {
                            $namaP = trim(mb_strtolower($normalized['nama_pekerjaan']));
                            $namaI = trim(mb_strtolower($normalized['nama_instansi']));

                            $dupQuery->whereRaw('LOWER(TRIM(nama_pekerjaan)) = ?', [$namaP])
                                ->whereRaw('LOWER(TRIM(nama_instansi)) = ?', [$namaI]);

                            if (!empty($normalized['tahun_anggaran'])) {
                                $dupQuery->where('tahun_anggaran', $normalized['tahun_anggaran']);
                            }

                            if ($dupQuery->exists()) {
                                $skipped++;
                                $errors[] = "Index {$i}: menemukan record serupa (nama_instansi/nama_pekerjaan/tahun), dilewati untuk mencegah duplikasi.";
                                continue;
                            }
                        }

                        // Buat record baru
                        $record = RupRecord::create($normalized);
                        if ($record) {
                            $created++;
                        } else {
                            $skipped++;
                            $errors[] = "Index {$i}: gagal membuat record baru.";
                        }
                    } catch (\Throwable $e) {
                        $skipped++;
                        $errors[] = "Index {$i}: {$e->getMessage()}";
                        Log::warning('n8nImport row failed', ['index' => $i, 'error' => $e->getMessage(), 'payload' => $recordData]);
                    }
                }
            } catch (\Throwable $e) {
                Log::error('n8nImport processing failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses import: '.$e->getMessage(),
                ], 500);
            }

            $event = $payload['event'] ?? 'n8n_import';
            $message = $payload['message'] ?? "Import selesai: {$created} baru, {$updated} diperbarui, {$skipped} dilewati.";

            N8nWebhookLog::create([
                'source' => $payload['source'] ?? 'n8n',
                'event' => $event,
                'channel' => $payload['channel'] ?? 'api',
                'payload' => $payload,
                'message' => $message,
                'customer' => $payload['customer'] ?? 'system',
                'status' => 'imported',
            ]);

            SystemNotification::create([
                'title' => $payload['title'] ?? 'Import data n8n',
                'message' => $message,
                'type' => 'n8n_import',
                'priority' => $payload['priority'] ?? 'high',
                'link' => $payload['link'] ?? null,
                'source' => $payload['source'] ?? 'n8n',
                'payload' => $payload,
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'created' => $created,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'errors' => $errors,
                    'message' => $message,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('n8nImport error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses import: '.$e->getMessage(),
            ], 500);
        }
    }

    private function parseN8nImportFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $content = file_get_contents($file->getRealPath());

        if ($extension === 'json') {
            $data = json_decode($content, true);
            return is_array($data) ? $data : [];
        }

        if ($extension === 'csv') {
            $lines = array_filter(array_map('trim', explode("\n", $content)));
            $rows = array_map('str_getcsv', $lines);
            $header = array_map('trim', $rows[0] ?? []);
            $result = [];

            foreach (array_slice($rows, 1) as $row) {
                // Lindungi dari baris CSV yang jumlah kolomnya tidak sama dengan header
                if (count($row) !== count($header)) {
                    continue;
                }
                $result[] = array_combine($header, $row);
            }

            return $result;
        }

        return [];
    }

    /**
     * Field yang boleh diisi lewat n8n, sesuai $fillable di model RupRecord.
     * Field boolean (is_*) otomatis dinormalisasi jadi 0/1.
     */
    private function normalizeRupData(array $record): array
    {
        $model = new RupRecord();
        $fillable = $model->getFillable();
        $normalized = [];

        $booleanFields = ['is_sirup', 'is_import', 'is_pekerjaan_prospek', 'is_status_kirim_penawaran'];

        foreach ($fillable as $field) {
            if (!array_key_exists($field, $record)) {
                continue;
            }

            $value = $record[$field];

            if (in_array($field, $booleanFields, true)) {
                $normalized[$field] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                continue;
            }

            // Normalisasi string: trim whitespace
            if (is_string($value)) {
                $value = trim($value);
            }

            $normalized[$field] = $value;
        }

        if (isset($record['created_at'])) {
            $normalized['created_at'] = $record['created_at'];
        }

        if (isset($record['updated_at'])) {
            $normalized['updated_at'] = $record['updated_at'];
        }

        return $normalized;
    }

    private function buildStats(): array
    {
        $total = RupRecord::count();

        $todayCount = RupRecord::whereDate('created_at', Carbon::today())->count();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $weekCount = RupRecord::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $monthCount = RupRecord::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        return [
            ['label' => 'Total RUP', 'value' => number_format($total, 0, ',', '.'), 'tone' => 'primary'],
            ['label' => 'Hari Ini', 'value' => number_format($todayCount, 0, ',', '.'), 'tone' => 'accent'],
            ['label' => 'Minggu Ini', 'value' => number_format($weekCount, 0, ',', '.'), 'tone' => 'accent'],
            ['label' => 'Bulan Ini', 'value' => number_format($monthCount, 0, ',', '.'), 'tone' => 'accent'],
        ];
    }

    private function buildChartSeries(): array
    {
        return RupRecord::selectRaw('tahun_anggaran as label, count(*) as total')
            ->whereNotNull('tahun_anggaran')
            ->groupBy('tahun_anggaran')
            ->orderBy('tahun_anggaran')
            ->limit(8)
            ->get()
            ->map(fn ($row) => [
                'label' => $row->label,
                'value' => (int) $row->total,
                'bar_height' => max(20, min(160, ((int) $row->total) * 30)),
            ])
            ->values()
            ->all();
    }

    private function buildMonthlySeries(): array
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $label = $date->format('M');
            $count = RupRecord::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $months->push(['month' => $label, 'value' => $count, 'bar_height' => max(24, min(160, $count * 12))]);
        }

        return $months->all();
    }

    private function buildStatusBreakdown(): array
    {
        $total = RupRecord::count();
        $sent = RupRecord::where('is_status_kirim_penawaran', 1)->count();
        $imported = RupRecord::where('is_import', 1)->count();
        $sirup = RupRecord::where('is_sirup', 1)->count();
        $draft = max(0, $total - ($sent + $imported + $sirup));

        return [
            ['label' => 'Draft', 'value' => $draft],
            ['label' => 'Terkirim', 'value' => $sent],
            ['label' => 'Review', 'value' => $imported],
            ['label' => 'Selesai', 'value' => $sirup],
        ];
    }

    private function buildDashboardSummary(): array
    {
        return app(SirupRepository::class)->summary();
    }

    private function buildChatResponse(string $message): string
    {
        $messageLower = strtolower($message);

        if (str_contains($messageLower, 'ringkas')) {
            return 'Saat ini terdapat ' . RupRecord::count() . ' record dalam database Anda. Data terbaru muncul di dashboard tabel.';
        }

        if (str_contains($messageLower, 'instansi')) {
            $top = RupRecord::selectRaw('nama_instansi, count(*) as total')
                ->groupBy('nama_instansi')
                ->orderByDesc('total')
                ->first();

            return $top
                ? 'Instansi dengan item terbanyak adalah ' . $top->nama_instansi . ' dengan ' . $top->total . ' record.'
                : 'Data instansi belum tersedia.';
        }

        if (str_contains($messageLower, 'trend')) {
            $thisYear = (string) date('Y');
            $countThisYear = RupRecord::where('tahun_anggaran', $thisYear)->count();
            return 'Tren saat ini menunjukkan ' . $countThisYear . ' RUP untuk tahun anggaran ' . $thisYear . ', dengan aktivitas tertinggi pada kuartal terakhir.';
        }

        return 'Ini adalah respons OpenClaw mock. Silakan beri perintah seperti "Ringkas data terbaru" atau "Tampilkan status import".';
    }
    
    public function download(Request $request)
    {
        return Excel::download(
        new RupExport($request->query('search'), $request->query('tahun_anggaran')),
        'data-rup-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}