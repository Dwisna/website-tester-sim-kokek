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
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 12px; }
        .topbar .title { font-size: 1.8rem; font-weight: 700; }
        .topbar .subtitle { color: var(--muted); margin-top: 4px; }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }
        .bell-btn { position: relative; width: 44px; height: 44px; border-radius: 50%; border: 1px solid var(--border); background: var(--panel); color: #fff; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; text-decoration: none; }
        .bell-btn .dot { position:absolute; top:8px; right:8px; width:10px; height:10px; border-radius:50%; background: #ff6b6b; }
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
        .chat-widget { margin-top: 20px; background: var(--panel); border: 1px solid var(--border); border-radius: 20px; padding: 18px; }
        .chat-thread { display: flex; flex-direction: column; gap: 10px; max-height: 260px; overflow: auto; padding-right: 4px; }
        .bubble { max-width: 82%; padding: 10px 12px; border-radius: 14px; line-height: 1.5; }
        .bubble.assistant { align-self: flex-start; background: rgba(74,163,255,0.16); color: #eaf4ff; }
        .bubble.user { align-self: flex-end; background: rgba(61,220,151,0.16); color: #ebfff4; }
        .chat-input-row { display:flex; gap:10px; margin-top:12px; }
        .chat-input-row input { flex:1; border-radius:999px; border:1px solid var(--border); background: rgba(255,255,255,0.04); color:#f3f7ff; padding: 10px 14px; }
        .chat-input-row button { border:none; border-radius:999px; background: var(--accent-2); color:#07111f; padding:10px 16px; font-weight:700; cursor:pointer; }
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
            <a class="active" href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ url('/openclaw') }}">API Chat</a>
            <a href="{{ route('history') }}">History</a>
            <a href="{{ url('/api/download') }}">Download</a>
            <a href="{{ route('notifications') }}">Notification</a>
        </div>
    </aside>
    <main class="main">
        <div class="topbar">
            <div>
                <div class="title">Dashboard</div>
                <div class="subtitle">Integrasi data RUP dari database {{ config('database.connections.'.config('database.default').'.database') ?? 'magang_db' }}</div>
            </div>
            <div class="topbar-actions">
                <a href="{{ route('notifications') }}" class="bell-btn" aria-label="Notifications">
                    🔔
                    <span class="dot"></span>
                </a>
                <div class="pill">Realtime monitoring • {{ now()->format('d M Y') }}</div>
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
                    <p style="margin:0; color:#98a8c2;">Tabel ini menampilkan isi database RUP langsung dari MySQL.</p>
                </div>
                <form method="GET" action="/" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pekerjaan, instansi, id RUP" style="border:1px solid rgba(255,255,255,0.12); background:rgba(255,255,255,0.03); color:#f3f7ff; padding:10px 14px; border-radius:999px; min-width:240px;" />
                    <select name="tahun_anggaran" style="border:1px solid rgba(255,255,255,0.12); background:rgba(255,255,255,0.03); color:#f3f7ff; padding:10px 14px; border-radius:999px; min-width:160px;">
                        <option value="">Semua Tahun</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ request('tahun_anggaran') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    <button type="submit" style="border:none; background:#4aa3ff; color:#07111f; padding:10px 18px; border-radius:999px; font-weight:700;">Filter</button>
                </form>
            </div>

            <table>
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
                            <td><a href="{{ route('records.show', $record) }}" style="color:#4aa3ff; text-decoration:none;">Lihat</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="9">Belum ada data yang sesuai filter.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top:18px; display:flex; justify-content:center; gap:8px; flex-wrap:wrap;">
                @if ($records->onFirstPage() === false)
                    <a href="{{ $records->previousPageUrl() }}" style="padding:8px 14px; border-radius:999px; background:#4aa3ff; color:#07111f; text-decoration:none;">Sebelumnya</a>
                @endif
                <span style="color:#98a8c2; align-self:center;">Halaman {{ $records->currentPage() }} dari {{ $records->lastPage() }}</span>
                @if ($records->hasMorePages())
                    <a href="{{ $records->nextPageUrl() }}" style="padding:8px 14px; border-radius:999px; background:#4aa3ff; color:#07111f; text-decoration:none;">Selanjutnya</a>
                @endif
            </div>
        </section>

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

        <section class="chat-widget">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:14px; flex-wrap:wrap;">
                <div>
                    <h3 style="margin:0;">Customer Service Chat</h3>
                    <p style="margin:4px 0 0; color:#98a8c2;">Widget bubble chat yang mengirim pesan ke Laravel dulu sebelum diarahkan ke n8n.</p>
                </div>
                <span class="pill">Live • n8n-ready</span>
            </div>
            <div class="chat-thread" id="chat-thread">
                <div class="bubble assistant">Halo! Saya asisten CS. Silakan tanyakan kebutuhan Anda tentang data RUP.</div>
            </div>
            <div class="chat-input-row">
                <input id="customer-chat-input" type="text" placeholder="Ketik pesan untuk customer service..." />
                <button id="customer-chat-send">Kirim</button>
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

    const thread = document.getElementById('chat-thread');
    const input = document.getElementById('customer-chat-input');
    const send = document.getElementById('customer-chat-send');

    function addBubble(role, text) {
        const bubble = document.createElement('div');
        bubble.className = 'bubble ' + role;
        bubble.textContent = text;
        thread.appendChild(bubble);
        thread.scrollTop = thread.scrollHeight;
    }

    send.addEventListener('click', () => {
        const text = input.value.trim();
        if (!text) return;
        addBubble('user', text);
        input.value = '';

        fetch('/api/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ message: text, source: 'dashboard', channel: 'web' })
        })
        .then((response) => response.json())
        .then((payload) => {
            addBubble('assistant', payload?.data?.message || 'Terima kasih, pesan Anda sudah diterima.');
        });
    });
</script>
</body>
</html>
