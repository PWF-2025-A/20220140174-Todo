<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        Gate::define('admin', function ($user) {
            return $user->is_admin == true;
        });
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
