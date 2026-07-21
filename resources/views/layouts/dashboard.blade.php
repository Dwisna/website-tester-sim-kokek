@extends('layouts.app')

@section('content')
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">RUP<span> Intelligence</span></div>
        <div class="nav">
            <a @class(['active' => request()->routeIs('dashboard')]) href="{{ route('dashboard') }}">Dashboard</a>
            <a @class(['active' => request()->routeIs('history')]) href="{{ route('history') }}">History</a>
        </div>
    </aside>
    <main class="main">
        @yield('main')
    </main>
</div>
@endsection
