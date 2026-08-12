<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RUP Intelligence')</title>
    @include('components.theme-init')
    <!-- Tabler CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta11/dist/css/tabler.min.css" rel="stylesheet">
    <!-- Tabler Icons (webfont) - fixed CDN path -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@1.39.1/dist/tabler-icons.min.css" rel="stylesheet">
    @php
        $viteManifest = public_path('build/manifest.json');
    @endphp
    @if (file_exists($viteManifest))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Vite manifest not found; loading minimal fallback styles for local/testing -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    @stack('head')
</head>
<body>
    <div class="page">
        @yield('content')
    </div>
    @include('components.floating-chat')
    @stack('scripts')
    <!-- Tabler JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta11/dist/js/tabler.min.js"></script>
</body>
</html>
