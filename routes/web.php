<?php

declare(strict_types=1);

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

Route::get('gardens', GardensIndexController::class)->middleware(['auth', 'verified'])->name('gardens.index');
Route::get('gardens/{garden}', GardenShowController::class)->middleware(['auth', 'verified'])->name('gardens.show');

Route::middleware('auth')->group(function () {
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');
});

require __DIR__.'/auth.php';
