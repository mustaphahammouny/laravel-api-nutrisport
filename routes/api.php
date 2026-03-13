<?php

use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Front\OrderController;
use App\Http\Controllers\Front\PasswordController;
use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('site')
    ->group(function () {
        Route::prefix('auth')
            ->group(function ($router) {
                Route::middleware('guest')
                    ->group(function () {
                        Route::post('login', [AuthController::class, 'login'])->name('auth.login');
                    });

                Route::middleware('auth:front-api')
                    ->group(function () {
                        Route::get('me', [AuthController::class, 'me'])->name('auth.me');
                        Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
                        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
                    });
            });

        Route::middleware('auth:front-api')
            ->group(function () {
                Route::prefix('profile')
                    ->group(function () {
                        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
                        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
                    });

                Route::put('password', [PasswordController::class, 'update'])->name('password.update');

                Route::prefix('commandes')
                    ->group(function () {
                        Route::get('/', [OrderController::class, 'index'])->name('order.index');
                        Route::get('{order}', [OrderController::class, 'show'])
                            ->name('order.show')
                            ->can('view', 'order');
                    });
            });

        Route::prefix('produits')
            ->group(function () {
                Route::get('/', [ProductController::class, 'index'])->name('product.index');
                Route::get('{product}', [ProductController::class, 'show'])->name('product.show');
            });
    });
