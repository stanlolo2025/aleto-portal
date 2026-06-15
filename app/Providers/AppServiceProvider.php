<?php

namespace App\Providers;

use App\Services\AuditLoggerService;
use App\Services\Contracts\AuditLoggerInterface;
use App\Services\Contracts\FraudDetectorInterface;
use App\Services\Contracts\GrantServiceInterface;
use App\Services\Contracts\LifecycleServiceInterface;
use App\Services\Contracts\ProxyServiceInterface;
use App\Services\Contracts\RegistryServiceInterface;
use App\Services\FraudDetectorService;
use App\Services\LifecycleService;
use App\Services\ProxyService;
use App\Services\RegistryService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuditLoggerInterface::class, AuditLoggerService::class);
        $this->app->bind(RegistryServiceInterface::class, RegistryService::class);
        $this->app->bind(LifecycleServiceInterface::class, LifecycleService::class);
        $this->app->bind(FraudDetectorInterface::class, FraudDetectorService::class);
        $this->app->bind(ProxyServiceInterface::class, ProxyService::class);
    }

    public function boot(): void
    {
        //
    }
}
