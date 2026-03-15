<?php

use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CartItemController;
use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Front\OrderController;
use App\Http\Controllers\Front\PasswordController;
use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('site')
    ->group(function () {
        Route::prefix('auth')
            ->group(function () {
                Route::middleware('guest')
                    ->group(function () {
                        Route::post('login', [AuthController::class, 'login'])->name('front.auth.login');
                    });

                Route::middleware(['auth:front-api', 'customer'])
                    ->group(function () {
                        Route::get('me', [AuthController::class, 'me'])->name('front.auth.me');
                        Route::post('refresh', [AuthController::class, 'refresh'])->name('front.auth.refresh');
                        Route::post('logout', [AuthController::class, 'logout'])->name('front.auth.logout');
                    });
            });

        Route::middleware(['auth:front-api', 'customer'])
            ->group(function () {
                Route::prefix('profile')
                    ->group(function () {
                        Route::get('/', [ProfileController::class, 'show'])->name('front.profile.show');
                        Route::patch('/', [ProfileController::class, 'update'])->name('front.profile.update');
                    });

                Route::put('password', [PasswordController::class, 'update'])->name('front.password.update');

                Route::prefix('commandes')
                    ->group(function () {
                        Route::get('/', [OrderController::class, 'index'])->name('front.order.index');
                        Route::post('/', [OrderController::class, 'store'])
                            ->name('front.order.store')
                            ->middleware('cart');
                        Route::get('{order}', [OrderController::class, 'show'])
                            ->name('front.order.show')
                            ->can('view', 'order');
                    });
            });

        Route::prefix('produits')
            ->group(function () {
                Route::get('/', [ProductController::class, 'index'])->name('front.product.index');
                Route::get('{product}', [ProductController::class, 'show'])->name('front.product.show');
            });

        Route::middleware('cart')
            ->prefix('panier')
            ->group(function () {
                Route::get('/', [CartController::class, 'show'])->name('front.cart.show');
                Route::put('/', [CartController::class, 'update'])->name('front.cart.update');
                Route::delete('/', [CartController::class, 'destroy'])->name('front.cart.destroy');

                Route::prefix('items/{product}')
                    ->group(function () {
                        Route::post('/', [CartItemController::class, 'store'])->name('front.cart-item.store');
                        Route::delete('/', [CartItemController::class, 'destroy'])->name('front.cart-item.destroy');
                    });
            });
    });
