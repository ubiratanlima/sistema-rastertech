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
        \Illuminate\Pagination\Paginator::useBootstrapFour();

        // 🚀 ESTABILIZAÇÃO GLOBAL DE URL (REMOÇÃO DE :8000)
        if (config('app.url')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
            \Illuminate\Support\Facades\URL::forceScheme('http');

            // Reforço específico para links de Paginação
            \Illuminate\Pagination\Paginator::currentPathResolver(function () {
                return config('app.url') . request()->getPathInfo();
            });
        }
    }
}
