@extends('layouts.app')

@section('content')
<div class="page app-shell-shell">
    <aside class="sidebar sidebar-fixed" id="app-sidebar">
        <div class="sidebar-brand">
            <div class="brand-mark">R</div>
            <div>
                <div class="brand">RUP Intelligence</div>
                <div class="brand-subtitle">Admin dashboard</div>
            </div>
        </div>

        <div class="sidebar-user">
            <div class="sidebar-user-avatar">A</div>
            <div class="sidebar-user-copy">
                <strong>Administrator</strong>
                <span>System operator</span>
            </div>
        </div>

        <nav class="sidebar-nav" aria-label="Sidebar navigation">
            <a href="{{ route('dashboard') }}" class="sidebar-nav-link @if(request()->routeIs('dashboard')) is-active @endif">
                @include('components.ui.icon', ['name' => 'speedometer', 'size' => 18])
                <span>Dashboard</span>
            </a>
            <!-- <a href="{{ route('history') }}" class="sidebar-nav-link @if(request()->routeIs('history')) is-active @endif">
                @include('components.ui.icon', ['name' => 'clock', 'size' => 18])
                <span>History</span>
            </a> -->
        </nav>

        <!-- <div class="sidebar-footer">
            <div class="sidebar-footer-label">Workspace</div>
            <div class="sidebar-footer-copy">Clean SaaS dashboard shell</div>
        </div> -->
    </aside>

    <div class="app-main">
        <header class="topnav">
            <button type="button" class="topnav-menu-btn" id="sidebar-toggle" aria-label="Buka menu">
                @include('components.ui.icon', ['name' => 'menu', 'size' => 20])
            </button>

            <div class="topnav-heading">
                <div class="topnav-breadcrumb">@yield('topnav-breadcrumb')</div>
                <div class="topnav-title-row">
                    <h1 class="topnav-title">@yield('topnav-title', trim($__env->yieldContent('title', 'Dashboard')))</h1>
                    <span class="topnav-date">@yield('topnav-date', now()->format('d M Y'))</span>
                </div>
                <div class="topnav-description">@yield('topnav-description')</div>
            </div>

            <div class="topnav-search-wrap">
                @yield('topnav-search')
            </div>

            <div class="topnav-actions">
                <div class="topnav-action-chip">@include('components.ui.icon', ['name' => 'clock', 'size' => 16]) <span>Realtime</span></div>
                <a href="{{ route('notifications') }}" class="topnav-icon-btn" aria-label="Notifications">
                    @include('components.ui.icon', ['name' => 'bell', 'size' => 18])
                    <span class="topnav-icon-dot"></span>
                </a>
                @include('components.ui.theme-toggle')
                <div class="topnav-profile">
                    <div class="topnav-avatar">A</div>
                    <div class="topnav-profile-copy">
                        <strong>Administrator</strong>
                        <span>Active now</span>
                    </div>
                </div>
            </div>
        </header>

        <main class="app-content">
            @yield('main')
        </main>
    </div>
</div>

<script>
    (function () {
        const sidebar = document.getElementById('app-sidebar');
        const toggle = document.getElementById('sidebar-toggle');

        if (!sidebar || !toggle) return;

        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('is-open');
        });
    })();
</script>
@endsection
