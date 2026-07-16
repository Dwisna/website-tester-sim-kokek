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
        .chat-window { background:#0b1727; border:1px solid rgba(255,255,255,0.12); border-radius:24px; padding:20px; min-height:420px; display:flex; flex-direction:column; gap:18px; }
        .message { max-width: 80%; line-height:1.6; padding:14px 18px; border-radius:20px; position:relative; }
        .message.assistant { align-self:flex-start; background:rgba(74,163,255,0.15); border:1px solid rgba(74,163,255,0.22); color:#e8f2ff; }
        .message.user { align-self:flex-end; background:rgba(61,220,151,0.12); border:1px solid rgba(61,220,151,0.22); color:#ddffed; }
        .message small { display:block; margin-top:6px; color:#8aa5be; font-size:.86rem; }
        .chat-input { display:flex; gap:12px; margin-top:auto; }
        .chat-input input { flex:1; border-radius:999px; border:1px solid rgba(255,255,255,0.12); background:#0b1727; color:#f3f7ff; padding:14px 18px; outline:none; }
        .chat-input button { border:none; border-radius:999px; background:#4aa3ff; color:#07111f; font-weight:700; padding:14px 22px; cursor:pointer; }
        .chat-input button:hover { background:#3a8cee; }
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

    <div class="card chat-window" id="chat-window">
        @foreach ($chatMessages as $message)
            <div class="message {{ $message['role'] }}">
                {{ $message['text'] }}
                <small>{{ ucfirst($message['role']) }}</small>
            </div>
        @endforeach
        <div class="chat-input">
            <input id="chat-input" type="text" placeholder="Ketik pertanyaan OpenClaw..." autocomplete="off" />
            <button id="chat-send">Kirim</button>
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
<script>
    const chatWindow = document.getElementById('chat-window');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');

    function appendMessage(role, text) {
        const message = document.createElement('div');
        message.className = 'message ' + role;
        message.innerHTML = text + '<small>' + role.charAt(0).toUpperCase() + role.slice(1) + '</small>';
        chatWindow.insertBefore(message, chatWindow.querySelector('.chat-input'));
        message.scrollIntoView({ behavior: 'smooth', block: 'end' });
    }

    chatSend.addEventListener('click', () => {
        const text = chatInput.value.trim();
        if (!text) return;
        appendMessage('user', text);
        chatInput.value = '';

        fetch('/api/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            appendMessage('assistant', data.data.message);
        });
    });
</script>
</body>
</html>
