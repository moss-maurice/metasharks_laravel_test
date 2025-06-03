<?php

namespace App\Providers;

use App\Contracts\AuthServiceInterface;
use App\Contracts\NotifyServiceInterface;
use App\Contracts\RoomServiceInterface;
use App\Services\AuthService;
use App\Services\NotifyService;
use App\Services\RoomService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(RoomServiceInterface::class, RoomService::class);
        $this->app->bind(NotifyServiceInterface::class, NotifyService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
