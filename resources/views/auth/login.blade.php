@extends('layouts.guest')

@section('title', 'Login - RUP Intelligence')

@section('content')
<style>
    .login-page { min-height:100vh; display:grid; place-items:center; padding:32px 20px; background:#f5f7fb; }
    .login-card { width:min(100%,420px); background:#fff; border:1px solid #e5e7eb; border-radius:18px; padding:32px; box-shadow:0 18px 45px rgba(15,23,42,.08); }
    .login-brand { display:flex; align-items:center; gap:12px; margin-bottom:28px; }
    .login-brand-mark { width:44px; height:44px; border-radius:12px; display:grid; place-items:center; background:#1d4ed8; color:#fff; font-weight:800; }
    .login-brand-title { font-weight:800; color:#111827; }
    .login-brand-subtitle { color:#64748b; font-size:13px; }
    .login-label { display:block; margin-bottom:7px; font-size:14px; font-weight:700; color:#334155; }
    .login-input { width:100%; box-sizing:border-box; padding:12px 13px; border:1px solid #cbd5e1; border-radius:10px; outline:none; }
    .login-input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
    .login-button { width:100%; border:0; border-radius:10px; padding:12px 16px; background:#2563eb; color:#fff; font-weight:800; cursor:pointer; }
    .login-button:hover { background:#1d4ed8; }
</style>

<div class="login-page">
    <div class="login-card">
        <div class="login-brand">
            <div class="login-brand-mark">R</div>
            <div>
                <div class="login-brand-title">RUP Intelligence</div>
                <div class="login-brand-subtitle">SIM KOKEK</div>
            </div>
        </div>

        <div style="margin-bottom:24px;">
            <h1 style="margin:0 0 6px; font-size:28px;">Login</h1>
            <p style="margin:0; color:#64748b;">Masuk untuk mengakses dashboard.</p>
        </div>

        @if ($errors->any())
            <div role="alert" style="margin-bottom:18px; padding:12px 14px; border-radius:10px; background:#fef2f2; color:#b91c1c; font-size:14px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div style="margin-bottom:16px;">
                <label class="login-label" for="email">Email</label>
                <input class="login-input" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>

            <div style="margin-bottom:14px;">
                <label class="login-label" for="password">Password</label>
                <input class="login-input" id="password" name="password" type="password" required autocomplete="current-password">
            </div>

            <label style="display:flex; align-items:center; gap:8px; margin-bottom:20px; color:#475569; font-size:14px;">
                <input type="checkbox" name="remember" value="1">
                Ingat saya
            </label>

            <button class="login-button" type="submit">Masuk</button>
        </form>
    </div>
</div>
@endsection
