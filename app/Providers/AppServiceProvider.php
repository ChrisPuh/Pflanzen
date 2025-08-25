<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Area\AreaPlantService;
use App\Services\Area\Contracts\AreaPlantServiceInterface;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Area\Contracts\AreaRepositoryInterface::class,
            \App\Repositories\Area\AreaRepository::class
        );
        $this->app->bind(
            \App\Repositories\Area\Contracts\AreaPlantRepositoryInterface::class,
            \App\Repositories\Area\AreaPlantRepository::class
        );
        $this->app->singleton(AreaPlantServiceInterface::class, AreaPlantService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
