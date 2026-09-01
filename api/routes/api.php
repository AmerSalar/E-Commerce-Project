<?php

use App\Http\Controllers\API\{
    AddressController,
    CartController,
    CategoryController,
    OrderController,
    ProductController,
    UserController,
    UserProfileController
};
use App\Http\Controllers\Auth\{
    LoginController,
    LogoutController,
    RefreshController,
    RegisterController,
    ResetPasswordController
};
use App\Http\Middleware\Auth\AuthenticateFromCookie;
use Illuminate\Support\Facades\Route;

/**
 *
 * PUBLIC ROUTES NO JWT NEED
 *
 */
Route::post('/register', RegisterController::class)->name('register')
    ->middleware('throttle:10,1');
Route::post('/login', LoginController::class)->name('login')
    ->middleware('throttle:5,1');
Route::post('/refresh', RefreshController::class)->name('refresh')
    ->middleware('throttle:5,1');

/**
 *
 * RESET PASSWORD
 *
 */
Route::post('/forgot-password', [ResetPasswordController::class, 'forgot'])
    ->name('forgot-password')
    ->middleware('throttle:3,1');
Route::post('/verify-code', [ResetPasswordController::class, 'verify'])
    ->name('verify-code')
    ->middleware('throttle:10,5');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('reset-password')
    ->middleware('throttle:2,1');



/**
 *
 * PUBLIC PRODUCTS
 *
 */
Route::prefix('products')
    ->name('products.')
    ->controller(ProductController::class)
    ->group(function () {

        Route::get('/', 'index');
        Route::get('/{product}', 'show');
    });
/**
 *
 * PUBLIC CATEGORIES
 *
 */
Route::prefix('categories')
    ->name('categories.')
    ->controller(CategoryController::class)
    ->group(function () {

        Route::get('/', 'index')->name('get-all');
        Route::get('/{category}', 'show')
            ->name('get-one');
    });

/**
 *
 * AUTHENTICATED ROUTES (PRIVATE) NEED JWT
 *
 */
Route::middleware([AuthenticateFromCookie::class, 'auth:api'])->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');
    /**
     *
     * My Profile (current user)
     *
     */
    Route::prefix('me')
        ->name("me.")
        ->controller(UserProfileController::class)
        ->group(function () {
            Route::get('/', 'profile');
            Route::post('/', 'update');
            Route::post('/change-password', 'password');
        });
    /**
     *
     * USERS
     *
     */
    Route::prefix('users')
        ->name('users.')
        ->controller(UserController::class)
        ->group(function () {
            Route::get('/', 'index');
            Route::get('/{user}', 'show');
            Route::post('/{user}', 'update');
            Route::post('/{user}/roles/{role_id}', 'assign')
                ->whereNumber('user')
                ->whereNumber('role_id');
            Route::delete('/{user}/roles/{role_id}', 'revoke')
                ->whereNumber('user')
                ->whereNumber('role_id');
            Route::delete('/{user}', 'destroy');
        });
    /**
     *
     * ADDRESSES
     *
     */
    Route::prefix('addresses')
        ->name('addresses.')
        ->controller(AddressController::class)
        ->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{address}', 'show');
            Route::post('/{address}', 'update');
            Route::delete('/{address}', 'destroy');
        });
    /**
     *
     * PRODUCTS
     *
     */
    Route::prefix('products')
        ->name('products.')
        ->controller(ProductController::class)
        ->group(function () {
            Route::post('/', 'store');
            Route::post('/{product}', 'update');
            Route::delete('/{product}', 'destroy');
        });
    /**
     *
     * CATEGORIES
     *
     */
    Route::prefix('categories')
        ->name('categories.')
        ->controller(CategoryController::class)
        ->group(function () {
            Route::post('/', 'store')->name('store');
            Route::post('/{category}', 'update')
                ->name('update');
            Route::delete('/{category}', 'destroy')
                ->name('destroy');
        });
    /**
     *
     * CARTS
     *
     */
    Route::prefix('carts')
        ->name('carts.')
        ->group(function () {
            Route::get('/my-cart', [CartController::class, 'index'])->name('my-cart');
            Route::delete('/my-cart', [CartController::class, 'abandon'])->name('delete-cart');
            Route::post('/my-cart/{product}', [CartController::class, 'push']);
            Route::delete('/my-cart/{product}', [CartController::class, 'pull']);
        });

    /**
     *
     * ORDERS
     *
     */
    Route::prefix('orders')
        ->name('orders.')
        ->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::get('/pending', [OrderController::class, 'pending']);
            Route::post('/order-now', [OrderController::class, 'order']);
            Route::post('/deliver/{order}', [OrderController::class, 'deliver']);
            Route::delete('/cancel/{order}', [OrderController::class, 'cancel']);
            Route::get('/{order}', [OrderController::class, 'show']);
        });
});
