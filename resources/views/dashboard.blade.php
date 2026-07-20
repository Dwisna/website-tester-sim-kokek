@extends('layouts.dashboard')

@section('title', 'RUP Intelligence Dashboard')

@section('main')
    <div class="topbar">
        <div>
            <div class="title">Dashboard</div>
            <div class="subtitle">Integrasi data RUP dari database {{ config('database.connections.'.config('database.default').'.database') ?? 'magang_db' }}</div>
        </div>
        <div class="topbar-actions">
            @include('components.theme-toggle')
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
                <p class="text-muted" style="margin:0;">Tabel ini menampilkan isi database RUP langsung dari MySQL.</p>
            </div>
            <form method="GET" action="/" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                <input class="form-input" type="text" name="search" value="{{ request('search') }}" placeholder="Cari pekerjaan, instansi, id RUP" style="min-width:240px;" />
                <select class="form-select" name="tahun_anggaran" style="min-width:160px;">
                    <option value="">Semua Tahun</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" {{ request('tahun_anggaran') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary" style="padding:10px 18px;">Filter</button>
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
                        <td><a href="{{ route('records.show', $record) }}" style="text-decoration:none;">Lihat</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9">Belum ada data yang sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            @if ($records->onFirstPage() === false)
                <a href="{{ $records->previousPageUrl() }}" class="btn-primary" style="padding:8px 14px;">Sebelumnya</a>
            @endif
            <span class="text-muted" style="align-self:center;">Halaman {{ $records->currentPage() }} dari {{ $records->lastPage() }}</span>
            @if ($records->hasMorePages())
                <a href="{{ $records->nextPageUrl() }}" class="btn-primary" style="padding:8px 14px;">Selanjutnya</a>
            @endif
        </div>
    </section>

    <div class="card" style="margin-top:16px;">
        <h3 style="margin-top:0;">OpenClaw Preview</h3>
        <p class="text-muted" style="margin-bottom:12px;">Mock interface untuk integrasi scraping data dari OpenClaw.</p>
        <a href="{{ route('openclaw') }}" class="btn-primary">Buka preview</a>
        <div class="preview-box">
            <div style="font-size:1.2rem; font-weight:700;">42 item</div>
            <div class="text-muted" style="margin-top:6px;">Sinkronisasi mock siap dipakai ketika OpenClaw selesai di-setup.</div>
        </div>
    </div>

    <section class="chart-box">
        <h3 style="margin:0;">Distribusi tahunan</h3>
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
                <p class="text-muted" style="margin:4px 0 0;">Widget bubble chat yang mengirim pesan ke Laravel dulu sebelum diarahkan ke n8n.</p>
            </div>
            <span class="pill">Live • n8n-ready</span>
        </div>
        <div class="chat-thread" id="chat-thread">
            <div class="bubble assistant">Halo! Saya asisten CS. Silakan tanyakan kebutuhan Anda tentang data RUP.</div>
        </div>
        <div class="chat-input-row">
            <input id="customer-chat-input" type="text" placeholder="Ketik pesan untuk customer service..." />
            <button id="customer-chat-send" class="btn-primary">Kirim</button>
        </div>
    </section>

    <section class="grid-2">
        <div class="card">
            <h3 style="margin-top:0;">Trend bulanan</h3>
            <div class="bar-list" style="height:160px;">
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
                <div class="status-row">
                    <span>{{ $item['label'] }}</span>
                    <strong>{{ $item['value'] }}</strong>
                </div>
            @endforeach
        </div>
    </section>
@endsection

@push('scripts')
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
@endpush
