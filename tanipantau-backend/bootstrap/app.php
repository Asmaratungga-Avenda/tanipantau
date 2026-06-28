<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
         $middleware->alias([
            'role'    => \App\Http\Middleware\RoleMiddleware::class,
            'auth'    => \App\Http\Middleware\Authenticate::class,
            'guest'   => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'prevent-back' => \App\Http\Middleware\PreventBackHistory::class,
            'check.lahan.ownership' => \App\Http\Middleware\CheckLahanOwnership::class,
            'check.kunjungan.ownership' => \App\Http\Middleware\CheckKunjunganOwnership::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
