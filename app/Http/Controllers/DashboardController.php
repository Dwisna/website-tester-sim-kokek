<?php

namespace App\Http\Controllers;

use App\Models\N8nWebhookLog;
use App\Models\RupRecord;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

class DashboardController extends Controller
{
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

        $records = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        $years = RupRecord::select('tahun_anggaran')
            ->whereNotNull('tahun_anggaran')
            ->groupBy('tahun_anggaran')
            ->orderBy('tahun_anggaran', 'desc')
            ->pluck('tahun_anggaran');

        $stats = $this->buildStats();
        $totalRecords = RupRecord::count();
        $chartSeries = $this->buildChartSeries();
        $monthlySeries = $this->buildMonthlySeries();
        $statusBreakdown = $this->buildStatusBreakdown();

        return view('dashboard', compact('stats', 'records', 'years', 'chartSeries', 'monthlySeries', 'statusBreakdown', 'totalRecords'));
    }

    public function dashboardApi(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $this->buildStats(),
                'recent_records' => RupRecord::latest('created_at')->take(8)->get()->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'nama_pekerjaan' => $record->nama_pekerjaan,
                        'nama_instansi' => $record->nama_instansi,
                        'tahun_anggaran' => $record->tahun_anggaran,
                        'created_at' => $record->created_at?->format('Y-m-d H:i'),
                    ];
                }),
                'chart_series' => $this->buildChartSeries(),
                'monthly_series' => $this->buildMonthlySeries(),
                'status_breakdown' => $this->buildStatusBreakdown(),
            ],
        ]);
    }

    public function showRecord(RupRecord $record)
    {
        return view('record-detail', compact('record'));
    }

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

    public function historyPage()
    {
        $history = N8nWebhookLog::latest('created_at')->take(20)->get();

        return view('history', compact('history'));
    }

    public function historyApi(): \Illuminate\Http\JsonResponse
    {
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
    }

    public function downloadApi(): \Illuminate\Http\JsonResponse
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

    public function notificationsApi(): \Illuminate\Http\JsonResponse
    {
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
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    public function notificationsPage()
    {
        $notifications = SystemNotification::latest('created_at')->take(20)->get();

        return view('notifications', compact('notifications'));
    }

    public function n8nWebhook(Request $request): JsonResponse
    {
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

        SystemNotification::create([
            'title' => $payload['title'] ?? ucfirst(str_replace('_', ' ', $event)),
            'message' => $message,
            'type' => 'n8n',
            'priority' => $payload['priority'] ?? 'medium',
            'link' => $payload['link'] ?? null,
            'source' => $payload['source'] ?? 'n8n',
            'payload' => $payload,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'status' => 'accepted',
                'message' => 'Webhook diterima dan disimpan sebelum diproses lebih lanjut.',
                'event' => $event,
            ],
        ]);
    }

    public function n8nImport(Request $request): JsonResponse
    {
        $payload = $request->all();
        $recordPayload = $payload['records'] ?? [];

        if ($request->hasFile('file')) {
            $recordPayload = array_merge($recordPayload, $this->parseN8nImportFile($request->file('file')));
        }

        if (!is_array($recordPayload) || count($recordPayload) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Payload tidak berisi data RUP untuk diimpor.',
            ], 422);
        }

        $created = 0;
        $updated = 0;
        foreach ($recordPayload as $recordData) {
            if (!is_array($recordData)) {
                continue;
            }

            $normalized = $this->normalizeRupData($recordData);
            if (empty($normalized['id_rup'])) {
                continue;
            }

            $record = RupRecord::updateOrCreate([
                'id_rup' => $normalized['id_rup'],
            ], $normalized);

            if ($record->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $event = $payload['event'] ?? 'n8n_import';
        $message = $payload['message'] ?? "Import selesai: {$created} baru, {$updated} diperbarui.";

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
                'message' => $message,
            ],
        ]);
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
                $result[] = array_combine($header, $row);
            }
            return $result;
        }

        return [];
    }

    private function normalizeRupData(array $record): array
    {
        $model = new RupRecord();
        $fillable = $model->getFillable();
        $normalized = [];

        foreach ($fillabe as $field) {
            if (array_key_exists($field, $record)) {
                $normalized[$field] = $record[$field];
            }
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
        $thisYear = (string) date('Y');
        $currentYear = RupRecord::where('tahun_anggaran', $thisYear)->count();
        $sent = RupRecord::where('is_status_kirim_penawaran', 1)->count();
        $prospect = RupRecord::where('is_pekerjaan_prospek', 1)->count();
        $sirup = RupRecord::where('is_sirup', 1)->count();
        $imported = RupRecord::where('is_import', 1)->count();

        return [
            ['label' => 'Total RUP', 'value' => number_format($total, 0, ',', '.'), 'tone' => 'primary'],
            ['label' => 'Tahun Anggaran', 'value' => number_format($currentYear, 0, ',', '.'), 'tone' => 'accent'],
            ['label' => 'Terkirim Penawaran', 'value' => number_format($sent, 0, ',', '.'), 'tone' => 'success'],
            ['label' => 'Prospek Pekerjaan', 'value' => number_format($prospect, 0, ',', '.'), 'tone' => 'warning'],
            ['label' => 'SIRUP', 'value' => number_format($sirup, 0, ',', '.'), 'tone' => 'info'],
            ['label' => 'Import Data', 'value' => number_format($imported, 0, ',', '.'), 'tone' => 'neutral'],
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
            $months->push([ 'month' => $label, 'value' => $count, 'bar_height' => max(24, min(160, $count * 12)) ]);
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
}
