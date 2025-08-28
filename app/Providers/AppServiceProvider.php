<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Area\AreaPlantRepository;
use App\Repositories\Area\AreaRepository;
use App\Repositories\Area\Contracts\AreaPlantRepositoryInterface;
use App\Repositories\Area\Contracts\AreaRepositoryInterface;
use App\Repositories\Garden\Contracts\GardenRepositoryInterface;
use App\Repositories\Garden\GardenRepository;
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
            AreaRepositoryInterface::class,
            AreaRepository::class
        );
        $this->app->bind(
            AreaPlantRepositoryInterface::class,
            AreaPlantRepository::class
        );
        $this->app->singleton(AreaPlantServiceInterface::class, AreaPlantService::class);
        $this->app->singleton(GardenRepositoryInterface::class, GardenRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
