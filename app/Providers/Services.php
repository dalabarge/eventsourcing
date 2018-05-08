<?php

namespace App\Providers;

use App\Store\Contracts\Store;
use App\Store\Factory;
use Illuminate\Support\ServiceProvider;

class Services extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->app->bind(Store::class, function () {
            return Factory::make();
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
    }
}
