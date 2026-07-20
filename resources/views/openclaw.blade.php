@extends('layouts.app')

@section('title', 'OpenClaw Preview')

@section('content')
<div class="wrap">
    <div class="page-header">
        <div></div>
        <div class="page-header-actions">
            @include('components.theme-toggle')
            <a href="{{ route('dashboard') }}" class="btn-primary" style="padding:8px 14px;">← Dashboard</a>
        </div>
    </div>

    <div class="card">
        <h1 style="margin-top:0;">OpenClaw</h1>
        <p class="text-muted">Preview antarmuka mock untuk integrasi scraping data dari OpenClaw. Saat setup selesai, modul ini bisa dipakai untuk mengirim permintaan dan melihat status sinkronisasi.</p>
        <span class="pill-success">{{ $mockData['status'] }}</span>
    </div>

    <div class="grid">
        <div class="card">
            <h3 style="margin-top:0;">Ringkasan</h3>
            <div class="metric">{{ $mockData['items'] }}</div>
            <p class="text-muted">item data siap diproses dari hasil scraping mock.</p>
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
@endsection

@push('scripts')
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
@endpush
