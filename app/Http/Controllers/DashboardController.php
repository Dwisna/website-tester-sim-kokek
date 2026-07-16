<?php

namespace App\Http\Controllers;

use App\Models\RupRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = $this->buildStats();
        $recentRecords = RupRecord::latest('created_at')->take(8)->get();
        $chartSeries = $this->buildChartSeries();
        $monthlySeries = $this->buildMonthlySeries();
        $statusBreakdown = $this->buildStatusBreakdown();

        return view('dashboard', compact('stats', 'recentRecords', 'chartSeries', 'monthlySeries', 'statusBreakdown'));
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
        $mockData = [
            'status' => 'Connected (mock)',
            'last_sync' => now()->subMinutes(9)->format('d M Y, H:i'),
            'items' => 42,
            'summary' => 'Data scraping diproses secara simulasi untuk preview UI.',
        ];

        return view('openclaw', compact('mockData'));
    }

    public function chatApi(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'assistant' => 'Saya siap membantu Anda menelaah data RUP, menganalisis tren pengadaan, dan menyiapkan ringkasan executive dashboard.',
                'suggestions' => [
                    'Ringkas data pengadaan bulan ini',
                    'Tampilkan instansi dengan nilai terbesar',
                    'Jelaskan tren tender dari tahun sebelumnya',
                ],
            ],
        ]);
    }

    public function historyApi(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                ['event' => 'Sinkronisasi data RUP', 'detail' => 'Data diambil dari sumber utama', 'timestamp' => now()->subHours(2)->toDateTimeString()],
                ['event' => 'Pembaruan dashboard', 'detail' => 'Metric dan summary diperbarui', 'timestamp' => now()->subHours(5)->toDateTimeString()],
            ],
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
        return response()->json([
            'success' => true,
            'data' => [
                ['title' => 'Notifikasi sistem', 'message' => 'Ada 12 record baru yang menunggu validasi.', 'priority' => 'high'],
                ['title' => 'Penyedia terdekat', 'message' => 'Terdapat 4 penawaran yang akan segera ditinjau.', 'priority' => 'medium'],
            ],
        ]);
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
        return [
            ['month' => 'Jan', 'value' => 8, 'bar_height' => 64],
            ['month' => 'Feb', 'value' => 12, 'bar_height' => 96],
            ['month' => 'Mar', 'value' => 10, 'bar_height' => 80],
            ['month' => 'Apr', 'value' => 14, 'bar_height' => 112],
            ['month' => 'Mei', 'value' => 18, 'bar_height' => 144],
            ['month' => 'Jun', 'value' => 16, 'bar_height' => 128],
        ];
    }

    private function buildStatusBreakdown(): array
    {
        return [
            ['label' => 'Draft', 'value' => 6],
            ['label' => 'Terkirim', 'value' => 14],
            ['label' => 'Review', 'value' => 9],
            ['label' => 'Selesai', 'value' => 11],
        ];
    }
}
