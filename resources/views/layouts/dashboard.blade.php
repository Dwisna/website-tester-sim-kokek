@extends('layouts.app')

@section('content')
<div class="page">
    <style>
        /* Improve sidebar visibility in dark mode and adjust notification sizing */
        .sidebar-card { transition: background-color .15s ease, border-color .15s ease; }
        body.dark .sidebar-card {
            background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.015)) !important;
            border: 1px solid rgba(255,255,255,0.04) !important;
            box-shadow: 0 6px 18px rgba(0,0,0,0.35);
            color: rgba(255,255,255,0.92) !important;
        }
        /* Make list items readable in dark mode */
        body.dark .sidebar-card .list-group-item {
            background: transparent !important;
            color: rgba(255,255,255,0.82) !important;
            border-color: rgba(255,255,255,0.02) !important;
        }
        body.dark .sidebar-card .list-group-item .svg, body.dark .sidebar-card .list-group-item svg { color: inherit; }
        body.dark .sidebar-card .list-group-item.active {
            background: rgba(255,255,255,0.02) !important;
            border-left: 3px solid rgba(245,158,11,0.95) !important;
        }

        /* Notification button: remove strong white square and control bg via CSS */
        .topbar-actions .btn.notification-btn { width:46px; height:36px; display:inline-flex; align-items:center; justify-content:center; padding:0 6px; background: transparent; border: none; }
        .topbar-actions .btn.notification-btn svg { width:20px; height:20px; }
        .topbar-actions .btn.notification-btn:hover { background: rgba(0,0,0,0.04); }
        body.dark .topbar-actions .btn.notification-btn { background: rgba(255,255,255,0.03) !important; border: 1px solid rgba(255,255,255,0.06) !important; color: rgba(255,255,255,0.95) !important; }
        body.dark .topbar-actions .btn.notification-btn:hover { background: rgba(255,255,255,0.04) !important; }

        /* Small adjustments for avatar-style icons inside list items */
        .avatar svg { vertical-align: middle; }
    </style>
    <!-- Navbar (hamburger removed per request) -->
    <header class="navbar navbar-expand-md navbar-light d-print-none">
        <div class="d-flex order-md-last ms-3">
            @include('components.theme-toggle')
        </div>
    </header>

    <div class="page-wrapper">
        <div class="container-xl">
            <div class="row g-4">
                <div class="col-12 col-xl-3 ps-0">
                    <div class="card sidebar-card">
                        <div class="card-body">
                            <h3 class="card-title">Menu</h3>
                            <div class="list-group list-group-flush">
                                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action @if(request()->routeIs('dashboard')) active @endif">@include('components.icon', ['name' => 'speedometer', 'size' => 18]) Dashboard</a>
                                <a href="{{ route('history') }}" class="list-group-item list-group-item-action @if(request()->routeIs('history')) active @endif">@include('components.icon', ['name' => 'clock', 'size' => 18]) History</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-9">
                    @yield('main')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
