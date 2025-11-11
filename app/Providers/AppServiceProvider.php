<?php

namespace App\Providers;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        EncryptCookies::except('theme-config');

        // Define Gate for Laba Rugi (Profit & Loss Report) - Admin only
        Gate::define('viewLabaRugi', function ($user) {
            return $user->role === 'admin';
        });

        // Define Gate for Transaction Management - Admin only
        Gate::define('manageTransactions', function ($user) {
            return $user->role === 'admin';
        });
    }
}
