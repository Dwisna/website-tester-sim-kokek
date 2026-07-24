@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Notifications</h2>
            <div class="text-muted">Daftar pemberitahuan sistem.</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            @include('components.theme-toggle')
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">@include('components.icon', ['name' => 'speedometer', 'size' => 14]) Dashboard</a>
        </div>
    </div>

    <div class="card">
        <div class="list-group list-group-flush">
            @forelse ($notifications as $notification)
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar bg-primary text-white rounded-circle p-2">@include('components.icon', ['name' => 'bell', 'size' => 16])</span>
                        </div>
                        <div class="col">
                            <div class="fw-semibold">{{ $notification->title }}</div>
                            <div class="text-muted">{{ $notification->message }}</div>
                        </div>
                        <div class="col-auto text-muted small">{{ ucfirst($notification->priority) }} • {{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <div class="list-group-item">Belum ada notifikasi.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
