<?php

declare(strict_types=1);

use App\Models\Area;
use App\Models\Garden;
use App\Models\Plant;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->garden = Garden::factory()->create(['user_id' => $this->user->id]);
    $this->area = Area::factory()->create(['garden_id' => $this->garden->id]);
});

describe('Adding plants to area', function () {
    it('can add a single plant to an area', function (): void {
        $plant = Plant::factory()->create();

        $response = $this->actingAs($this->user)->post(route('areas.plants.store', $this->area), [
            'plants' => [
                [
                    'plant_id' => $plant->id,
                    'quantity' => 3,
                    'notes' => 'Test notes',
                ],
            ],
        ]);

        $response->assertRedirect(route('areas.show', $this->area))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('area_plant', [
            'area_id' => $this->area->id,
            'plant_id' => $plant->id,
            'quantity' => 3,
            'notes' => 'Test notes',
        ]);
    });

    it('can add multiple plants to an area', function (): void {
        $plants = Plant::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->post(route('areas.plants.store', $this->area), [
            'plants' => [
                [
                    'plant_id' => $plants[0]->id,
                    'quantity' => 2,
                    'notes' => 'First plant',
                ],
                [
                    'plant_id' => $plants[1]->id,
                    'quantity' => 1,
                    'notes' => 'Second plant',
                ],
                [
                    'plant_id' => $plants[2]->id,
                    'quantity' => 5,
                    'notes' => null,
                ],
            ],
        ]);

        $response->assertRedirect(route('areas.show', $this->area))
            ->assertSessionHas('success');

        foreach ($plants as $index => $plant) {
            $expectedQuantity = [2, 1, 5][$index];
            $expectedNotes = ['First plant', 'Second plant', null][$index];

            $this->assertDatabaseHas('area_plant', [
                'area_id' => $this->area->id,
                'plant_id' => $plant->id,
                'quantity' => $expectedQuantity,
                'notes' => $expectedNotes,
            ]);
        }
    });

    it('increases quantity when adding existing plant', function (): void {
        $plant = Plant::factory()->create();

        // First, add the plant with quantity 2
        $this->area->plants()->attach($plant->id, [
            'quantity' => 2,
            'notes' => 'Original notes',
            'planted_at' => now(),
        ]);

        // Then add the same plant again with quantity 3
        $response = $this->actingAs($this->user)->post(route('areas.plants.store', $this->area), [
            'plants' => [
                [
                    'plant_id' => $plant->id,
                    'quantity' => 3,
                    'notes' => 'Updated notes',
                ],
            ],
        ]);

        $response->assertRedirect(route('areas.show', $this->area))
            ->assertSessionHas('success');

        // Should have total quantity of 5 (2 + 3)
        $this->assertDatabaseHas('area_plant', [
            'area_id' => $this->area->id,
            'plant_id' => $plant->id,
            'quantity' => 5,
            'notes' => 'Updated notes',
        ]);
    });

    it('validates required fields', function (): void {
        $response = $this->actingAs($this->user)->post(route('areas.plants.store', $this->area), [
            'plants' => [
                [
                    'plant_id' => '',
                    'quantity' => '',
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['plants.0.plant_id', 'plants.0.quantity']);
    });

    it('validates plant exists', function (): void {
        $response = $this->actingAs($this->user)->post(route('areas.plants.store', $this->area), [
            'plants' => [
                [
                    'plant_id' => 99999,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['plants.0.plant_id']);
    });

    it('validates quantity limits', function (): void {
        $plant = Plant::factory()->create();

        $response = $this->actingAs($this->user)->post(route('areas.plants.store', $this->area), [
            'plants' => [
                [
                    'plant_id' => $plant->id,
                    'quantity' => 0,
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['plants.0.quantity']);

        $response = $this->actingAs($this->user)->post(route('areas.plants.store', $this->area), [
            'plants' => [
                [
                    'plant_id' => $plant->id,
                    'quantity' => 10000,
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['plants.0.quantity']);
    });

    it('prevents unauthorized users from adding plants', function (): void {
        $otherUser = User::factory()->create();
        $plant = Plant::factory()->create();

        $response = $this->actingAs($otherUser)->post(route('areas.plants.store', $this->area), [
            'plants' => [
                [
                    'plant_id' => $plant->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertForbidden();
    });

    it('requires authentication', function (): void {
        $plant = Plant::factory()->create();

        $response = $this->post(route('areas.plants.store', $this->area), [
            'plants' => [
                [
                    'plant_id' => $plant->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertRedirect(route('login'));
    });
});

describe('Removing plants from area', function () {
    it('can remove a plant from an area', function (): void {
        $plant = Plant::factory()->create();
        $this->area->plants()->attach($plant->id, [
            'quantity' => 2,
            'notes' => 'Test notes',
            'planted_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->delete(route('areas.plants.destroy', [$this->area, $plant]));

        $response->assertRedirect(route('areas.show', $this->area))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('area_plant', [
            'area_id' => $this->area->id,
            'plant_id' => $plant->id,
        ]);
    });

    it('prevents unauthorized users from removing plants', function (): void {
        $otherUser = User::factory()->create();
        $plant = Plant::factory()->create();
        $this->area->plants()->attach($plant->id, [
            'quantity' => 1,
            'planted_at' => now(),
        ]);

        $response = $this->actingAs($otherUser)->delete(route('areas.plants.destroy', [$this->area, $plant]));

        $response->assertForbidden();

        $this->assertDatabaseHas('area_plant', [
            'area_id' => $this->area->id,
            'plant_id' => $plant->id,
        ]);
    });

    it('requires authentication', function (): void {
        $plant = Plant::factory()->create();
        $this->area->plants()->attach($plant->id, [
            'quantity' => 1,
            'planted_at' => now(),
        ]);

        $response = $this->delete(route('areas.plants.destroy', [$this->area, $plant]));

        $response->assertRedirect(route('login'));
    });
});

describe('Area show page with plants', function () {
    it('displays available plants for selection', function (): void {
        $plants = Plant::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('areas.show', $this->area));

        $response->assertSuccessful()
            ->assertViewHas('availablePlants');

        $viewPlants = $response->viewData('availablePlants');
        expect($viewPlants)->toHaveCount(3);
    });

    it('shows plants already in the area with quantity', function (): void {
        $plant = Plant::factory()->create(['name' => 'Test Plant']);
        $this->area->plants()->attach($plant->id, [
            'quantity' => 5,
            'notes' => 'Test notes',
            'planted_at' => now()->subDays(7),
        ]);

        $response = $this->actingAs($this->user)->get(route('areas.show', $this->area));

        $response->assertSee('Test Plant')
            ->assertSee('5x')
            ->assertSee('Test notes');
    });

    it('shows add plant form for authorized users', function (): void {
        $response = $this->actingAs($this->user)->get(route('areas.show', $this->area));

        $response->assertSee('Pflanzen hinzufügen')
            ->assertSeeLivewire('area.plant-selection-modal');
    });

    it('hides add plant form for unauthorized users', function (): void {
        $otherUser = User::factory()->create();
        $otherGarden = Garden::factory()->create(['user_id' => $otherUser->id]);
        $otherArea = Area::factory()->create(['garden_id' => $otherGarden->id]);

        $response = $this->actingAs($this->user)->get(route('areas.show', $otherArea));

        $response->assertForbidden();
    });
});
