<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        // Force HTTPS for all URLs in production
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Share site name with all views
        view()->composer('*', function ($view) {
            $view->with('siteName', \App\Models\Setting::get('site_name', config('app.name', 'HK Checklist')));
        });
    }
}
