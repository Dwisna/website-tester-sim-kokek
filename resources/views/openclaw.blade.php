<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenClaw Preview</title>
    <style>
        body { margin:0; font-family:Inter,Segoe UI,sans-serif; background:#07111f; color:#f3f7ff; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 32px 20px 48px; }
        .card { background:#101c31; border:1px solid rgba(255,255,255,0.08); border-radius:22px; padding:24px; margin-bottom:18px; }
        .grid { display:grid; grid-template-columns: 1.2fr .8fr; gap:18px; }
        .pill { display:inline-block; padding:8px 12px; border-radius:999px; background:rgba(61,220,151,0.14); color:#8eeeb7; font-size:.9rem; }
        .metric { font-size:2rem; font-weight:700; }
        .list { margin: 0; padding-left: 18px; color:#98a8c2; }
        @media (max-width: 768px) { .grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1 style="margin-top:0;">OpenClaw</h1>
        <p style="color:#98a8c2;">Preview antarmuka mock untuk integrasi scraping data dari OpenClaw. Saat setup selesai, modul ini bisa dipakai untuk mengirim permintaan dan melihat status sinkronisasi.</p>
        <div class="pill">{{ $mockData['status'] }}</div>
    </div>

    <div class="grid">
        <div class="card">
            <h3 style="margin-top:0;">Ringkasan</h3>
            <div class="metric">{{ $mockData['items'] }}</div>
            <p style="color:#98a8c2;">item data siap diproses dari hasil scraping mock.</p>
            <p>{{ $mockData['summary'] }}</p>
        </div>
        <div class="card">
            <h3 style="margin-top:0;">Sinkronisasi terakhir</h3>
            <div class="metric">{{ $mockData['last_sync'] }}</div>
            <ul class="list">
                <li>Polling berjalan normal</li>
                <li>Queue siap menerima job baru</li>
                <li>Log disimpan untuk audit</li>
            </ul>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Mock activity</h3>
        <ul class="list">
            <li>Ambil data dari sumber simulasi</li>
            <li>Normalisasi field ke struktur database</li>
            <li>Masukkan ke tabel RUP dan tampilkan di dashboard</li>
        </ul>
    </div>
</div>
</body>
</html>
