<?php

namespace App\Providers;

use App\Services\AuditService;
use Illuminate\Auth\Events\Failed;
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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event) {
            AuditService::log('login_success');
        });

        Event::listen(Logout::class, function (Logout $event) {
            AuditService::log('logout');
        });

        Event::listen(Failed::class, function (Failed $event) {
            AuditService::log('login_failure', null, null, false, [
                'email' => $event->credentials['email'] ?? 'unknown',
            ]);
        });
    }
}
