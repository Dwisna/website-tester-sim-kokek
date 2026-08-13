<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login - RUP Intelligence')</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta11/dist/css/tabler.min.css" rel="stylesheet">
    @php($viteManifest = public_path('build/manifest.json'))
    @if (file_exists($viteManifest))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    @stack('head')
</head>
<body>
    @yield('content')
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta11/dist/js/tabler.min.js"></script>
</body>
</html>
