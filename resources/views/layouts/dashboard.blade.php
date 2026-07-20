@extends('layouts.app')

@section('content')
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">RUP<span> Intelligence</span></div>
        <div class="nav">
            <a @class(['active' => request()->routeIs('dashboard')]) href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ url('/openclaw') }}">API Chat</a>
            <a @class(['active' => request()->routeIs('history')]) href="{{ route('history') }}">History</a>
            <a href="{{ url('/api/download') }}">Download</a>
            <a @class(['active' => request()->routeIs('notifications')]) href="{{ route('notifications') }}">Notification</a>
        </div>
    </aside>
    <main class="main">
        @yield('main')
    </main>
</div>
@endsection
