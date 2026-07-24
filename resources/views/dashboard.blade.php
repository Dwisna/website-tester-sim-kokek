@extends('layouts.dashboard')

@section('title', 'RUP Intelligence Dashboard')

@section('main')
    <div class="topbar">
        <div>
            <div class="title">Dashboard</div>
            <div class="subtitle">Integrasi data RUP dari database {{ config('database.connections.'.config('database.default').'.database') ?? 'magang_db' }}</div>
        </div>
            <div class="topbar-actions d-flex align-items-center gap-2">
            @include('components.theme-toggle')
            <a href="{{ route('notifications') }}" class="btn btn-light position-relative" aria-label="Notifications">
                <i class="bi bi-bell-fill"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            </a>
            <div class="badge bg-secondary text-white">Realtime • {{ now()->format('d M Y') }}</div>
        </div>
    </div>

    <section class="hero">
        <div>
            <h2>Portal pengelolaan data RUP yang serius, cepat, dan profesional.</h2>
            <p>Dashboard ini dirancang untuk menampilkan data pengadaan secara terstruktur, siap dipakai untuk eksekutif, tim operasi, dan integrasi API modern.</p>
        </div>
        <div class="pill">Connected to database • magang_db</div>
    </section>

    <section class="stats-grid" id="stats-grid">
        @foreach ($stats as $stat)
            <div class="card {{ $stat['tone'] }}">
                <div class="label">{{ $stat['label'] }}</div>
                <div class="value">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </section>

    <section class="table-wrap" style="margin-top:16px;">
        <div style="display:flex; flex-wrap:wrap; gap:12px; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <div>
                <h3 style="margin:0 0 8px;">Daftar RUP</h3>
                <p class="text-muted" style="margin:0;">Tabel ini menampilkan isi database RUP langsung dari MySQL.</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                <a href="{{ route('rup.download', request()->query()) }}" class="btn btn-outline-primary d-inline-flex align-items-center" style="gap:8px; padding:8px 14px;">
                    <i class="bi bi-download"></i> Download Excel
                </a>
                <form method="GET" action="/" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                    <input class="form-control" type="text" name="search" value="{{ request('search') }}" placeholder="Cari pekerjaan, instansi, id RUP" style="min-width:240px;" />
                    <select class="form-select" name="tahun_anggaran" style="min-width:160px;">
                        <option value="">Semua Tahun</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ request('tahun_anggaran') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary" style="padding:8px 14px;">Filter</button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID RUP</th>
                    <th>Nama Pekerjaan</th>
                    <th>Pagu</th>
                    <th>Metode</th>
                    <th>Instansi</th>
                    <th>Tahun</th>
                    <th>Created</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->id_rup }}</td>
                        <td>{{ $record->nama_pekerjaan }}</td>
                        <td>{{ $record->pagu }}</td>
                        <td>{{ $record->nama_metode_pengadaan }}</td>
                        <td>{{ $record->nama_instansi }}</td>
                        <td>{{ $record->tahun_anggaran }}</td>
                        <td>{{ optional($record->created_at)->format('d M Y') }}</td>
                        <td><a href="{{ route('records.show', $record) }}" class="text-decoration-none">Lihat</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9">Belum ada data yang sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex gap-2 align-items-center mt-3">
            @if ($records->onFirstPage() === false)
                <a href="{{ $records->previousPageUrl() }}" class="btn btn-outline-secondary">Sebelumnya</a>
            @endif
            <span class="text-muted">Halaman {{ $records->currentPage() }} dari {{ $records->lastPage() }}</span>
            @if ($records->hasMorePages())
                <a href="{{ $records->nextPageUrl() }}" class="btn btn-outline-secondary">Selanjutnya</a>
            @endif
        </div>
        </div>
    </section>

    <!-- <section class="card" style="margin-top:16px;">
        <h3 style="margin-top:0;">OpenClaw Preview</h3>
        <p class="text-muted" style="margin-bottom:12px;">Mock interface untuk integrasi scraping data dari OpenClaw.</p>
        <a href="{{ route('openclaw') }}" class="btn-primary">Buka preview</a>
        <div class="preview-box" style="margin-top:16px;">
            <div style="font-size:1.2rem; font-weight:700;">{{ number_format($totalRecords, 0, ',', '.') }} item</div>
            <div class="text-muted" style="margin-top:6px; margin-bottom:12px;">Data ini langsung diambil dari tabel RUP, siap dikirim ke n8n.</div>
            <div style="position:relative; height:220px;">
                <canvas id="chartSeriesCanvas"></canvas>
            </div>
        </div>
    </section> -->

    <section class="chart-box">
        <div class="card">
            <h3 style="margin-top:0;">Trend bulanan</h3>
            <div style="position:relative; height:220px;">
                <canvas id="monthlyTrendCanvas"></canvas>
            </div>
        </div>
        <div class="card">
            <h3 style="margin-top:0;">Status breakdown</h3>
            <div style="position:relative; height:220px;">
                <canvas id="statusBreakdownCanvas"></canvas>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartSeriesData    = @json($chartSeries);
        const monthlySeriesData  = @json($monthlySeries);
        const statusBreakdownData = @json($statusBreakdown);

        const palette = {
            primary: '#6366f1',
            primaryFaint: 'rgba(99, 102, 241, 0.15)',
            secondary: '#22d3ee',
            secondaryFaint: 'rgba(34, 211, 238, 0.15)',
            slices: ['#6366f1', '#22d3ee', '#f59e0b', '#ef4444', '#10b981', '#a855f7'],
            grid: 'rgba(148, 163, 184, 0.15)',
            text: '#94a3b8',
        };

        Chart.defaults.color = palette.text;
        Chart.defaults.font.family = "'Inter', system-ui, sans-serif";

        // --- OpenClaw Preview: bar chart ---
        new Chart(document.getElementById('chartSeriesCanvas'), {
            type: 'bar',
            data: {
                labels: chartSeriesData.map(item => item.label),
                datasets: [{
                    label: 'Jumlah',
                    data: chartSeriesData.map(item => item.value ?? item.bar_height),
                    backgroundColor: palette.primaryFaint,
                    borderColor: palette.primary,
                    borderWidth: 2,
                    borderRadius: 8,
                    maxBarThickness: 36,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: palette.grid } },
                },
            },
        });

        // --- Trend bulanan: line chart ---
        new Chart(document.getElementById('monthlyTrendCanvas'), {
            type: 'line',
            data: {
                labels: monthlySeriesData.map(item => item.month),
                datasets: [{
                    label: 'Trend',
                    data: monthlySeriesData.map(item => item.value ?? item.bar_height),
                    fill: true,
                    tension: 0.4,
                    backgroundColor: palette.secondaryFaint,
                    borderColor: palette.secondary,
                    borderWidth: 3,
                    pointBackgroundColor: palette.secondary,
                    pointRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: palette.grid } },
                },
            },
        });

        // --- Status breakdown: doughnut chart ---
        new Chart(document.getElementById('statusBreakdownCanvas'), {
            type: 'doughnut',
            data: {
                labels: statusBreakdownData.map(item => item.label),
                datasets: [{
                    data: statusBreakdownData.map(item => item.value),
                    backgroundColor: palette.slices,
                    borderWidth: 0,
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 16 },
                    },
                },
            },
        });

        fetch('/api/dashboard')
            .then((response) => response.json())
            .then((payload) => {
                const cards = document.querySelectorAll('#stats-grid .card .value');
                const values = payload?.data?.stats ?? [];
                cards.forEach((node, index) => {
                    if (values[index]) {
                        node.textContent = values[index].value;
                    }
                });
            });
    });
</script>
@endpush