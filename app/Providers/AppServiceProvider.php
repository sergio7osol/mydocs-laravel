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
        // \Illuminate\Database\Eloquent\Model::preventLazyLoading(!app()->isProduction());
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.default');
    }
}
