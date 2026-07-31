<?php

namespace App\Providers;

use App\Models\StoreSetting;
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
    }
}
