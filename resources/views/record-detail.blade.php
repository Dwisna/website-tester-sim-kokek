<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail record</title>
    <style>
        body { margin:0; font-family:Inter,Segoe UI,sans-serif; background:#07111f; color:#f3f7ff; }
        .wrap { max-width: 1080px; margin: 0 auto; padding: 32px 20px 48px; }
        .card { background:#101c31; border:1px solid rgba(255,255,255,0.08); border-radius:22px; padding:24px; }
        .grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:16px; }
        .label { color:#98a8c2; font-size:.85rem; margin-bottom:6px; }
        .value { font-size:1rem; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .btn { display:inline-block; text-decoration:none; background:#3ddc97; color:#04110d; padding:10px 14px; border-radius:999px; font-weight:700; }
        @media (max-width: 768px) { .grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="topbar">
        <div>
            <h1 style="margin:0 0 6px;">Detail record</h1>
            <p style="margin:0; color:#98a8c2;">Ringkasan lengkap data hasil scraping dan integrasi dashboard.</p>
        </div>
        <a class="btn" href="/">← Kembali ke dashboard</a>
    </div>

    <div class="card">
        <h2 style="margin-top:0;">{{ $record->nama_pekerjaan ?? 'Tanpa judul' }}</h2>
        <div class="grid">
            <div><div class="label">Instansi</div><div class="value">{{ $record->nama_instansi ?? '-' }}</div></div>
            <div><div class="label">Tahun anggaran</div><div class="value">{{ $record->tahun_anggaran ?? '-' }}</div></div>
            <div><div class="label">Jenis pengadaan</div><div class="value">{{ $record->nama_jenis_pengadaan ?? '-' }}</div></div>
            <div><div class="label">Metode</div><div class="value">{{ $record->nama_metode_pengadaan ?? '-' }}</div></div>
            <div><div class="label">PIC</div><div class="value">{{ $record->pic ?? '-' }}</div></div>
            <div><div class="label">Lokasi</div><div class="value">{{ $record->lokasi_pekerjaan ?? '-' }}</div></div>
            <div><div class="label">Status penawaran</div><div class="value">{{ $record->is_status_kirim_penawaran ? 'Terkirim' : 'Belum' }}</div></div>
            <div><div class="label">Keterangan</div><div class="value">{{ $record->keterangan ?? '-' }}</div></div>
        </div>
    </div>
</div>
</body>
</html>
