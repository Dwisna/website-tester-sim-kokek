@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="wrap-narrow">
    <div class="page-header">
        <div></div>
        <div class="page-header-actions d-flex align-items-center gap-2">
            @include('components.theme-toggle')
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary" style="padding:8px 14px;"><i class="bi bi-arrow-left"></i> Dashboard</a>
        </div>
    </div>

    <div class="card">
        <h1 style="margin-top:0;">Notifications</h1>
        <p class="text-muted">Daftar pemberitahuan dalam bentuk icon lonceng di header website.</p>
    </div>

    <div class="card">
        @forelse ($notifications as $notification)
            <div class="item">
                <span class="me-2 text-primary"><i class="bi bi-bell-fill fs-5"></i></span>
                <strong>{{ $notification->title }}</strong>
                <div class="text-muted" style="margin-top:4px;">{{ $notification->message }}</div>
                <small style="display:block; margin-top:6px;">{{ ucfirst($notification->priority) }} • {{ $notification->created_at->diffForHumans() }}</small>
            </div>
        @empty
            <div class="item">Belum ada notifikasi.</div>
        @endforelse
    </div>
</div>
@endsection
