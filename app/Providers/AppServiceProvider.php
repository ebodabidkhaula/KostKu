<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // FIX: Force HTTPS scheme karena Render/Railway pakai reverse proxy
        // yang melakukan SSL termination, sehingga Laravel mengira request HTTP.
        if (env('APP_ENV') === 'production' || str_contains(env('APP_URL', ''), 'https://')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }

        Schema::defaultStringLength(191);
    }
}
