<?php

namespace App\Providers;

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
        $this->app->singleton('cloudfront', function () {
            return 'https://d1h4q8vrlfl3k9.cloudfront.net/';
        });

        $this->app->singleton('cloudfrontflags', function () {
            return asset("assets/flags/");
        });
        $this->app->singleton('cloudfrontflagsx2', function () {
            return asset("assets/flags/");
        });
    }
}
