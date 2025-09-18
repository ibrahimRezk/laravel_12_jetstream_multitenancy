<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
    use Illuminate\Support\Facades\URL;

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
                        JsonResource::withoutWrapping(); // this is to remove word data when calling data from any resource like usersResource collection


                        // if ($this->app->environment('production') || config('app.env') === 'localtonet') { // Add 'localtonet' if you have a specific env for it
                // URL::forceScheme('https');
            // }
    }
}
