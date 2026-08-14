@extends('layouts.dashboard')

@section('title', 'RUP Intelligence Dashboard')

@section('topnav-title', 'Dashboard')

@section('topnav-breadcrumb')
    <a href="{{ route('dashboard') }}">Home</a>
    <span>/</span>
    <span>Dashboard</span>
@endsection

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

@section('topnav-description', '')

@section('topnav-search')
    
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
                <a href="{{ url()->full() }}" class="btn-surface">
                @include('components.icon', ['name' => 'clock', 'size' => 16]) Refresh
            </a>
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
                <label class="sr-only" for="dashboard-per-page">Tampilkan</label>
                <select id="dashboard-per-page" name="per_page" class="form-select per-page-select" aria-label="Tampilkan">
                 @foreach ([10, 25, 50, 100] as $perPageOption)
                    <option value="{{ $perPageOption }}" {{ (int) request('per_page', 10) === $perPageOption ? 'selected' : '' }}> {{ $perPageOption }}</option>
                @endforeach
        </select>
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

            <div class="toolbar-filter">
                <label class="sr-only" for="dashboard-range">Rentang waktu</label>
                <select id="dashboard-range" name="range" class="form-select field-range" aria-label="Rentang waktu">
                    <option value="all" {{ request('range') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="today" {{ request('range') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ request('range') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="month" {{ request('range') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                </select>
            </div>
            
            <div class="toolbar-filter date-range-picker">
                <button type="button" class="date-range-trigger" id="date-range-trigger">
                    <span id="date-range-text">Pilih tanggal</span>
                </button>

                <div class="date-range-popup" id="date-range-popup">

                    <div class="date-range-header">
                        <div>
                            <div class="date-range-title">Pilih tanggal</div>
                            <div class="date-range-hint" id="date-range-hint">
                                Pilih tanggal mulai
                            </div>
                        </div>

                        <button type="button" class="date-range-close" id="date-range-close">
                            ×
                        </button>
                    </div>

                    <div class="date-range-selected">

                        <div class="date-selection-box active" id="start-selection">
                            <span class="selection-label">Tanggal Mulai</span>
                            <strong id="start-date-text">Pilih tanggal</strong>
                        </div>

                        <div class="date-selection-arrow">
                            →
                        </div>

                        <div class="date-selection-box" id="end-selection">
                            <span class="selection-label">Tanggal Selesai</span>
                            <strong id="end-date-text">Opsional</strong>
                        </div>

                    </div>

                    <div class="calendar">

                        <div class="calendar-header">

                            <button type="button" class="calendar-nav" id="prev-month">
                                ‹
                            </button>

                            <strong id="calendar-month">
                                Agustus 2026
                            </strong>

                            <button type="button" class="calendar-nav" id="next-month">
                                ›
                            </button>

                        </div>

                        <div class="calendar-weekdays">
                            <span>Sen</span>
                            <span>Sel</span>
                            <span>Rab</span>
                            <span>Kam</span>
                            <span>Jum</span>
                            <span>Sab</span>
                            <span>Min</span>
                        </div>

                        <div class="calendar-days" id="calendar-days"></div>

                    </div>

                    <div class="date-range-footer">

                        <button type="button" class="date-reset" id="date-reset">
                            Reset
                        </button>

                        <button type="button" class="date-apply" id="date-apply">
                            Terapkan
                        </button>

                    </div>

                </div>

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

            
        </form>

        <div class="table-responsive dashboard-table">
            <div id="dashboard-loading" style="display:none;position:absolute;inset:0;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(2px);z-index:10;">
                <div class="loader" style="background:rgba(255,255,255,0.9);padding:12px 16px;border-radius:8px;display:flex;align-items:center;gap:8px;box-shadow:0 6px 18px rgba(0,0,0,0.08);">
                    <div style="width:18px;height:18px;border-radius:50%;border:2px solid #cbd5e1;border-top-color:#2f6fed;animation:spin 0.8s linear infinite"></div>
                    <div style="font-size:13px;color:#334155">Memuat data...</div>
                </div>
            </div>
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
                <tbody id="records-body">
                    @foreach ($records as $record)
                        <tr data-name="{{ strtolower($record->nama_pekerjaan ?? '') }}" data-id="{{ strtolower($record->id_rup ?? '') }}" data-agency="{{ strtolower($record->nama_instansi ?? '') }}" data-year="{{ $record->tahun_anggaran }}" data-created="{{ $record->created_at?->toIso8601String() }}">
                            <td>{{ $record->id }}</td>
                            <td>{{ $record->id_rup }}</td>
                            <td>{{ $record->nama_pekerjaan }}</td>
                            <td> Rp {{ number_format($record->pagu, 0, ',', '.') }}</td>
                            <td>{{ $record->nama_metode_pengadaan }}</td>
                            <td>{{ $record->nama_instansi }}</td>
                            <td>{{ $record->tahun_anggaran }}</td>
                            <td>{{ optional($record->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
                            <td><a href="{{ route('records.show', $record) }}" class="text-decoration-none">Lihat</a></td>
                        </tr>
                    @endforeach
                    <tr id="no-results" style="display: none;"><td colspan="9">Belum ada data yang sesuai filter.</td></tr>
                </tbody>
            </table>

            <div class="pagination-row">

    <div class="pagination-info">
        Halaman {{ $records->currentPage() }} dari {{ $records->lastPage() }}
    </div>

    <div class="pagination-links">

        @if(!$records->onFirstPage())
            <a href="{{ $records->previousPageUrl() }}" class="page-btn">
                Sebelumnya
            </a>
        @endif

        @for($i = 1; $i <= $records->lastPage(); $i++)

            @if($i == $records->currentPage())

                <span class="page-number active">{{ $i }}</span>

            @elseif(
                $i == 1 ||
                $i == $records->lastPage() ||
                abs($i - $records->currentPage()) <= 2
            )

                <a href="{{ $records->url($i) }}" class="page-number">{{ $i }}</a>

            @elseif(
                $i == $records->currentPage()-3 ||
                $i == $records->currentPage()+3
            )

                <span class="page-number dots">...</span>

            @endif

        @endfor

        @if($records->hasMorePages())
            <a href="{{ $records->nextPageUrl() }}" class="page-btn">
                Selanjutnya
            </a>
        @endif

    </div>

</div>
    </section>

    {{-- Latest Scraping Note --}}
    <section class="scraping-note">
        <div class="scraping-note-icon">
            @include('components.icon', ['name' => 'clock', 'size' => 18])
        </div>

        <div class="scraping-note-content">
            <div class="scraping-note-title">
                {{ $latestNotification->title ?? 'Belum ada aktivitas scraping' }}
            </div>

            <div class="scraping-note-message">
                {{ $latestNotification->message ?? 'Belum ada data scraping terbaru.' }}
            </div>

            @if($latestNotification?->created_at)
                <div class="scraping-note-time">
                    Terakhir diperbarui:
                    {{ $latestNotification->created_at
                        ->setTimezone('Asia/Jakarta')
                        ->format('d M Y H:i') }}
                    WIB
                </div>
            @endif
        </div>
    </section>

    <section class="chart-grid">
        <div class="card chart-card">
            <h3 class="section-title">Trend Mingguan</h3>
            <div class="chart-frame">
                <canvas id="weeklyTrendCanvas"></canvas>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartSeriesData    = @json($chartSeries);
        const weeklySeriesData  = @json($weeksSeries);
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
        // FORMAT PAGU
        function formatRupiah(value) {
            if (value === null || value === undefined || value === '') {
                return 'Rp 0';
            }

            const number = Number(String(value).replace(/[^\d.-]/g, ''));

            if (isNaN(number)) {
                return 'Rp 0';
            }

            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 0
            }).format(number);
        }

        // --- Trend mingguan: line chart ---
        new Chart(document.getElementById('weeklyTrendCanvas'), {
            type: 'line',
            data: {
                labels: weeklySeriesData.map(item => item.day),
                datasets: [{
                    label: 'Trend',
                    data: weeklySeriesData.map(item => item.value ?? item.bar_height),
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
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                grid: { display: false }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    precision: 0
                },
                grid: {
                    color: palette.grid
                }
            },
                },
            },
        });


        // --- Server-side filtering via AJAX ---
        const searchInput = document.getElementById('dashboard-search');
        const yearSelect = document.getElementById('dashboard-year');
        const rangeSelect = document.getElementById('dashboard-range');
        const perPageSelect = document.getElementById('dashboard-per-page');
        const recordsBody = document.getElementById('records-body');
        const noResults = document.getElementById('no-results');
        const paginationRow = document.querySelector('.pagination-row');

        function formatDisplayDate(isoString) {
            if (!isoString) return '';
            let d = new Date(isoString);
            if (isNaN(d)) d = new Date(isoString.replace(' ', 'T'));
            if (isNaN(d)) return isoString;
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            const dd = String(d.getDate()).padStart(2,'0');
            const mm = months[d.getMonth()];
            const yyyy = d.getFullYear();
            const hh = String(d.getHours()).padStart(2,'0');
            const min = String(d.getMinutes()).padStart(2,'0');
            return `${dd} ${mm} ${yyyy} ${hh}:${min}`;
        }

        function renderRecords(records) {
            recordsBody.innerHTML = '';
            if (!records || records.length === 0) {
                noResults.style.display = '';
                return;
            }
            noResults.style.display = 'none';
            for (const r of records) {
                const tr = document.createElement('tr');
                tr.setAttribute('data-name', (r.nama_pekerjaan || '').toLowerCase());
                tr.setAttribute('data-id', (r.id_rup || '').toLowerCase());
                tr.setAttribute('data-agency', (r.nama_instansi || '').toLowerCase());
                tr.setAttribute('data-year', r.tahun_anggaran || '');
                tr.setAttribute('data-created', r.created_at || '');
                const displayCreated = r.created_at_display ?? r.created_at ?? '';

                tr.innerHTML = `
                    <td>${r.id}</td>
                    <td>${r.id_rup ?? ''}</td>
                    <td>${r.nama_pekerjaan ?? ''}</td>
                    <td>${formatRupiah(r.pagu)}</td>
                    <td>${r.nama_metode_pengadaan ?? ''}</td>
                    <td>${r.nama_instansi ?? ''}</td>
                    <td>${r.tahun_anggaran ?? ''}</td>
                    <td>${displayCreated}</td>
                    <td><a href="/records/${r.id}" class="text-decoration-none">Lihat</a></td>
                `;
                recordsBody.appendChild(tr);
            }
        }

        function renderPagination(meta) {
            if (!paginationRow) return;
            const safeMeta = meta || {};
            const cur = Number(safeMeta.current_page || 1);
            const last = Number(safeMeta.last_page || 1);
            const perPage = Number(safeMeta.per_page || 10);
            const total = Number(safeMeta.total || 0);
            const from = Number.isFinite(Number(safeMeta.from)) ? Number(safeMeta.from) : (total === 0 ? 0 : ((cur - 1) * perPage) + 1);
            const to = Number.isFinite(Number(safeMeta.to)) ? Number(safeMeta.to) : Math.min(from + perPage - 1, total);

            paginationRow.innerHTML = '';

            const info = document.createElement('div');
            info.className = 'pagination-info';
            info.textContent = `Menampilkan ${from.toLocaleString('id-ID')} sampai ${to.toLocaleString('id-ID')} dari ${total.toLocaleString('id-ID')} entri`;
            paginationRow.appendChild(info);

            const links = document.createElement('div');
            links.className = 'pagination-links';

            if (cur > 1) {
                const prev = document.createElement('a');
                prev.href = '#';
                prev.className = 'page-btn';
                prev.setAttribute('data-page', String(cur - 1));
                prev.textContent = 'Sebelumnya';
                prev.addEventListener('click', (e) => {
                    e.preventDefault();
                    fetchDashboard(cur - 1);
                });
                links.appendChild(prev);
            }

            for (let i = 1; i <= last; i++) {
                if (i === cur) {
                    const span = document.createElement('span');
                    span.className = 'page-number active';
                    span.textContent = String(i);
                    links.appendChild(span);
                } else if (
                    i === 1 ||
                    i === last ||
                    Math.abs(i - cur) <= 2
                ) {
                    const pageLink = document.createElement('a');
                    pageLink.href = '#';
                    pageLink.className = 'page-number';
                    pageLink.setAttribute('data-page', String(i));
                    pageLink.textContent = String(i);
                    pageLink.addEventListener('click', (e) => {
                        e.preventDefault();
                        fetchDashboard(i);
                    });
                    links.appendChild(pageLink);
                } else if (
                    i === cur - 3 ||
                    i === cur + 3
                ) {
                    const dots = document.createElement('span');
                    dots.className = 'page-number dots';
                    dots.textContent = '...';
                    links.appendChild(dots);
                }
            }

            if (cur < last) {
                const next = document.createElement('a');
                next.href = '#';
                next.className = 'page-btn';
                next.setAttribute('data-page', String(cur + 1));
                next.textContent = 'Selanjutnya';
                next.addEventListener('click', (e) => {
                    e.preventDefault();
                    fetchDashboard(cur + 1);
                });
                links.appendChild(next);
            }

            paginationRow.appendChild(links);
        }

        function fetchDashboard(page = 1) {
            const loader = document.getElementById('dashboard-loading');
            if (loader) loader.style.display = 'flex';
            const params = new URLSearchParams();
            const q = (searchInput?.value || '').trim();
            const y = (yearSelect?.value || '').trim();
            const range = (rangeSelect?.value || 'all');
            const perPage = (perPageSelect?.value || '').trim();
            if (q) params.append('search', q);
            if (y) params.append('tahun_anggaran', y);
            if (range && range !== 'all') params.append('range', range);
            const perPageVal = Number(perPageSelect?.value || 10);
            if (perPageVal) params.append('per_page', String(perPageVal));
            if (page && page > 1) params.append('page', page);

            fetch('/api/dashboard?' + params.toString(), { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                .then(r => {
                    const contentType = r.headers.get('content-type') || '';
                    if (!r.ok || contentType.indexOf('application/json') === -1) {
                        // Possibly redirected to login or received HTML response
                        throw new Error('Unexpected response from server');
                    }
                    return r.json();
                })
                .then(payload => {
                    const values = payload?.data?.stats ?? [];
                    const cards = document.querySelectorAll('#stats-grid .metric-value');
                    cards.forEach((node, index) => {
                        if (values[index]) node.textContent = values[index].value;
                    });

                    const records = payload?.data?.records ?? [];
                    renderRecords(records);
                    const pagination = payload?.data?.pagination ?? {};
                    renderPagination(pagination);
                    // show/hide topnav notification dot
                    try {
                        const unread = Number(payload?.data?.unread_notifications ?? 0);
                        const dot = document.querySelector('.topnav-icon-dot');
                        if (dot) dot.style.display = unread > 0 ? 'block' : 'none';
                    } catch (e) {
                        // ignore
                    }

                    if (loader) loader.style.display = 'none';
                })
                .catch(err => {
                    console.error('Fetch dashboard failed', err);
                    if (loader) loader.style.display = 'none';
                });
        }

        // bind events
        const filterForm = document.querySelector('.filter-form');
        filterForm?.addEventListener('submit', function (e) { e.preventDefault(); fetchDashboard(1); });
        searchInput?.addEventListener('input', () => fetchDashboard(1));
        yearSelect?.addEventListener('change', () => fetchDashboard(1));
        rangeSelect?.addEventListener('change', () => fetchDashboard(1));
        perPageSelect?.addEventListener('change', () => fetchDashboard(1));

        // initial load
        fetchDashboard(1);
    });
</script>
@endpush

<!-- Date Range -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const trigger = document.getElementById('date-range-trigger');
    const popup = document.getElementById('date-range-popup');
    const closeBtn = document.getElementById('date-range-close');

    const calendarDays = document.getElementById('calendar-days');
    const calendarMonth = document.getElementById('calendar-month');

    const prevMonth = document.getElementById('prev-month');
    const nextMonth = document.getElementById('next-month');

    const startText = document.getElementById('start-date-text');
    const endText = document.getElementById('end-date-text');

    const rangeText = document.getElementById('date-range-text');
    const hint = document.getElementById('date-range-hint');

    const resetBtn = document.getElementById('date-reset');
    const applyBtn = document.getElementById('date-apply');

    let currentDate = new Date();
    let startDate = null;
    let endDate = null;

    const monthNames = [
        'Januari', 'Februari', 'Maret', 'April',
        'Mei', 'Juni', 'Juli', 'Agustus',
        'September', 'Oktober', 'November', 'Desember'
    ];

    function formatDate(date) {
        if (!date) return '';

        return String(date.getDate()).padStart(2, '0') + '/' +
            String(date.getMonth() + 1).padStart(2, '0') + '/' +
            date.getFullYear();
    }

    function formatApiDate(date) {
        if (!date) return '';

        return date.getFullYear() + '-' +
            String(date.getMonth() + 1).padStart(2, '0') + '-' +
            String(date.getDate()).padStart(2, '0');
    }

    function isSameDate(a, b) {
        if (!a || !b) return false;

        return a.getFullYear() === b.getFullYear() &&
            a.getMonth() === b.getMonth() &&
            a.getDate() === b.getDate();
    }

    function isBetween(date, start, end) {
        if (!start || !end) return false;

        return date > start && date < end;
    }

    function renderCalendar() {

        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        calendarMonth.textContent =
            `${monthNames[month]} ${year}`;

        calendarDays.innerHTML = '';

        let firstDay = new Date(year, month, 1).getDay();

        firstDay = firstDay === 0 ? 6 : firstDay - 1;

        const daysInMonth =
            new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {

            const empty = document.createElement('span');

            empty.className = 'calendar-day empty';

            calendarDays.appendChild(empty);
        }

        for (let day = 1; day <= daysInMonth; day++) {

            const button = document.createElement('button');

            button.type = 'button';
            button.className = 'calendar-day';

            const date = new Date(year, month, day);

            button.textContent = day;

            const today = new Date();

            if (isSameDate(date, today)) {
                button.classList.add('today');
            }

            if (
                isSameDate(date, startDate) ||
                isSameDate(date, endDate)
            ) {
                button.classList.add('selected');
            }

            if (isBetween(date, startDate, endDate)) {
                button.classList.add('in-range');
            }

            button.addEventListener('click', function (event) {

                event.stopPropagation();

                if (!startDate) {

                    startDate = new Date(date);
                    endDate = null;

                    hint.textContent =
                        'Pilih tanggal selesai (opsional)';

                } else if (!endDate) {

                    if (date < startDate) {

                        endDate = new Date(startDate);
                        startDate = new Date(date);

                    } else {

                        endDate = new Date(date);

                    }

                    hint.textContent =
                        'Rentang tanggal siap diterapkan';

                } else {

                    startDate = new Date(date);
                    endDate = null;

                    hint.textContent =
                        'Pilih tanggal selesai (opsional)';
                }

                updateSelectedDates();
                renderCalendar();
            });

            calendarDays.appendChild(button);
        }
    }

    function updateSelectedDates() {

        startText.textContent =
            startDate ? formatDate(startDate) : 'Pilih tanggal';

        endText.textContent =
            endDate ? formatDate(endDate) : 'Opsional';
    }

    trigger.addEventListener('click', function (event) {

        event.stopPropagation();

        popup.classList.toggle('is-open');

    });

    closeBtn.addEventListener('click', function (event) {

        event.stopPropagation();

        popup.classList.remove('is-open');

    });

    applyBtn.addEventListener('click', function (event) {

        event.stopPropagation();

        if (!startDate) {

            rangeText.textContent = 'Pilih tanggal';

            return;
        }

        if (endDate) {

            rangeText.textContent =
                `${formatDate(startDate)} - ${formatDate(endDate)}`;

        } else {

            rangeText.textContent =
                formatDate(startDate);

        }

        popup.classList.remove('is-open');

        // Filter kalender
        const params = new URLSearchParams();

        const search =
            document.getElementById('dashboard-search')?.value || '';

        const year =
            document.getElementById('dashboard-year')?.value || '';

        const perPage =
            document.getElementById('dashboard-per-page')?.value || '10';

        if (search) {
            params.append('search', search);
        }

        if (year) {
            params.append('tahun_anggaran', year);
        }

        params.append('start_date', formatApiDate(startDate));

        if (endDate) {
            params.append('end_date', formatApiDate(endDate));
        }

        params.append('per_page', perPage);

        fetch('/api/dashboard?' + params.toString(), { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(response => {
                const contentType = response.headers.get('content-type') || '';
                if (!response.ok || contentType.indexOf('application/json') === -1) {
                    throw new Error('Unexpected response from server');
                }
                return response.json();
            })
            .then(payload => {

                if (!payload.success) {
                    return;
                }

                const records =
                    payload.data.records || [];

                const recordsBody =
                    document.getElementById('records-body');

                recordsBody.innerHTML = '';

                if (records.length === 0) {

                    recordsBody.innerHTML = `
                        <tr>
                            <td colspan="9" style="text-align:center;">
                                Belum ada data pada tanggal tersebut.
                            </td>
                        </tr>
                    `;

                    return;
                }

                records.forEach(record => {

                    const row =
                        document.createElement('tr');

                    row.innerHTML = `
                        <td>${record.id ?? ''}</td>
                        <td>${record.id_rup ?? ''}</td>
                        <td>${record.nama_pekerjaan ?? ''}</td>
                        <td>${formatRupiah(record.pagu)}</td>
                        <td>${record.nama_metode_pengadaan ?? ''}</td>
                        <td>${record.nama_instansi ?? ''}</td>
                        <td>${record.tahun_anggaran ?? ''}</td>
                        <td>${record.created_at_display ?? ''}</td>
                        <td>
                            <a href="/records/${record.id}">
                                Lihat
                            </a>
                        </td>
                    `;

                    recordsBody.appendChild(row);

                });

                // Pagination
                const pagination =
                    payload.data.pagination;

                const paginationRow =
                    document.querySelector('.pagination-row');

                if (paginationRow && pagination) {

                    paginationRow.innerHTML = `
                        <div class="pagination-info">
                            Menampilkan
                            ${pagination.from ?? 0}
                            sampai
                            ${pagination.to ?? 0}
                            dari
                            ${pagination.total ?? 0}
                            entri
                        </div>
                    `;
                }

            })
            .catch(error => {
                console.error('Filter tanggal gagal:', error);
            });
    });

    resetBtn.addEventListener('click', function (event) {

        event.stopPropagation();

        startDate = null;
        endDate = null;

        rangeText.textContent = 'Pilih tanggal';

        hint.textContent = 'Pilih tanggal mulai';

        updateSelectedDates();

        renderCalendar();

        // Reset tabel
        window.location.href =
            "{{ route('dashboard') }}";
    });

    prevMonth.addEventListener('click', function (event) {

        event.stopPropagation();

        currentDate.setMonth(
            currentDate.getMonth() - 1
        );

        renderCalendar();
    });

    nextMonth.addEventListener('click', function (event) {

        event.stopPropagation();

        currentDate.setMonth(
            currentDate.getMonth() + 1
        );

        renderCalendar();
    });

    document.addEventListener('click', function (event) {

        if (
            !popup.contains(event.target) &&
            !trigger.contains(event.target)
        ) {
            popup.classList.remove('is-open');
        }

    });

    updateSelectedDates();
    renderCalendar();

});
</script>
