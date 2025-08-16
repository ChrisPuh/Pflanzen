<?php

declare(strict_types=1);

use App\Enums\PlantCategoryEnum;
use App\Enums\PlantTypeEnum;
use App\Models\Plant;
use App\Models\PlantCategory;
use App\Models\PlantType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
});

it('can access plant types resource as admin', function (): void {
    $response = $this->actingAs($this->admin)->get('/admin/plant-types');

    $response->assertSuccessful();
});

it('can access plant categories resource as admin', function (): void {
    $response = $this->actingAs($this->admin)->get('/admin/plant-categories');

    $response->assertSuccessful();
});

it('can access plants resource as admin', function (): void {
    $response = $this->actingAs($this->admin)->get('/admin/plants');

    $response->assertSuccessful();
});

it('shows plant resources exist in database with seeded data', function (): void {
    // PlantTypes and PlantCategories are now seeded via migration
    $plantType = PlantType::where('name', PlantTypeEnum::Tree)->first();
    $plantCategory = PlantCategory::where('name', PlantCategoryEnum::Indoor)->first();

    expect($plantType)->not->toBeNull();
    expect($plantCategory)->not->toBeNull();

    $plant = Plant::factory()->create([
        'plant_type_id' => $plantType->id,
    ]);

    $plant->plantCategories()->attach($plantCategory);

    expect($plant->plantType)->not->toBeNull();
    expect($plant->plantCategories)->toHaveCount(1);

    $this->assertDatabaseHas('plants', ['name' => $plant->name]);
    $this->assertDatabaseHas('plant_types', ['name' => PlantTypeEnum::Tree->value]);
    $this->assertDatabaseHas('plant_categories', ['name' => PlantCategoryEnum::Indoor->value]);
});
