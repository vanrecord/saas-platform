<?php

namespace App\Providers;

use App\Services\Country;
use Illuminate\Support\ServiceProvider;

class CountryFacadeServiceProvider extends ServiceProvider
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
        $this->app->bind('country', function($app) {
            return new Country();
        });
    }
}
