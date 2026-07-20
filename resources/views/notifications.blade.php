@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="wrap-narrow">
    <div class="page-header">
        <div></div>
        <div class="page-header-actions">
            @include('components.theme-toggle')
            <a href="{{ route('dashboard') }}" class="btn-primary" style="padding:8px 14px;">← Dashboard</a>
        </div>
    </div>

    <div class="card">
        <h1 style="margin-top:0;">Notifications</h1>
        <p class="text-muted">Daftar pemberitahuan dalam bentuk icon lonceng di header website.</p>
    </div>

    <div class="card">
        <div class="item"><span style="font-size:1.2rem; margin-right:8px;">🔔</span> Ada 12 record baru menunggu validasi.</div>
        <div class="item"><span style="font-size:1.2rem; margin-right:8px;">🔔</span> Terdapat 4 penawaran yang hampir jatuh tempo.</div>
        <div class="item"><span style="font-size:1.2rem; margin-right:8px;">🔔</span> Customer service menerima 3 pesan dari n8n.</div>
    </div>
</div>
@endsection
