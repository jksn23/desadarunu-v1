<?php

namespace App\Providers;

use App\Services\ActivityLogger;
use App\Services\GeminiService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GeminiService::class);
        $this->app->singleton(ActivityLogger::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            app(ActivityLogger::class)->log([
                'user' => $event->user,
                'action' => 'login',
                'module' => 'auth',
                'description' => 'Pengguna masuk ke aplikasi.',
            ]);
        });

        Event::listen(Logout::class, function (Logout $event): void {
            app(ActivityLogger::class)->log([
                'user' => $event->user,
                'action' => 'logout',
                'module' => 'auth',
                'description' => 'Pengguna keluar dari aplikasi.',
            ]);
        });
    }
}
