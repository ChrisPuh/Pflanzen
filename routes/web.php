<?php

declare(strict_types=1);

use App\Http\Controllers\Area\AreaCreateController;
use App\Http\Controllers\Area\AreaDeleteController;
use App\Http\Controllers\Area\AreaEditController;
use App\Http\Controllers\Area\AreaShowController;
use App\Http\Controllers\Area\AreasIndexController;
use App\Http\Controllers\Garden\GardenCreateController;
use App\Http\Controllers\Garden\GardenDeleteController;
use App\Http\Controllers\Garden\GardenEditController;
use App\Http\Controllers\Garden\GardenShowController;
use App\Http\Controllers\Garden\GardensIndexController;
use App\Http\Controllers\Plants\PlantShowController;
use App\Http\Controllers\Plants\PlantsIndexController;
use App\Http\Controllers\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('plants', PlantsIndexController::class)->middleware(['auth', 'verified'])->name('plants.index');
Route::get('plants/{plant}', PlantShowController::class)->middleware(['auth', 'verified'])->name('plants.show');

Route::get('areas', AreasIndexController::class)->middleware(['auth', 'verified'])->name('areas.index');
Route::get('areas/create', [AreaCreateController::class, 'create'])->middleware(['auth', 'verified'])->name('areas.create');
Route::post('areas', [AreaCreateController::class, 'store'])->middleware(['auth', 'verified'])->name('areas.store');
Route::get('areas/{area}', AreaShowController::class)->middleware(['auth', 'verified'])->name('areas.show');
Route::get('areas/{area}/edit', [AreaEditController::class, 'edit'])->middleware(['auth', 'verified'])->name('areas.edit');
Route::put('areas/{area}', [AreaEditController::class, 'update'])->middleware(['auth', 'verified'])->name('areas.update');
Route::delete('areas/{area}', [AreaDeleteController::class, 'destroy'])->middleware(['auth', 'verified'])->name('areas.destroy');
Route::post('areas/{areaId}/restore', [AreaDeleteController::class, 'restore'])->middleware(['auth', 'verified'])->name('areas.restore');
Route::delete('areas/{areaId}/force', [AreaDeleteController::class, 'forceDelete'])->middleware(['auth', 'verified'])->name('areas.force-delete');

Route::get('gardens', GardensIndexController::class)->middleware(['auth', 'verified'])->name('gardens.index');
Route::get('gardens/archived', [GardensIndexController::class, 'archived'])->middleware(['auth', 'verified'])->name('gardens.archived');
Route::get('gardens/create', [GardenCreateController::class, 'create'])->middleware(['auth', 'verified'])->name('gardens.create');
Route::post('gardens', [GardenCreateController::class, 'store'])->middleware(['auth', 'verified'])->name('gardens.store');
Route::get('gardens/{garden}', GardenShowController::class)->middleware(['auth', 'verified'])->name('gardens.show');
Route::get('gardens/{garden}/edit', [GardenEditController::class, 'edit'])->middleware(['auth', 'verified'])->name('gardens.edit');
Route::put('gardens/{garden}', [GardenEditController::class, 'update'])->middleware(['auth', 'verified'])->name('gardens.update');
Route::delete('gardens/{garden}', [GardenDeleteController::class, 'destroy'])->middleware(['auth', 'verified'])->name('gardens.destroy');
Route::post('gardens/{garden}/restore', [GardenDeleteController::class, 'restore'])->middleware(['auth', 'verified'])->name('gardens.restore');

Route::middleware('auth')->group(function () {
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');
});

require __DIR__.'/auth.php';
