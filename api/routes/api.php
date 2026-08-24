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
    ->middleware('throttle:2,1');
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

        Route::get('/', 'getProducts');
        Route::get('/{product}', 'getSingleProduct');
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

        Route::get('/', 'getAll')->name('getAll');
        Route::get('/{category}', 'getOne')
            ->name('getOne');
    });

/**
 *
 * AUTHENTICATED ROUTES (PRIVATE) NEED JWT
 *
 */
Route::middleware('auth:api')->group(function () {
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
            Route::post('/change-password', 'changePassword');
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
            Route::get('/', 'getAll');
            Route::get('/{user}', 'getOne');
            Route::post('/{user}',  'updateName');
            Route::post('/{user}/roles/{role_id}',  'assignRole')
                ->whereNumber('user')
                ->whereNumber('role_id');
            Route::delete('/{user}/roles/{role_id}',  'revokeRole')
                ->whereNumber('user')
                ->whereNumber('role_id');
            Route::delete('/{user}',  'destroy');
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
            Route::get('/', 'getAll');
            Route::post('/', 'store');
            Route::get('/{address}', 'getOne');
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
            Route::post('/', 'storeProduct');
            Route::post('/{product}',  'updateProduct');
            Route::delete('/{product}', 'destroyProduct');
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
            Route::post('/{category}',  'update')
                ->name('update');
            Route::delete('/{category}',  'destroy')
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
            Route::get('/my-cart', [CartController::class, 'getCart'])->name('my-cart');
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
            Route::get('/', [OrderController::class, 'getAll']);
            Route::get('/pending', [OrderController::class, 'getPendingOrders']);
            Route::post('/order-now', [OrderController::class, 'orderNow']);
            Route::post('/deliver/{order}', [OrderController::class, 'deliverOrder']);
            Route::delete('/cancel/{order}', [OrderController::class, 'cancelOrder']);
            Route::get('/{order}', [OrderController::class, 'getOne']);
        });
});
