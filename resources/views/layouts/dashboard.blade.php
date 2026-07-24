@extends('layouts.app')

@section('content')
<div class="page">
    <style>
        /* Improve sidebar visibility in dark mode and adjust notification sizing */
        .sidebar-card { transition: background-color .15s ease, border-color .15s ease; }
        body.dark .sidebar-card {
            background: rgba(255,255,255,0.03) !important;
            border: 1px solid rgba(255,255,255,0.06) !important;
            box-shadow: 0 6px 18px rgba(0,0,0,0.25);
            color: #e6eef8 !important;
        }
        .topbar-actions .btn.notification-btn { width:46px; height:36px; display:inline-flex; align-items:center; justify-content:center; }
        .topbar-actions .btn.notification-btn svg { width:20px; height:20px; }
    </style>
    <!-- Navbar -->
    <header class="navbar navbar-expand-md navbar-light d-print-none">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">@include('components.icon', ['name' => 'menu', 'size' => 20])</a>
            </li>
        </ul>

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
