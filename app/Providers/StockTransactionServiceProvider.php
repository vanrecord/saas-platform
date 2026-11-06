<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\StockTransaction;

class StockTransactionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('stock-transaction', function($app) {
            return new StockTransaction();
        });
    }
}
