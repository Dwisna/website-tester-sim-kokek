<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <style>
        body { margin:0; font-family: Inter,Segoe UI,Roboto,sans-serif; background:#07111f; color:#f3f7ff; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 32px 20px 48px; }
        .card { background:#101c31; border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:24px; margin-bottom:18px; }
        .pill { display:inline-block; padding:8px 12px; border-radius:999px; background:rgba(74,163,255,0.16); color:#9ed0ff; font-size:.9rem; }
        .item { padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.08); }
        .muted { color:#98a8c2; font-size:.92rem; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1 style="margin-top:0;">History</h1>
        <p class="muted">Riwayat pesan dan webhook yang masuk ke sistem sebelum diproses lebih lanjut.</p>
        <div class="pill">{{ $history->count() }} event tersimpan</div>
    </div>

    <div class="card">
        @forelse ($history as $entry)
            <div class="item">
                <strong>{{ $entry->event ?? 'webhook' }}</strong>
                <div class="muted">{{ $entry->message ?? 'Pesan diterima' }}</div>
                <div class="muted">{{ $entry->created_at?->format('d M Y H:i') }} • {{ $entry->customer ?? 'unknown' }}</div>
            </div>
        @empty
            <p class="muted">Belum ada history. Webhook dari n8n akan muncul di sini.</p>
        @endforelse
    </div>
</div>
</body>
</html>
