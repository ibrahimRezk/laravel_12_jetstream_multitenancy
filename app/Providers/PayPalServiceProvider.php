<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PayPalService;

class PayPalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PayPalService::class, function ($app) {
            return new PayPalService();
        });
    }

    public function boot(): void
    {
        //
    }
}