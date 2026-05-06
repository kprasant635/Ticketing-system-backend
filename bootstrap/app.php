<?php
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: [
            'ups_access_token',
            'ups_role',
        ]);
        
        $middleware->alias([
            // 'jwt.auth' => \App\Http\Middleware\JwtAuthMiddleware::class,
            'ups.role' => \App\Http\Middleware\UpsRoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return app(\App\Exceptions\Handler::class)->render($request, $e);
            }
        });
    })
    ->create();
