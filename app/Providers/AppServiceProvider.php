<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Services\OcrService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

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

        if (! $this->app->environment('local')) {
            URL::forceScheme('https');
        }
    }
}
