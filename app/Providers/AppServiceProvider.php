<?php

namespace App\Providers;

use App\Services\EpasienApiService;
use App\Services\SessionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EpasienApiService::class);
        $this->app->singleton(SessionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
