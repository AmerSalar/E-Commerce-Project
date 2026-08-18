<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;


Route::post('/register', RegisterController::class)->name('register')
    ->middleware('throttle:20,1');
Route::post('/login', LoginController::class)->name('login')
    ->middleware('throttle:10,1');

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
