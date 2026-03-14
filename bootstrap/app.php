<?php

use App\Http\Middleware\InitializeSite;
use App\Http\Middleware\InsureCustomerBelongToSite;
use App\Http\Middleware\ResolveCart;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'site' => InitializeSite::class,
            'customer' => InsureCustomerBelongToSite::class,
            'cart' => ResolveCart::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
