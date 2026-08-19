<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::post('/register', RegisterController::class)->name('register')
    ->middleware('throttle:10,1');
Route::post('/login', LoginController::class)->name('login')
    ->middleware('throttle:5,1');

Route::post('/forgot-password', [ResetPasswordController::class, 'forgot'])
    ->name('forgot-password')
    ->middleware('throttle:2,1');
Route::post('/verify-code', [ResetPasswordController::class, 'verify'])
    ->name('verify-code');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('reset-password');


Route::middleware('auth:api')->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');
    Route::post('/change-password', ChangePasswordController::class)
        ->name('change-password');
});


Route::prefix('products')
    ->name('products.')
    ->controller(ProductController::class)
    ->group(function () {

        Route::get('/', 'getProducts');
        Route::get('/{product}', 'getSingleProduct')
            ->missing([ProductController::class, 'productNotFound']);
        Route::post('/', 'storeProduct');
        Route::post('/{product}',  'updateProduct')
            ->missing([ProductController::class, 'productNotFound']);
        Route::delete('/{product}', 'destroyProduct')
            ->missing([ProductController::class, 'productNotFound']);
    });
