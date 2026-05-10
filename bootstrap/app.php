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
    ->withMiddleware(function (Middleware $middleware): void {
        // Domain resolution and localization for both web and api
        $middleware->alias([
            'domain' => \App\Http\Middleware\ResolveDomain::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        ]);

        $middleware->appendToGroup('web', [
            \App\Http\Middleware\ResolveDomain::class,
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->appendToGroup('api', [
            \App\Http\Middleware\ResolveDomain::class,
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withProviders([
        \App\Providers\ViewThemeServiceProvider::class,
        \App\Providers\LocalizationServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
