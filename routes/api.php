<?php

use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Front\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('site')
    ->group(function () {
        Route::prefix('auth')
            ->group(function ($router) {
                Route::post('login', [AuthController::class, 'login']);

                Route::middleware('auth:front-api')
                    ->group(function () {
                        Route::get('me', [AuthController::class, 'me']);
                        Route::post('refresh', [AuthController::class, 'refresh']);
                        Route::post('logout', [AuthController::class, 'logout']);
                    });
            });

        Route::prefix('produits')
            ->group(function () {
                Route::get('/', [ProductController::class, 'index']);
                Route::get('{product}', [ProductController::class, 'show']);
            });
    });
