<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\User;
use App\Policies\OrderPolicy;
use App\Policies\User\UserPolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(\App\Providers\TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
        Gate::policy(User::class, UserPolicy::class);
        Gate::define('manage', [UserPolicy::class, 'manage']);
        Gate::define('get', [UserPolicy::class, 'get']);
        Gate::define('access-address', function (User $user, Address $address) {
            return $user->id === $address->user_id
                || $user->hasRole(['super_admin', 'admin', 'manager'])
                ? Response::allow()
                : Response::deny("You do not have permission to access this address!");
        });
        Gate::define('deliver-order', [OrderPolicy::class, 'deliver']);
        Gate::define('my-order', [OrderPolicy::class, 'getOne']);
    }
}
