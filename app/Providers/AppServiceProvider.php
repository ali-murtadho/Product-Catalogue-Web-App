<?php

namespace App\Providers;

use App\Models\StoreSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
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
        View::composer('layouts.public', function ($view) {
            $view->with('storeSetting', StoreSetting::instance() ?? new StoreSetting());
        });

        $this->configureRateLimiting();
    }

    /**
     * Configure rate limiting for sensitive public routes.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('public-search', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('cart-operations', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        RateLimiter::for('order-submission', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
