<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\DashboardBasicAuth;
use App\Http\Middleware\VerifyN8nSecret;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'dashboard.auth' => DashboardBasicAuth::class,
            'verify.n8n.secret' => VerifyN8nSecret::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
        if ($request->is('n8n/*') || $request->is('api/*')) {
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

            $message = match ($status) {
                404 => 'Endpoint tidak ditemukan.',
                401 => 'Akses ditolak. Autentikasi tidak valid.',
                403 => 'Akses dibatasi.',
                default => 'Terjadi kesalahan pada server.',
            };

            return response()->json([
                'success' => false,
                'message' => $message,
            ], $status);
        }
    });

    })->create();
