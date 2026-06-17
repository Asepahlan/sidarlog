<?php

namespace App\Providers;

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
        // Super Admin bypass — all permission checks return true
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Register Observers for automatic Activity Log
        \App\Models\Item::observe(\App\Observers\ItemObserver::class);
        \App\Models\StockTransaction::observe(\App\Observers\StockTransactionObserver::class);
        \App\Models\StockOpname::observe(\App\Observers\StockOpnameObserver::class);
        \App\Models\StockMutation::observe(\App\Observers\StockMutationObserver::class);
    }
}
