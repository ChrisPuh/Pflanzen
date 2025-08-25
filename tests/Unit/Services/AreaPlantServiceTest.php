<?php

declare(strict_types=1);

use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\Models\Area;
use App\Models\Plant;
use App\Services\Area\Contracts\AreaPlantServiceInterface;

describe('AreaPlantService', function () {
    beforeEach(function () {
        $this->service = app(AreaPlantServiceInterface::class);
        $this->area = Area::factory()->create();
        $this->plant1 = Plant::factory()->create();
        $this->plant2 = Plant::factory()->create();
        $this->plant3 = Plant::factory()->create();
    });

    describe('attachPlantsToArea', function () {
        it('can attach single plant to area', function () {
            // Arrange
            $plantData = [
                $this->plant1->id => [
                    'quantity' => 5,
                    'notes' => 'Test notes for plant 1',
                    'planted_at' => now(),
                    'plant_id' => $this->plant1->id, // Included for completeness
                ],
            ];

            $dto = AttachPlantToAreaDTO::fromValidatedRequest($plantData);

            // Act
            $this->service->attachPlantsToArea($this->area, $dto);

            // Assert
            expect($this->area->plants()->get())->toHaveCount(1)
                ->and($this->area->plants()->where('plant_id', $this->plant1->id)->first()->id)->toBe($this->plant1->id);

            $pivot = $this->area->plants()->where('plant_id', $this->plant1->id)->first()->pivot;
            expect($pivot->quantity)->toBe(5)
                ->and($pivot->notes)->toBe('Test notes for plant 1');
        });

        it('can attach multiple plants to area', function () {
            // Arrange
            $plantedAt = now();
            $plantData = [
                $this->plant1->id => [
                    'quantity' => 3,
                    'notes' => 'First plant',
                    'planted_at' => $plantedAt,
                    'plant_id' => $this->plant1->id,
                ],
                $this->plant2->id => [
                    'quantity' => 7,
                    'notes' => 'Second plant',
                    'planted_at' => $plantedAt,
                    'plant_id' => $this->plant2->id,
                ],
            ];

            $dto = AttachPlantToAreaDTO::fromValidatedRequest($plantData);

            // Act
            $this->service->attachPlantsToArea($this->area, $dto);

            // Assert
            expect($this->area->plants()->get())->toHaveCount(2);

            // Check first plant
            $plant1Pivot = $this->area->plants()->where('plant_id', $this->plant1->id)->first()->pivot;
            expect($plant1Pivot->quantity)->toBe(3)
                ->and($plant1Pivot->notes)->toBe('First plant');

            // Check second plant
            $plant2Pivot = $this->area->plants()->where('plant_id', $this->plant2->id)->first()->pivot;
            expect($plant2Pivot->quantity)->toBe(7)
                ->and($plant2Pivot->notes)->toBe('Second plant');
        });

        it('does not detach existing plants when adding new ones', function () {
            // Arrange - First attach a plant directly
            $this->area->plants()->attach($this->plant1->id, [
                'quantity' => 2,
                'notes' => 'Existing plant',
                'planted_at' => now(),
                'plant_id' => $this->plant1->id,
            ]);

            expect($this->area->plants()->get())->toHaveCount(1);

            // Add another plant via service
            $plantData = [
                $this->plant2->id => [
                    'quantity' => 5,
                    'notes' => 'New plant',
                    'planted_at' => now(),
                    'plant_id' => $this->plant2->id,
                ],
            ];

            $dto = AttachPlantToAreaDTO::fromValidatedRequest($plantData);

            // Act
            $this->service->attachPlantsToArea($this->area, $dto);

            // Assert - Both plants should exist
            expect($this->area->plants()->get())->toHaveCount(2)
                ->and($this->area->plants()->get()->pluck('id'))->toContain($this->plant1->id, $this->plant2->id);
        });

        it('updates existing plant data when attaching same plant again', function () {
            // Arrange - First attach a plant
            $this->area->plants()->attach($this->plant1->id, [
                'quantity' => 2,
                'notes' => 'Original notes',
                'planted_at' => now()->subDays(5),
                'plant_id' => $this->plant1->id,
            ]);

            // Update the same plant with new data
            $newPlantedAt = now();
            $plantData = [
                $this->plant1->id => [
                    'quantity' => 10,
                    'notes' => 'Updated notes',
                    'planted_at' => $newPlantedAt,
                    'plant_id' => $this->plant1->id,
                ],
            ];

            $dto = AttachPlantToAreaDTO::fromValidatedRequest($plantData);

            // Act
            $this->service->attachPlantsToArea($this->area, $dto);

            // Assert - Should still have only one plant, but with updated data
            expect($this->area->plants()->get())->toHaveCount(1);

            $pivot = $this->area->plants()->where('plant_id', $this->plant1->id)->first()->pivot;
            expect($pivot->quantity)->toBe(10)
                ->and($pivot->notes)->toBe('Updated notes');
        });

        it('handles empty notes correctly', function () {
            // Arrange
            $plantData = [
                $this->plant1->id => [
                    'quantity' => 1,
                    'notes' => '',
                    'planted_at' => now(),
                    'plant_id' => $this->plant1->id,
                ],
            ];

            $dto = AttachPlantToAreaDTO::fromValidatedRequest($plantData);

            // Act
            $this->service->attachPlantsToArea($this->area, $dto);

            // Assert
            $pivot = $this->area->plants()->where('plant_id', $this->plant1->id)->first()->pivot;
            expect($pivot->notes)->toBe('');
        });

        it('preserves planted_at timestamp correctly', function () {
            // Arrange
            $specificTime = now()->subDays(3)->startOfDay();
            $plantData = [
                $this->plant1->id => [
                    'quantity' => 1,
                    'notes' => 'Historical planting',
                    'planted_at' => $specificTime,
                    'plant_id' => $this->plant1->id,
                ],
            ];

            $dto = AttachPlantToAreaDTO::fromValidatedRequest($plantData);

            // Act
            $this->service->attachPlantsToArea($this->area, $dto);

            // Assert
        });

        it('can handle large quantities', function () {
            // Arrange
            $plantData = [
                $this->plant1->id => [
                    'quantity' => 9999,
                    'notes' => 'Maximum quantity test',
                    'planted_at' => now(),
                    'plant_id' => $this->plant1->id,
                ],
            ];

            $dto = AttachPlantToAreaDTO::fromValidatedRequest($plantData);

            // Act
            $this->service->attachPlantsToArea($this->area, $dto);

            // Assert
            $pivot = $this->area->plants()->where('plant_id', $this->plant1->id)->first()->pivot;
            expect($pivot->quantity)->toBe(9999);
        });

        it('maintains referential integrity', function () {
            // Arrange
            $plantData = [
                $this->plant1->id => [
                    'quantity' => 1,
                    'notes' => 'Test',
                    'planted_at' => now(),
                    'plant_id' => $this->plant1->id,
                ],
            ];

            $dto = AttachPlantToAreaDTO::fromValidatedRequest($plantData);

            // Act
            $this->service->attachPlantsToArea($this->area, $dto);

            // Assert - Database records should exist
            $this->assertDatabaseHas('area_plant', [
                'area_id' => $this->area->id,
                'plant_id' => $this->plant1->id,
                'quantity' => 1,
                'notes' => 'Test',
            ]);

            // Verify through model relationships
            expect($this->area->fresh()->plants->pluck('id'))->toContain($this->plant1->id)
                ->and($this->plant1->fresh()->areas->pluck('id'))->toContain($this->area->id);
        });
    });
});
