@extends('layouts.app')

@section('title', 'History')

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
        <h1 style="margin-top:0;">History</h1>
        <p class="text-muted">Riwayat pesan dan webhook yang masuk ke sistem sebelum diproses lebih lanjut.</p>
        <span class="pill-accent">{{ $history->count() }} event tersimpan</span>
    </div>

    <div class="card">
        @forelse ($history as $entry)
            <div class="item">
                <strong>{{ $entry->event ?? 'webhook' }}</strong>
                <div class="text-muted">{{ $entry->message ?? 'Pesan diterima' }}</div>
                <div class="text-muted">{{ $entry->created_at?->format('d M Y H:i') }} • {{ $entry->customer ?? 'unknown' }}</div>
            </div>
        @empty
            <p class="text-muted">Belum ada history. Webhook dari n8n akan muncul di sini.</p>
        @endforelse
    </div>
</div>
@endsection
