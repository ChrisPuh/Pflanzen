<?php

declare(strict_types=1);

use App\Http\Controllers\Area;
use App\Http\Controllers\Garden;
use App\Http\Controllers\Plants;
use App\Http\Controllers\Settings;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', function () {
    $stats = [
        'plants_count' => \App\Models\Plant::count(),
        'plant_types_count' => \App\Models\PlantType::count(),
        'categories_count' => \App\Models\Category::count(),
        'users_count' => \App\Models\User::count(),
        'gardens_count' => \App\Models\Garden::count(),
        'areas_count' => \App\Models\Area::count(),
    ];
    
    return view('about', compact('stats'));
})->name('about');

Route::get('/privacy', function () {
    return view('legal.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('legal.terms');
})->name('terms');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Plants Resource Routes
    Route::controller(Plants\PlantsIndexController::class)->group(function () {
        Route::get('plants', '__invoke')->name('plants.index');
    });

    Route::controller(Plants\PlantShowController::class)->group(function () {
        Route::get('plants/{plant}', '__invoke')->name('plants.show');
    });

    // Areas Resource Routes
    Route::prefix('areas')->name('areas.')->group(function () {
        Route::get('/', Area\AreasIndexController::class)->name('index');
        Route::get('create', [Area\AreaCreateController::class, 'create'])->name('create');
        Route::post('/', [Area\AreaCreateController::class, 'store'])->name('store');
        Route::get('{area}', Area\AreaShowController::class)->name('show');
        Route::get('{area}/edit', [Area\AreaEditController::class, 'edit'])->name('edit');
        Route::put('{area}', [Area\AreaEditController::class, 'update'])->name('update');
        Route::delete('{area}', [Area\AreaDeleteController::class, 'destroy'])->name('destroy');

        // Plant Management Routes
        Route::post('{area}/plants', [Area\AreaPlantController::class, 'store'])->name('plants.store');
        Route::delete('{area}/plants/{plant}', [Area\AreaPlantController::class, 'destroy'])->name('plants.destroy');

        // Soft Delete Management
        Route::post('{areaId}/restore', [Area\AreaDeleteController::class, 'restore'])->name('restore');
        Route::delete('{areaId}/force', [Area\AreaDeleteController::class, 'forceDelete'])->name('force-delete');
    });

    // Gardens Resource Routes
    Route::prefix('gardens')->name('gardens.')->group(function () {
        Route::get('/', Garden\GardensIndexController::class)->name('index');
        Route::get('archived', [Garden\GardensIndexController::class, 'archived'])->name('archived');
        Route::get('create', [Garden\GardenCreateController::class, 'create'])->name('create');
        Route::post('/', [Garden\GardenCreateController::class, 'store'])->name('store');
        Route::get('{garden}', Garden\GardenShowController::class)->name('show');
        Route::get('{garden}/edit', [Garden\GardenEditController::class, 'edit'])->name('edit');
        Route::put('{garden}', [Garden\GardenEditController::class, 'update'])->name('update');
        Route::delete('{garden}', [Garden\GardenDeleteController::class, 'destroy'])->name('destroy');

        // Soft Delete Management
        Route::post('{garden}/restore', [Garden\GardenDeleteController::class, 'restore'])->name('restore');
    });

    // Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        // Profile Management
        Route::prefix('profile')->name('profile.')->controller(Settings\ProfileController::class)->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::put('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
        });

        // Password Management
        Route::prefix('password')->name('password.')->controller(Settings\PasswordController::class)->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::put('/', 'update')->name('update');
        });

        // Appearance Settings
        Route::prefix('appearance')->name('appearance.')->controller(Settings\AppearanceController::class)->group(function () {
            Route::get('/', 'edit')->name('edit');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
