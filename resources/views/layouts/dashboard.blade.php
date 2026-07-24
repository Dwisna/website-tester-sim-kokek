@extends('layouts.app')

@section('content')
<div class="page">
    <!-- Navbar -->
    <header class="navbar navbar-expand-md navbar-light d-print-none">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="bi bi-list"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('history') }}" class="nav-link">History</a>
            </li>
        </ul>

        </ul>
        <div class="d-flex order-md-last ms-3">
            @include('components.theme-toggle')
        </div>
    </header>

    <div class="page-wrapper">
        <div class="container-xl">
            <div class="row g-4">
                <div class="col-12 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Menu</h3>
                            <div class="list-group list-group-flush">
                                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action @if(request()->routeIs('dashboard')) active @endif"><i class="ti ti-speedometer me-2"></i> Dashboard</a>
                                <a href="{{ route('history') }}" class="list-group-item list-group-item-action @if(request()->routeIs('history')) active @endif"><i class="ti ti-clock me-2"></i> History</a>
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
@endsection
