<?php

declare(strict_types=1);

use App\Enums\Garden\GardenTypeEnum;
use App\Models\Garden;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('GET /gardens/{garden}/edit', function () {
    it('requires authentication', function () {
        $garden = Garden::factory()->for($this->user)->create();

        $this->get(route('gardens.edit', $garden))
            ->assertRedirect(route('login'));
    });

    it('shows the edit form for garden owner', function () {
        $garden = Garden::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->get(route('gardens.edit', $garden))
            ->assertOk()
            ->assertViewIs('gardens.edit')
            ->assertViewHas('garden', $garden)
            ->assertViewHas('gardenTypes')
            ->assertSee($garden->name)
            ->assertSee($garden->description);
    });

    it('denies access for non-owners', function () {
        $garden = Garden::factory()->for($this->otherUser)->create();

        $this->actingAs($this->user)
            ->get(route('gardens.edit', $garden))
            ->assertForbidden();
    });

    it('displays all garden types in the form', function () {
        $garden = Garden::factory()->for($this->user)->create();

        $response = $this->actingAs($this->user)
            ->get(route('gardens.edit', $garden));

        foreach (GardenTypeEnum::cases() as $type) {
            $response->assertSee($type->getLabel());
        }
    });
});

describe('PUT /gardens/{garden}', function () {
    it('requires authentication', function () {
        $garden = Garden::factory()->for($this->user)->create();

        $this->put(route('gardens.update', $garden), [])
            ->assertRedirect(route('login'));
    });

    it('updates garden successfully with valid data', function () {
        $garden = Garden::factory()->for($this->user)->create([
            'name' => 'Old Garden Name',
            'type' => GardenTypeEnum::VegetableGarden,
        ]);

        $updateData = [
            'name' => 'Updated Garden Name',
            'description' => 'Updated description',
            'type' => GardenTypeEnum::FlowerGarden->value,
            'size_sqm' => 25.5,
            'location' => 'Updated location',
            'city' => 'Updated city',
            'postal_code' => '12345',
            'coordinates' => [
                'latitude' => 52.5200,
                'longitude' => 13.4050,
            ],
            'is_active' => true,
            'established_at' => '2023-01-15',
        ];

        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), $updateData)
            ->assertRedirect(route('gardens.show', $garden))
            ->assertSessionHas('status', 'Garten wurde erfolgreich aktualisiert!');

        $garden->refresh();

        expect($garden->name)->toBe('Updated Garden Name');
        expect($garden->description)->toBe('Updated description');
        expect($garden->type)->toBe(GardenTypeEnum::FlowerGarden);
        expect($garden->size_sqm)->toBe(25.5);
        expect($garden->location)->toBe('Updated location');
        expect($garden->city)->toBe('Updated city');
        expect($garden->postal_code)->toBe('12345');
        expect($garden->coordinates)->toBe([
            'latitude' => 52.5200,
            'longitude' => 13.4050,
        ]);
        expect($garden->is_active)->toBeTrue();
        expect($garden->established_at->format('Y-m-d'))->toBe('2023-01-15');
    });

    it('denies access for non-owners', function () {
        $garden = Garden::factory()->for($this->otherUser)->create();

        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [
                'name' => 'Updated Garden',
                'type' => GardenTypeEnum::VegetableGarden->value,
            ])
            ->assertForbidden();
    });

    it('validates required fields', function () {
        $garden = Garden::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [])
            ->assertSessionHasErrors(['name', 'type']);
    });

    it('validates name length', function () {
        $garden = Garden::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [
                'name' => 'a', // Too short
                'type' => GardenTypeEnum::VegetableGarden->value,
            ])
            ->assertSessionHasErrors(['name']);

        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [
                'name' => str_repeat('a', 256), // Too long
                'type' => GardenTypeEnum::VegetableGarden->value,
            ])
            ->assertSessionHasErrors(['name']);
    });

    it('validates garden type enum', function () {
        $garden = Garden::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [
                'name' => 'Valid Name',
                'type' => 'invalid_type',
            ])
            ->assertSessionHasErrors(['type']);
    });

    it('validates numeric fields', function () {
        $garden = Garden::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [
                'name' => 'Valid Name',
                'type' => GardenTypeEnum::VegetableGarden->value,
                'size_sqm' => 'not_a_number',
            ])
            ->assertSessionHasErrors(['size_sqm']);

        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [
                'name' => 'Valid Name',
                'type' => GardenTypeEnum::VegetableGarden->value,
                'size_sqm' => -1,
            ])
            ->assertSessionHasErrors(['size_sqm']);
    });

    it('validates coordinates when provided', function () {
        $garden = Garden::factory()->for($this->user)->create();

        // Test incomplete coordinates
        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [
                'name' => 'Valid Name',
                'type' => GardenTypeEnum::VegetableGarden->value,
                'coordinates' => [
                    'latitude' => 52.5200,
                    // longitude missing
                ],
            ])
            ->assertSessionHasErrors(['coordinates.longitude']);

        // Test invalid latitude range
        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [
                'name' => 'Valid Name',
                'type' => GardenTypeEnum::VegetableGarden->value,
                'coordinates' => [
                    'latitude' => 91, // Out of range
                    'longitude' => 13.4050,
                ],
            ])
            ->assertSessionHasErrors(['coordinates.latitude']);

        // Test invalid longitude range
        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [
                'name' => 'Valid Name',
                'type' => GardenTypeEnum::VegetableGarden->value,
                'coordinates' => [
                    'latitude' => 52.5200,
                    'longitude' => 181, // Out of range
                ],
            ])
            ->assertSessionHasErrors(['coordinates.longitude']);
    });

    it('handles empty coordinates correctly', function () {
        $garden = Garden::factory()->for($this->user)->create([
            'coordinates' => ['latitude' => 52.5200, 'longitude' => 13.4050],
        ]);

        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [
                'name' => 'Updated Garden',
                'type' => GardenTypeEnum::VegetableGarden->value,
                'coordinates' => [
                    'latitude' => '',
                    'longitude' => '',
                ],
            ])
            ->assertRedirect(route('gardens.show', $garden));

        $garden->refresh();
        expect($garden->coordinates)->toBeNull();
    });

    it('validates established_at date', function () {
        $garden = Garden::factory()->for($this->user)->create();

        // Test future date
        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [
                'name' => 'Valid Name',
                'type' => GardenTypeEnum::VegetableGarden->value,
                'established_at' => now()->addDay()->format('Y-m-d'),
            ])
            ->assertSessionHasErrors(['established_at']);
    });

    it('updates garden with minimal data', function () {
        $garden = Garden::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->put(route('gardens.update', $garden), [
                'name' => 'Minimal Garden',
                'type' => GardenTypeEnum::VegetableGarden->value,
            ])
            ->assertRedirect(route('gardens.show', $garden));

        $garden->refresh();
        expect($garden->name)->toBe('Minimal Garden');
        expect($garden->type)->toBe(GardenTypeEnum::VegetableGarden);
    });
});
