@extends('layouts.app')

@section('title', 'History')

@section('topnav-search')
    <div class="topnav-page-label">History</div>
@endsection

@section('content')
<div class="wrap page-shell fade-in">
    <div class="page-header">
        <div>
            <div class="eyebrow">Activity log</div>
            <h1 class="page-title">History</h1>
            <p class="page-subtitle">Riwayat pesan dan webhook yang masuk ke sistem sebelum diproses lebih lanjut.</p>
        </div>
        <div class="page-header-actions">
            @include('components.theme-toggle')
            <a href="{{ route('dashboard') }}" class="btn-primary">← Dashboard</a>
        </div>
    </div>

    <div class="card detail-card">
        <span class="pill-accent">{{ $history->count() }} event tersimpan</span>
    </div>

    <div class="card detail-card">
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
