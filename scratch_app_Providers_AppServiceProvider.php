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
        // 🚀 FORÇAR HTTPS ABSOLUTO (Evita avisos de segurança no login/formulários)
        \Illuminate\Support\Facades\URL::forceScheme('https');

        \Illuminate\Pagination\Paginator::useBootstrapFour();
    }
}
