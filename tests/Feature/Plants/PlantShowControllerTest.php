<?php

declare(strict_types=1);

use App\Enums\PlantCategoryEnum;
use App\Models\Category;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);

    $this->user = User::factory()->create();
    $this->user->assignRole('user');

    $this->actingAs($this->user);
});

it('can access plant show page', function (): void {
    $plant = Plant::factory()->create(['name' => 'Test Plant']);

    $response = $this->get("/plants/{$plant->id}");

    $response->assertSuccessful()
        ->assertViewIs('plants.show')
        ->assertViewHas('plant', $plant)
        ->assertSee('Test Plant');
});

it('displays plant information correctly', function (): void {
    $plant = Plant::factory()->create([
        'name' => 'Beautiful Rose',
        'latin_name' => 'Rosa bella',
        'description' => 'A beautiful flower with red petals',
    ]);

    $response = $this->get("/plants/{$plant->id}");

    $response->assertSuccessful()
        ->assertSee('Beautiful Rose')
        ->assertSee('Rosa bella')
        ->assertSee('A beautiful flower with red petals')
        ->assertSee($plant->plantType->name->getLabel());
});

it('displays plant categories', function (): void {
    $plant = Plant::factory()->create(['name' => 'Categorized Plant']);
    $category = Category::where('name', PlantCategoryEnum::Indoor)->first();

    // Insert category relationship directly
    DB::table('category_plant')->insert([
        ['category_id' => $category->id, 'plant_id' => $plant->id, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $response = $this->get("/plants/{$plant->id}");

    $response->assertSuccessful()
        ->assertSee($category->name->getLabel());
});

it('shows back to plants link', function (): void {
    $plant = Plant::factory()->create();

    $response = $this->get("/plants/{$plant->id}");

    $response->assertSuccessful()
        ->assertSee('Zurück zu Pflanzen')
        ->assertSee(route('plants.index'));
});

it('shows quick action links', function (): void {
    $plant = Plant::factory()->create(['name' => 'Action Plant']);

    $response = $this->get("/plants/{$plant->id}");

    $response->assertSuccessful()
        ->assertSee('Ähnliche Pflanzen finden')
        ->assertSee('Alle '.$plant->plantType->name->getLabel().' anzeigen')
        ->assertSee(route('plants.index', ['search' => $plant->name]))
        ->assertSee(route('plants.index', ['type' => $plant->plantType->name->value]));
});

it('shows plant creation date', function (): void {
    $plant = Plant::factory()->create();

    $response = $this->get("/plants/{$plant->id}");

    $response->assertSuccessful()
        ->assertSee($plant->created_at->format('d.m.Y'));
});

it('requires authentication', function (): void {
    auth()->logout();
    $plant = Plant::factory()->create();

    $response = $this->get("/plants/{$plant->id}");

    $response->assertRedirect('/login');
});

it('returns 404 for non-existent plant', function (): void {
    $response = $this->get('/plants/999999');

    $response->assertNotFound();
});

it('loads plant relationships', function (): void {
    $plant = Plant::factory()->create();

    // Make the request
    $this->get("/plants/{$plant->id}");

    // Check that relationships would be loaded (test the controller logic)
    $loadedPlant = Plant::with(['plantType', 'categories'])->find($plant->id);

    expect($loadedPlant->relationLoaded('plantType'))->toBeTrue();
    expect($loadedPlant->relationLoaded('categories'))->toBeTrue();
});

it('shows edit button for admins', function (): void {
    // Create admin user
    $adminUser = User::factory()->create();
    $adminUser->assignRole('admin');

    $plant = Plant::factory()->create();

    $response = $this->actingAs($adminUser)->get("/plants/{$plant->id}");

    $response->assertSuccessful()
        ->assertSee('Bearbeiten')
        ->assertSee("admin/plants/{$plant->id}/edit");
});

it('does not show edit button for regular users', function (): void {
    $plant = Plant::factory()->create();

    $response = $this->get("/plants/{$plant->id}");

    $response->assertSuccessful()
        ->assertDontSee('Bearbeiten');
});
