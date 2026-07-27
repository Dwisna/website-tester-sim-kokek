@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="wrap page-shell">
    <div class="page-header">
        <div>
            <div class="eyebrow">System alerts</div>
            <h1 class="page-title">Notifications</h1>
            <p class="page-subtitle">Daftar pemberitahuan sistem.</p>
        </div>
        <div class="page-header-actions">
            @include('components.theme-toggle')
            <a href="{{ route('dashboard') }}" class="btn-surface">@include('components.icon', ['name' => 'speedometer', 'size' => 14]) Dashboard</a>
        </div>
    </div>

    <div class="card detail-card">
        <div class="notif-list">
            @forelse ($notifications as $notification)
                <div class="notif-item">
                    <div class="notif-avatar">@include('components.icon', ['name' => 'bell', 'size' => 16])</div>
                    <div>
                        <div class="notif-title">{{ $notification->title }}</div>
                        <div class="notif-message">{{ $notification->message }}</div>
                    </div>
                    <div class="notif-meta">{{ ucfirst($notification->priority) }} • {{ $notification->created_at->diffForHumans() }}</div>
                </div>
            @empty
                <div class="notif-item">
                    <div class="notif-avatar">@include('components.icon', ['name' => 'bell', 'size' => 16])</div>
                    <div>
                        <div class="notif-title">Belum ada notifikasi.</div>
                        <div class="notif-message">Semua pemberitahuan sistem akan muncul di sini.</div>
                    </div>
                    <div class="notif-meta">System</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
