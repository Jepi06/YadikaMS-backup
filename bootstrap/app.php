<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.pkl' => \App\Http\Middleware\AuthenticatePkl::class,
            'auth.spmb' => \App\Http\Middleware\AuthenticateSpmb::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,

            // ← BARU: modul LMS
            'auth.lms' => \App\Http\Middleware\AuthenticateLms::class,
            'role.lms' => \App\Http\Middleware\RoleLmsMiddleware::class,
            'super.admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
