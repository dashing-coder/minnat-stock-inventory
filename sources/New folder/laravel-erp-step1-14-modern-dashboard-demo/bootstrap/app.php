<?php

use App\Http\Middleware\AuditMutations;
use App\Http\Middleware\EnsureActiveUser;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'active' => EnsureActiveUser::class,
            'audit' => AuditMutations::class,
        ]);
        $middleware->web(append: [AuditMutations::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Add application-specific exception rendering here if desired.
    })->create();
