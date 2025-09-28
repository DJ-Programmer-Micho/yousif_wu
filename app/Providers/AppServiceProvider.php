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
        $this->app->singleton('app_link', function () {
            return 'http://127.0.0.1:8000/';
            // return 'https://iraqremit.com/';
        });
        $this->app->singleton('cloudfront', function () {
            return 'https://d1a4xucvuho2c9.cloudfront.net/';
        });
        
        $this->app->singleton('master_email', function () {
            return 'michelshabo1@gmail.com';
        });

        $this->app->singleton('cloudfrontflags', function () {
            return asset("assets/flags/");
        });
        $this->app->singleton('cloudfrontflagsx2', function () {
            return asset("assets/flags/");
        });
    }
}
