<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Repositories\Eloquent\ItemRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Repositories\Interfaces\ItemRepositoryInterface::class, \App\Repositories\Eloquent\ItemRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\TransactionRepositoryInterface::class, \App\Repositories\Eloquent\TransactionRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\StockOpnameRepositoryInterface::class, \App\Repositories\Eloquent\StockOpnameRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
