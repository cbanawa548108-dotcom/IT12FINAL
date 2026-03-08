<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ Trust all proxies
        $middleware->trustProxies(at: '*');

        // ✅ Exclude captcha from CSRF to prevent 419 session expiry
        $middleware->validateCsrfTokens(except: [
            '/captcha',
        ]);

        $middleware->alias([
            'role'       => \App\Http\Middleware\CheckRole::class,
            'blocked.ip' => \App\Http\Middleware\CheckBlockedIp::class,
        ]);

        // ✅ Apply IP block check to all web routes
        $middleware->web(append: [
            \App\Http\Middleware\CheckBlockedIp::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();