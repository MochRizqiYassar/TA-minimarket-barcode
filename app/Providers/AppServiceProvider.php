<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OcrService;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OcrService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
{
    Paginator::useBootstrap();
}

}
