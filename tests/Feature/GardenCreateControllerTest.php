<?php

declare(strict_types=1);

use App\Enums\Garden\GardenTypeEnum;
use App\Models\Garden;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

describe('GardenCreateController', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->admin->assignRole($this->adminRole);
    });

    describe('Create Form (GET /gardens/create)', function () {
        it('redirects unauthenticated users to login', function () {
            $response = $this->get(route('gardens.create'));

            $response->assertRedirect(route('login'));
        });

        it('allows authenticated users to access create form', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.create'));

            $response->assertOk()
                ->assertViewIs('gardens.create')
                ->assertViewHas('gardenTypes');
        });

        it('passes garden types to view', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.create'));

            $response->assertOk();
            
            $gardenTypes = $response->viewData('gardenTypes');
            expect($gardenTypes)->toBeArray();
            expect($gardenTypes)->not->toBeEmpty();
            
            foreach ($gardenTypes as $type) {
                expect($type)->toBeInstanceOf(GardenTypeEnum::class);
            }
        });

        it('displays create form correctly', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.create'));

            $response->assertOk()
                ->assertSee('Neuen Garten erstellen')
                ->assertSee('Gartenname')
                ->assertSee('Gartentyp')
                ->assertSee('Beschreibung')
                ->assertSee('Größe (m²)')
                ->assertSee('Standort')
                ->assertSee('GPS-Koordinaten')
                ->assertSee('Garten erstellen');
        });

        it('includes all garden types in form options', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.create'));

            $response->assertOk()
                ->assertSee('Nutzgarten');
        });
    });

    describe('Store Garden (POST /gardens)', function () {
        it('redirects unauthenticated users to login', function () {
            $response = $this->post(route('gardens.store'), [
                'name' => 'Test Garden',
                'type' => 'vegetable_garden',
            ]);

            $response->assertRedirect(route('login'));
        });

        it('creates garden with valid data', function () {
            $gardenData = [
                'name' => 'Mein Testgarten',
                'description' => 'Ein wunderschöner Testgarten',
                'type' => 'vegetable_garden',
                'size_sqm' => 25.5,
                'location' => 'Hinterhof',
                'city' => 'Berlin',
                'postal_code' => '10115',
                'is_active' => true,
                'established_at' => '2023-05-15',
            ];

            $response = $this->actingAs($this->user)->post(route('gardens.store'), $gardenData);

            // Check redirect
            $garden = Garden::where('name', 'Mein Testgarten')->first();
            expect($garden)->not->toBeNull();
            
            $response->assertRedirect(route('gardens.show', $garden))
                ->assertSessionHas('success', 'Garten wurde erfolgreich erstellt!');

            // Check database
            expect($garden->name)->toBe('Mein Testgarten');
            expect($garden->description)->toBe('Ein wunderschöner Testgarten');
            expect($garden->type)->toBe(GardenTypeEnum::VegetableGarden);
            expect($garden->size_sqm)->toBe(25.5);
            expect($garden->location)->toBe('Hinterhof');
            expect($garden->city)->toBe('Berlin');
            expect($garden->postal_code)->toBe('10115');
            expect($garden->is_active)->toBeTrue();
            expect($garden->established_at->format('Y-m-d'))->toBe('2023-05-15');
            expect($garden->user_id)->toBe($this->user->id);
        });

        it('creates garden with minimal required data', function () {
            $gardenData = [
                'name' => 'Minimal Garden',
                'type' => 'flower_garden',
            ];

            $response = $this->actingAs($this->user)->post(route('gardens.store'), $gardenData);

            $garden = Garden::where('name', 'Minimal Garden')->first();
            expect($garden)->not->toBeNull();
            
            $response->assertRedirect(route('gardens.show', $garden));

            expect($garden->name)->toBe('Minimal Garden');
            expect($garden->type)->toBe(GardenTypeEnum::FlowerGarden);
            expect($garden->description)->toBeNull();
            expect($garden->size_sqm)->toBeNull();
            expect($garden->location)->toBeNull();
            expect($garden->city)->toBeNull();
            expect($garden->postal_code)->toBeNull();
            expect($garden->coordinates)->toBeNull();
            expect($garden->is_active)->toBeTrue(); // Default value
            expect($garden->established_at)->toBeNull();
            expect($garden->user_id)->toBe($this->user->id);
        });

        it('creates garden with coordinates', function () {
            $gardenData = [
                'name' => 'Garden with GPS',
                'type' => 'herb_garden',
                'coordinates' => [
                    'latitude' => 52.5200,
                    'longitude' => 13.4050,
                ],
            ];

            $response = $this->actingAs($this->user)->post(route('gardens.store'), $gardenData);

            $garden = Garden::where('name', 'Garden with GPS')->first();
            expect($garden)->not->toBeNull();
            
            $response->assertRedirect(route('gardens.show', $garden));

            expect($garden->coordinates)->toBeArray();
            expect($garden->coordinates['latitude'])->toBe(52.5200);
            expect($garden->coordinates['longitude'])->toBe(13.4050);
        });

        it('handles empty coordinates correctly', function () {
            $gardenData = [
                'name' => 'Garden without GPS',
                'type' => 'vegetable_garden',
                'coordinates' => [
                    'latitude' => '',
                    'longitude' => '',
                ],
            ];

            $response = $this->actingAs($this->user)->post(route('gardens.store'), $gardenData);

            $garden = Garden::where('name', 'Garden without GPS')->first();
            expect($garden)->not->toBeNull();
            
            expect($garden->coordinates)->toBeNull();
        });
    });

    describe('Validation', function () {
        it('requires garden name', function () {
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'type' => 'vegetable_garden',
            ]);

            $response->assertSessionHasErrors('name');
            expect(Garden::count())->toBe(0);
        });

        it('requires garden type', function () {
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Test Garden',
            ]);

            $response->assertSessionHasErrors('type');
            expect(Garden::count())->toBe(0);
        });

        it('validates garden name length', function () {
            // Too short
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'A',
                'type' => 'vegetable_garden',
            ]);
            $response->assertSessionHasErrors('name');

            // Too long
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => str_repeat('A', 256),
                'type' => 'vegetable_garden',
            ]);
            $response->assertSessionHasErrors('name');

            expect(Garden::count())->toBe(0);
        });

        it('validates garden type enum', function () {
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Test Garden',
                'type' => 'invalid_garden_type',
            ]);

            $response->assertSessionHasErrors('type');
            expect(Garden::count())->toBe(0);
        });

        it('validates size is numeric and positive', function () {
            // Negative size
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Test Garden',
                'type' => 'vegetable_garden',
                'size_sqm' => -5,
            ]);
            $response->assertSessionHasErrors('size_sqm');

            // Non-numeric size
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Test Garden',
                'type' => 'vegetable_garden',
                'size_sqm' => 'not-a-number',
            ]);
            $response->assertSessionHasErrors('size_sqm');

            expect(Garden::count())->toBe(0);
        });

        it('validates description length', function () {
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Test Garden',
                'type' => 'vegetable_garden',
                'description' => str_repeat('A', 1001),
            ]);

            $response->assertSessionHasErrors('description');
            expect(Garden::count())->toBe(0);
        });

        it('validates coordinates format', function () {
            // Invalid latitude
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Test Garden',
                'type' => 'vegetable_garden',
                'coordinates' => [
                    'latitude' => 95, // Out of range
                    'longitude' => 13.4050,
                ],
            ]);
            $response->assertSessionHasErrors('coordinates.latitude');

            // Invalid longitude
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Test Garden',
                'type' => 'vegetable_garden',
                'coordinates' => [
                    'latitude' => 52.5200,
                    'longitude' => 185, // Out of range
                ],
            ]);
            $response->assertSessionHasErrors('coordinates.longitude');

            expect(Garden::count())->toBe(0);
        });

        it('validates established date is not in future', function () {
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Test Garden',
                'type' => 'vegetable_garden',
                'established_at' => now()->addDay()->format('Y-m-d'),
            ]);

            $response->assertSessionHasErrors('established_at');
            expect(Garden::count())->toBe(0);
        });

        it('accepts valid established date', function () {
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Test Garden',
                'type' => 'vegetable_garden',
                'established_at' => now()->subYear()->format('Y-m-d'),
            ]);

            $response->assertRedirect();
            $response->assertSessionHasNoErrors();
            expect(Garden::count())->toBe(1);
        });
    });

    describe('Authorization', function () {
        it('allows regular users to create gardens', function () {
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'User Garden',
                'type' => 'vegetable_garden',
            ]);

            $response->assertRedirect();
            $response->assertSessionHasNoErrors();
            expect(Garden::count())->toBe(1);
        });

        it('allows admin users to create gardens', function () {
            $response = $this->actingAs($this->admin)->post(route('gardens.store'), [
                'name' => 'Admin Garden',
                'type' => 'flower_garden',
            ]);

            $response->assertRedirect();
            $response->assertSessionHasNoErrors();
            expect(Garden::count())->toBe(1);
        });

        it('associates garden with authenticated user', function () {
            $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'User Garden',
                'type' => 'vegetable_garden',
            ]);

            $garden = Garden::first();
            expect($garden->user_id)->toBe($this->user->id);
        });
    });

    describe('Form Data Processing', function () {
        it('sets default is_active to true when not provided', function () {
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Default Active Garden',
                'type' => 'vegetable_garden',
            ]);

            $garden = Garden::first();
            expect($garden->is_active)->toBeTrue();
        });

        it('handles is_active checkbox correctly when checked', function () {
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Active Garden',
                'type' => 'vegetable_garden',
                'is_active' => '1',
            ]);

            $garden = Garden::first();
            expect($garden->is_active)->toBeTrue();
        });

        it('converts established_at to proper date format', function () {
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Date Garden',
                'type' => 'vegetable_garden',
                'established_at' => '2023-06-15',
            ]);

            $garden = Garden::first();
            expect($garden->established_at)->toBeInstanceOf(\Carbon\Carbon::class);
            expect($garden->established_at->format('Y-m-d'))->toBe('2023-06-15');
        });
    });

    describe('Response Structure', function () {
        it('redirects to garden show page on success', function () {
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Redirect Test Garden',
                'type' => 'vegetable_garden',
            ]);

            $garden = Garden::first();
            $response->assertRedirect(route('gardens.show', $garden));
        });

        it('includes success message in session', function () {
            $response = $this->actingAs($this->user)->post(route('gardens.store'), [
                'name' => 'Success Message Garden',
                'type' => 'vegetable_garden',
            ]);

            $response->assertSessionHas('success', 'Garten wurde erfolgreich erstellt!');
        });

        it('redirects back with errors on validation failure', function () {
            $response = $this->actingAs($this->user)
                ->from(route('gardens.create'))
                ->post(route('gardens.store'), [
                    'type' => 'vegetable_garden',
                    // Missing required name
                ]);

            $response->assertRedirect(route('gardens.create'))
                ->assertSessionHasErrors('name');
        });
    });
});