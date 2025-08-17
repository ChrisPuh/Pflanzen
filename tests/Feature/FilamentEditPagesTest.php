<?php

declare(strict_types=1);

use App\Enums\PlantCategoryEnum;
use App\Enums\PlantTypeEnum;
use App\Models\Category;
use App\Models\Plant;
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

it('can access edit plant page as admin', function (): void {
    $plant = Plant::factory()->withCategories([PlantCategoryEnum::Indoor])->create();

    $response = $this->actingAs($this->admin)->get("/admin/plants/{$plant->id}/edit");

    $response->assertSuccessful();
});

it('can access edit plant type page as admin', function (): void {
    $plantType = PlantType::where('name', PlantTypeEnum::Tree)->first();

    $response = $this->actingAs($this->admin)->get("/admin/plant-types/{$plantType->id}/edit");

    $response->assertSuccessful();
});

it('can access edit plant category page as admin', function (): void {
    $category = Category::where('name', PlantCategoryEnum::Indoor)->first();

    $response = $this->actingAs($this->admin)->get("/admin/plant-categories/{$category->id}/edit");

    $response->assertSuccessful();
});

it('denies access to edit plant page for regular users', function (): void {
    $user = User::factory()->create();
    $user->assignRole('user');
    $plant = Plant::factory()->withCategories([PlantCategoryEnum::Indoor])->create();

    $response = $this->actingAs($user)->get("/admin/plants/{$plant->id}/edit");

    $response->assertForbidden();
});

it('denies access to edit plant type page for regular users', function (): void {
    $user = User::factory()->create();
    $user->assignRole('user');
    $plantType = PlantType::where('name', PlantTypeEnum::Tree)->first();

    $response = $this->actingAs($user)->get("/admin/plant-types/{$plantType->id}/edit");

    $response->assertForbidden();
});

it('denies access to edit plant category page for regular users', function (): void {
    $user = User::factory()->create();
    $user->assignRole('user');
    $category = Category::where('name', PlantCategoryEnum::Indoor)->first();

    $response = $this->actingAs($user)->get("/admin/plant-categories/{$category->id}/edit");

    $response->assertForbidden();
});
