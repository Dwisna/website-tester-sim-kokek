@extends('layouts.dashboard')

@section('title', 'RUP Intelligence Dashboard')

@section('topnav-title', 'Dashboard')

@section('topnav-breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a>
    <span>/</span>
    <span>Dashboard</span>
@endsection

@section('topnav-description', '')

@section('topnav-search')
    <form method="GET" action="{{ route('dashboard') }}" class="topnav-search-form topnav-search-form-compact">
        <span class="topnav-search-icon">@include('components.icon', ['name' => 'search', 'size' => 16])</span>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pekerjaan, instansi, id RUP" />
        <button type="submit" class="btn-surface btn-surface-compact">Cari</button>
    </form>
@endsection

@section('main')
    <section class="hero dashboard-hero dashboard-summary-hero">
        <div>
            <div class="eyebrow">RUP intelligence</div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Fokus utama di sini adalah ringkasan data, distribusi, dan akses cepat ke detail record.</p>
        </div>
        <div class="hero-meta">
            <div class="pill">Realtime • {{ now()->format('d M Y') }}</div>
            <a href="{{ route('notifications') }}" class="btn-surface bell-link" aria-label="Notifications">
                @include('components.icon', ['name' => 'bell', 'size' => 18]) Notifications
            </a>
        </div>
    </section>

    <section class="stats-grid" id="stats-grid">
        @php
            $statMeta = [
                'Total RUP' => ['icon' => 'speedometer', 'subtitle' => 'Seluruh data yang tersimpan'],
                'Tahun Anggaran' => ['icon' => 'clock', 'subtitle' => 'Rekap tahun berjalan'],
                'Terkirim Penawaran' => ['icon' => 'send', 'subtitle' => 'Status pengiriman aktif'],
                'Prospek Pekerjaan' => ['icon' => 'message', 'subtitle' => 'Peluang yang sedang dipantau'],
                'SIRUP' => ['icon' => 'bell', 'subtitle' => 'Sinkronisasi dan publikasi'],
                'Import Data' => ['icon' => 'download', 'subtitle' => 'Data hasil impor terbaru'],
            ];
        @endphp
        @foreach ($stats as $stat)
            @php($meta = $statMeta[$stat['label']] ?? ['icon' => 'speedometer', 'subtitle' => 'Ringkasan data'])
            <div class="card metric-card {{ $stat['tone'] }}">
                <div class="metric-card-top">
                    <div class="metric-icon metric-icon-{{ $stat['tone'] }}">@include('components.ui.icon', ['name' => $meta['icon'], 'size' => 18])</div>
                    <div class="metric-label">{{ $stat['label'] }}</div>
                </div>
                <div class="metric-value">{{ $stat['value'] }}</div>
                <div class="metric-subtitle">{{ $meta['subtitle'] }}</div>
            </div>
        @endforeach
    </section>

    <section class="table-wrap section-stack section-spacing dashboard-toolbar">
        <div class="toolbar dashboard-toolbar-header">
            <div>
                <h3 class="section-title">Daftar RUP</h3>
                <p class="section-description">Tabel ini menampilkan isi database RUP langsung dari MySQL.</p>
            </div>
            <div class="toolbar-actions">
                <a href="{{ route('rup.download', request()->query()) }}" class="btn-surface">
                    @include('components.icon', ['name' => 'download', 'size' => 16]) Download Excel
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="filter-form dashboard-toolbar-form">
            <div class="toolbar-search">
                <label class="sr-only" for="dashboard-search">Cari data</label>
                <input id="dashboard-search" type="text" name="search" value="{{ request('search') }}" class="form-input field-search" placeholder="Cari pekerjaan, instansi, atau ID RUP" />
            </div>

            <div class="toolbar-filter">
                <label class="sr-only" for="dashboard-year">Tahun anggaran</label>
                <select id="dashboard-year" name="tahun_anggaran" class="form-select field-year" aria-label="Tahun anggaran">
                    <option value="">Semua Tahun</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" {{ request('tahun_anggaran') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            @isset($statuses)
                <div class="toolbar-filter">
                    <select name="status" class="form-select field-year" aria-label="Status">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            @endisset

            @isset($agencies)
                <div class="toolbar-filter">
                    <select name="agency" class="form-select field-year" aria-label="Agency">
                        <option value="">Semua Agency</option>
                        @foreach ($agencies as $agency)
                            <option value="{{ $agency }}" {{ request('agency') == $agency ? 'selected' : '' }}>{{ $agency }}</option>
                        @endforeach
                    </select>
                </div>
            @endisset

            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('rup.download', request()->query()) }}" class="btn-surface">
                @include('components.icon', ['name' => 'download', 'size' => 16]) Download Excel
            </a>
            <a href="{{ url()->full() }}" class="btn-surface">
                @include('components.icon', ['name' => 'clock', 'size' => 16]) Refresh
            </a>
        </form>

        <div class="table-responsive dashboard-table">
            <table class="table align-middle">
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

            <div class="pagination pagination-row">
                @if ($records->onFirstPage() === false)
                    <a href="{{ $records->previousPageUrl() }}" class="btn-surface">Sebelumnya</a>
                @endif
                <span class="text-muted">Halaman {{ $records->currentPage() }} dari {{ $records->lastPage() }}</span>
                @if ($records->hasMorePages())
                    <a href="{{ $records->nextPageUrl() }}" class="btn-surface">Selanjutnya</a>
                @endif
            </div>
        </div>
    </section>

    <section class="chart-grid">
        <div class="card chart-card">
            <h3 class="section-title">Trend bulanan</h3>
            <div class="chart-frame">
                <canvas id="monthlyTrendCanvas"></canvas>
            </div>
        </div>
        <div class="card chart-card">
            <h3 class="section-title">Status breakdown</h3>
            <div class="chart-frame">
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
            primary: '#2f6fed',
            primaryFaint: 'rgba(47, 111, 237, 0.14)',
            secondary: '#0f9f6e',
            secondaryFaint: 'rgba(15, 159, 110, 0.14)',
            slices: ['#2f6fed', '#0f9f6e', '#d98b08', '#dc4c64', '#7c3aed', '#0ea5e9'],
            grid: 'rgba(148, 163, 184, 0.16)',
            text: getComputedStyle(document.documentElement).getPropertyValue('--muted').trim() || '#94a3b8',
        };

        Chart.defaults.color = palette.text;
        Chart.defaults.font.family = "'Manrope', 'Inter', system-ui, sans-serif";

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