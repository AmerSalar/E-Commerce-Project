<?php

use App\Http\Controllers\API\{
    CartController,
    CategoryController,
    OrderController,
    ProductController,
    UserController
};
use App\Http\Controllers\Auth\{
    ChangePasswordController,
    LoginController,
    LogoutController,
    RefreshController,
    RegisterController,
    ResetPasswordController
};
use Illuminate\Support\Facades\Route;

// public routes VVVVVVVVVVVVVVVVVVVVVVVVVV
Route::post('/register', RegisterController::class)->name('register')
    ->middleware('throttle:10,1');
Route::post('/login', LoginController::class)->name('login')
    ->middleware('throttle:5,1');
Route::post('/refresh', RefreshController::class)->name('refresh')
    ->middleware('throttle:5,1');

Route::post('/forgot-password', [ResetPasswordController::class, 'forgot'])
    ->name('forgot-password')
    ->middleware('throttle:2,1');
Route::post('/verify-code', [ResetPasswordController::class, 'verify'])
    ->name('verify-code')
    ->middleware('throttle:10,5');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('reset-password');

Route::prefix('products')
    ->name('products.')
    ->controller(ProductController::class)
    ->group(function () {

        Route::get('/', 'getProducts');
        Route::get('/{product}', 'getSingleProduct')
            ->missing([ProductController::class, 'productNotFound']);
    });
Route::prefix('categories')
    ->name('categories.')
    ->controller(CategoryController::class)
    ->group(function () {

        Route::get('/', 'getAll')->name('getAll');
        Route::get('/{category}', 'getOne')
            ->missing([CategoryController::class, 'notFound'])
            ->name('getOne');
    });

// private routes VVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');
    Route::post('/change-password', ChangePasswordController::class)
        ->name('change-password');

    Route::prefix('users')
        ->name('users.')
        ->controller(UserController::class)
        ->group(function () {
            Route::get('/', 'getAll');
            Route::get('/{user}', 'getOne')
                ->missing([UserController::class, 'notFound']);
            Route::post('/{user}',  'updateName')
                ->missing([UserController::class, 'notFound']);

            Route::post('/{user}/roles/{role_id}',  'assignRole')
                ->whereNumber('user')
                ->whereNumber('role_id')
                ->missing([UserController::class, 'notFound']);
            Route::delete('/{user}/roles/{role_id}',  'revokeRole')
                ->whereNumber('user')
                ->whereNumber('role_id')
                ->missing([UserController::class, 'notFound']);

            Route::delete('/{user}',  'destroy')
                ->missing([UserController::class, 'notFound']);
        });
    Route::prefix('products')
        ->name('products.')
        ->controller(ProductController::class)
        ->group(function () {

            Route::post('/', 'storeProduct');
            Route::post('/{product}',  'updateProduct')
                ->missing([ProductController::class, 'productNotFound']);
            Route::delete('/{product}', 'destroyProduct')
                ->missing([ProductController::class, 'productNotFound']);
        });
    Route::prefix('categories')
        ->name('categories.')
        ->controller(CategoryController::class)
        ->group(function () {

            Route::post('/', 'store')->name('store');
            Route::post('/{category}',  'update')
                ->missing([CategoryController::class, 'notFound'])
                ->name('update');
            Route::delete('/{category}',  'destroy')
                ->missing([CategoryController::class, 'notFound'])
                ->name('destroy');
        });
    Route::prefix('carts')
        ->name('carts.')
        ->group(function () {
            Route::get('/my-cart', [CartController::class, 'getCart'])->name('my-cart');
            Route::delete('/my-cart', [CartController::class, 'abandon'])->name('delete-cart');
            Route::post('/my-cart/{product}', [CartController::class, 'push'])
                ->missing([CartController::class, 'notFound'])->name('push-item');
            Route::delete('/my-cart/{product}', [CartController::class, 'pull'])
                ->missing([CartController::class, 'notFound'])->name('pull-item');
        });
    Route::prefix('orders')
        ->name('orders.')
        ->group(function () {
            Route::get('/', [OrderController::class, 'getAll']);
            Route::get('/pending', [OrderController::class, 'getPendingOrders']);
            Route::post('/order-now', [OrderController::class, 'orderNow']);
            Route::post('/deliver/{order}', [OrderController::class, 'deliverOrder'])
                ->missing([OrderController::class, 'notFound']);
            Route::delete('/cancel/{order}', [OrderController::class, 'cancelOrder'])
                ->missing([OrderController::class, 'notFound']);
            Route::get('/{order}', [OrderController::class, 'getOne'])
                ->missing([OrderController::class, 'notFound']);
        });
});
