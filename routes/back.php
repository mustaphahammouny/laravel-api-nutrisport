<?php

use App\Http\Controllers\Back\AuthController;
use App\Http\Controllers\Back\OrderController;
use App\Http\Controllers\Back\PasswordController;
use App\Http\Controllers\Back\ProfileController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::prefix('backoffice')
    ->group(function () {
        Route::prefix('auth')
            ->group(function () {
                Route::middleware('guest')
                    ->group(function () {
                        Route::post('login', [AuthController::class, 'login'])->name('back.auth.login');
                    });

                Route::middleware('auth:back-api')
                    ->group(function () {
                        Route::get('me', [AuthController::class, 'me'])->name('back.auth.me');
                        Route::post('refresh', [AuthController::class, 'refresh'])->name('back.auth.refresh');
                        Route::post('logout', [AuthController::class, 'logout'])->name('back.auth.logout');
                    });
            });

        Route::middleware('auth:back-api')
            ->group(function () {
                Route::prefix('profile')
                    ->group(function () {
                        Route::get('/', [ProfileController::class, 'show'])->name('back.profile.show');
                        Route::patch('/', [ProfileController::class, 'update'])->name('back.profile.update');
                    });

                Route::put('password', [PasswordController::class, 'update'])->name('back.password.update');

                Route::prefix('commandes')
                    ->group(function () {
                        Route::get('/', [OrderController::class, 'index'])
                            ->name('back.order.index')
                            ->can('viewAny', Order::class);
                        Route::get('{order}', [OrderController::class, 'show'])
                            ->name('back.order.show')
                            ->can('view', 'order');
                    });
            });
    });
