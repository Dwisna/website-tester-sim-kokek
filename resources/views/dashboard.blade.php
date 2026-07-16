<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RUP Intelligence Dashboard</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #07111f;
            --panel: #101c31;
            --panel-2: #16253d;
            --text: #f3f7ff;
            --muted: #98a8c2;
            --accent: #3ddc97;
            --accent-2: #4aa3ff;
            --warning: #ffb84d;
            --border: rgba(255,255,255,0.08);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter,Segoe UI,Roboto,sans-serif;
            background: linear-gradient(135deg, #07111f 0%, #111d33 100%);
            color: var(--text);
        }
        .app-shell { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
        .sidebar { padding: 28px 20px; background: rgba(7,17,31,0.9); border-right: 1px solid var(--border); }
        .brand { font-size: 1.3rem; font-weight: 700; margin-bottom: 24px; }
        .brand span { color: var(--accent); }
        .nav { display: grid; gap: 10px; }
        .nav a { text-decoration: none; color: var(--muted); padding: 12px 14px; border-radius: 10px; transition: .2s ease; }
        .nav a.active, .nav a:hover { background: var(--panel); color: var(--text); }
        .main { padding: 24px 28px 36px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .topbar .title { font-size: 1.8rem; font-weight: 700; }
        .topbar .subtitle { color: var(--muted); margin-top: 4px; }
        .pill { border: 1px solid var(--border); padding: 10px 14px; border-radius: 999px; color: var(--muted); }
        .hero { background: linear-gradient(120deg, var(--panel) 0%, var(--panel-2) 100%); border: 1px solid var(--border); border-radius: 24px; padding: 24px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .hero h2 { font-size: 1.35rem; margin: 0 0 8px; }
        .hero p { margin: 0; color: var(--muted); max-width: 560px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px; }
        .card { background: var(--panel); border: 1px solid var(--border); padding: 18px; border-radius: 18px; }
        .card .label { color: var(--muted); font-size: .9rem; }
        .card .value { font-size: 1.45rem; font-weight: 700; margin-top: 8px; }
        .card.primary { border-color: rgba(61,220,151,0.25); }
        .card.accent { border-color: rgba(74,163,255,0.25); }
        .card.warning { border-color: rgba(255,184,77,0.25); }
        .card.success { border-color: rgba(61,220,151,0.25); }
        .card.info { border-color: rgba(74,163,255,0.25); }
        .card.neutral { border-color: rgba(255,184,77,0.25); }
        .grid-2 { display:grid; grid-template-columns: 1.1fr .9fr; gap:16px; margin-top:18px; }
        .table-wrap { background: var(--panel); border: 1px solid var(--border); border-radius: 18px; padding: 16px; overflow: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 10px; border-bottom: 1px solid var(--border); text-align: left; font-size: .95rem; }
        th { color: var(--muted); font-weight: 600; }
        .chart-box { margin-top: 20px; background: var(--panel); border: 1px solid var(--border); border-radius: 18px; padding: 16px; }
        .bar-list { display: flex; align-items: flex-end; gap: 10px; height: 180px; margin-top: 14px; }
        .bar-item { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .bar { width: 100%; background: linear-gradient(180deg, var(--accent-2), var(--accent)); border-radius: 8px 8px 0 0; min-height: 6px; height: 0; }
        .bar.secondary { background: linear-gradient(180deg, #ffb84d, #ff7b2c); }
        .bar-label { color: var(--muted); font-size: .8rem; }
        @media (max-width: 960px) { .app-shell { grid-template-columns: 1fr; } .sidebar { border-right: none; border-bottom: 1px solid var(--border); } .stats-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 640px) { .stats-grid { grid-template-columns: 1fr; } .hero { flex-direction: column; align-items: flex-start; gap: 10px; } }
    </style>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">RUP<span> Intelligence</span></div>
        <div class="nav">
            <a class="active" href="#">Dashboard</a>
            <a href="#">API Chat</a>
            <a href="#">History</a>
            <a href="#">Download</a>
            <a href="#">Notification</a>
        </div>
    </aside>
    <main class="main">
        <div class="topbar">
            <div>
                <div class="title">Dashboard</div>
                <div class="subtitle">Integrasi data RUP dari database {{ config('database.connections.'.config('database.default').'.database') ?? 'magang_db' }}</div>
            </div>
            <div class="pill">Realtime monitoring • {{ now()->format('d M Y') }}</div>
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

        <section class="grid-2">
            <div class="table-wrap">
                <h3 style="margin: 0 0 12px;">Data terbaru</h3>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Pekerjaan</th>
                            <th>Instansi</th>
                            <th>Tahun Anggaran</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentRecords as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>{{ $record->nama_pekerjaan }}</td>
                                <td>{{ $record->nama_instansi }}</td>
                                <td>{{ $record->tahun_anggaran }}</td>
                                <td><a href="{{ route('records.show', $record) }}" style="color:#4aa3ff; text-decoration:none;">Lihat</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5">Belum ada data yang tersimpan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h3 style="margin-top:0;">OpenClaw Preview</h3>
                <p style="color:#98a8c2; margin-bottom: 12px;">Mock interface untuk integrasi scraping data dari OpenClaw.</p>
                <a href="{{ route('openclaw') }}" style="display:inline-block; padding:10px 14px; border-radius:999px; background:#4aa3ff; color:white; text-decoration:none; font-weight:700;">Buka preview</a>
                <div style="margin-top:16px; padding:12px; border:1px solid rgba(255,255,255,0.08); border-radius:12px; background:rgba(255,255,255,0.03);">
                    <div style="font-size:1.2rem; font-weight:700;">42 item</div>
                    <div style="color:#98a8c2; margin-top:6px;">Sinkronisasi mock siap dipakai ketika OpenClaw selesai di-setup.</div>
                </div>
            </div>
        </section>

        <section class="chart-box">
            <h3 style="margin: 0;">Distribusi tahunan</h3>
            <div class="bar-list">
                @foreach ($chartSeries as $item)
                    <div class="bar-item">
                        <div class="bar" data-height="{{ $item['bar_height'] }}"></div>
                        <div class="bar-label">{{ $item['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="grid-2">
            <div class="card">
                <h3 style="margin-top:0;">Trend bulanan</h3>
                <div class="bar-list" style="height: 160px;">
                    @foreach ($monthlySeries as $item)
                        <div class="bar-item">
                            <div class="bar secondary" data-height="{{ $item['bar_height'] }}"></div>
                            <div class="bar-label">{{ $item['month'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card">
                <h3 style="margin-top:0;">Status breakdown</h3>
                @foreach ($statusBreakdown as $item)
                    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.08);">
                        <span>{{ $item['label'] }}</span>
                        <strong>{{ $item['value'] }}</strong>
                    </div>
                @endforeach
            </div>
        </section>
    </main>
</div>

<script>
    document.querySelectorAll('.bar[data-height]').forEach((node) => {
        const height = node.getAttribute('data-height');
        if (height) {
            node.style.height = height + 'px';
        }
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
</script>
</body>
</html>
