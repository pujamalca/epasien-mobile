<?php

namespace App\Providers;

use App\Listeners\FcmTokenListener;
use App\Services\EpasienApiService;
use App\Services\FcmService;
use App\Services\SessionService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Native\Mobile\Events\PushNotification\TokenGenerated;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EpasienApiService::class);
        $this->app->singleton(SessionService::class);
        $this->app->singleton(FcmService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $baseUrl = config('epasien.base_url', '');
        if (app()->isProduction() && !str_starts_with($baseUrl, 'https://')) {
            throw new \RuntimeException('EPASIEN_BASE_URL harus menggunakan HTTPS di production.');
        }

        Event::listen(TokenGenerated::class, FcmTokenListener::class);
    }
}
