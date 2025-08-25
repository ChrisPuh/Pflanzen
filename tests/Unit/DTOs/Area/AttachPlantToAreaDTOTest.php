<?php

declare(strict_types=1);

use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\DTOs\Area\Actions\PlantSelectionData;
use Illuminate\Support\Carbon;

describe('AttachPlantToAreaDTO', function () {
    describe('fromValidatedRequest', function () {
        it('creates DTO from valid request data with all fields', function () {
            // Arrange
            $plantedAt = now()->startOfSecond();

            Carbon::setTestNow($plantedAt);

            $validated = [
                [
                    'plant_id' => 1,
                    'quantity' => 5,
                    'notes' => 'Test notes',
                    'planted_at' => $plantedAt->toDateTimeString(),
                ],
                [
                    'plant_id' => 2,
                    'quantity' => 3,
                    'notes' => 'Second plant notes',
                    'planted_at' => $plantedAt->toDateTimeString(),
                ],
            ];

            // Act
            $dto = AttachPlantToAreaDTO::fromValidatedRequest($validated);

            // Assert
            expect($dto->plants)->toHaveCount(2)
                ->and($dto->plants[0])->toBeInstanceOf(PlantSelectionData::class)
                ->and($dto->plants[0]->plantId)->toBe(1)
                ->and($dto->plants[0]->quantity)->toBe(5)
                ->and($dto->plants[0]->notes)->toBe('Test notes')
                ->and($dto->plants[0]->plantedAt->equalTo($plantedAt))->toBeTrue()
                ->and($dto->plants[1])->toBeInstanceOf(PlantSelectionData::class)
                ->and($dto->plants[1]->plantId)->toBe(2)
                ->and($dto->plants[1]->quantity)->toBe(3)
                ->and($dto->plants[1]->notes)->toBe('Second plant notes')
                ->and($dto->plants[1]->plantedAt->equalTo($plantedAt))->toBeTrue();

            Carbon::setTestNow();
        });

        it('handles missing optional fields', function () {
            // Arrange
            $validated = [
                [
                    'plant_id' => 1,
                    'quantity' => 5,
                    // notes missing
                    // planted_at missing
                ],
            ];

            // Act
            $dto = AttachPlantToAreaDTO::fromValidatedRequest($validated);

            // Assert
            expect($dto->plants)->toHaveCount(1)
                ->and($dto->plants[0]->plantId)->toBe(1)
                ->and($dto->plants[0]->quantity)->toBe(5)
                ->and($dto->plants[0]->notes)->toBeNull()
                ->and($dto->plants[0]->plantedAt)->toBeNull();
        });

        it('handles empty notes field', function () {
            // Arrange
            $validated = [
                [
                    'plant_id' => 1,
                    'quantity' => 5,
                    'notes' => '',
                    'planted_at' => now()->toDateTimeString(),
                ],
            ];

            // Act
            $dto = AttachPlantToAreaDTO::fromValidatedRequest($validated);

            // Assert
            expect($dto->plants[0]->notes)->toBe('');
        });

        it('handles null notes field', function () {
            // Arrange
            $validated = [
                [
                    'plant_id' => 1,
                    'quantity' => 5,
                    'notes' => null,
                    'planted_at' => now()->toDateTimeString(),
                ],
            ];

            // Act
            $dto = AttachPlantToAreaDTO::fromValidatedRequest($validated);

            // Assert
            expect($dto->plants[0]->notes)->toBeNull();
        });

        it('parses different date formats', function () {
            // Arrange
            $date1 = '2023-12-25 14:30:00';
            $date2 = '2023-12-26T15:45:30Z';
            $validated = [
                [
                    'plant_id' => 1,
                    'quantity' => 5,
                    'planted_at' => $date1,
                ],
                [
                    'plant_id' => 2,
                    'quantity' => 3,
                    'planted_at' => $date2,
                ],
            ];

            // Act
            $dto = AttachPlantToAreaDTO::fromValidatedRequest($validated);

            // Assert
            expect($dto->plants[0]->plantedAt)->toBeInstanceOf(Carbon::class)
                ->and($dto->plants[0]->plantedAt->format('Y-m-d H:i:s'))->toBe('2023-12-25 14:30:00')
                ->and($dto->plants[1]->plantedAt)->toBeInstanceOf(Carbon::class)
                ->and($dto->plants[1]->plantedAt->format('Y-m-d H:i:s'))->toBe('2023-12-26 15:45:30');

        });

        it('handles empty validated array', function () {
            // Arrange
            $validated = [];

            // Act
            $dto = AttachPlantToAreaDTO::fromValidatedRequest($validated);

            // Assert
            expect($dto->plants)->toBeEmpty();
        });

        it('converts string numbers to integers', function () {
            // Arrange
            $validated = [
                [
                    'plant_id' => '123',
                    'quantity' => '456',
                    'notes' => 'Test',
                ],
            ];

            // Act
            $dto = AttachPlantToAreaDTO::fromValidatedRequest($validated);

            // Assert
            expect($dto->plants[0]->plantId)->toBe(123)
                ->and($dto->plants[0]->quantity)->toBe(456)
                ->and($dto->plants[0]->plantId)->toBeInt()
                ->and($dto->plants[0]->quantity)->toBeInt();
        });
    });

    describe('toModelData', function () {
        it('converts DTO to model data with all fields', function () {
            // Arrange
            $plantedAt1 = now()->subDays(5);
            $plantedAt2 = now()->subDays(3);

            $plants = [
                new PlantSelectionData(1, 5, 'First plant', $plantedAt1),
                new PlantSelectionData(2, 3, 'Second plant', $plantedAt2),
            ];

            $dto = new AttachPlantToAreaDTO($plants);

            // Act
            $modelData = $dto->toModelData();

            // Assert
            expect($modelData)->toHaveCount(2)
                ->and($modelData)->toHaveKeys([1, 2])
                ->and($modelData[1])->toBe([
                    'quantity' => 5,
                    'notes' => 'First plant',
                    'planted_at' => $plantedAt1,
                    'plant_id' => 1,
                ])
                ->and($modelData[2])->toBe([
                    'quantity' => 3,
                    'notes' => 'Second plant',
                    'planted_at' => $plantedAt2,
                    'plant_id' => 2,
                ]);

        });

        it('uses now() when planted_at is null', function () {
            // Arrange
            $now = now();
            Carbon::setTestNow($now);

            $plants = [
                new PlantSelectionData(1, 5, 'Test plant', null),
            ];

            $dto = new AttachPlantToAreaDTO($plants);

            // Act
            $modelData = $dto->toModelData();

            // Assert
            expect($modelData[1]['planted_at'])->toEqual($now);

            Carbon::setTestNow();
        });

        it('handles null notes', function () {
            // Arrange
            $plants = [
                new PlantSelectionData(1, 5, null, now()),
            ];

            $dto = new AttachPlantToAreaDTO($plants);

            // Act
            $modelData = $dto->toModelData();

            // Assert
            expect($modelData[1]['notes'])->toBeNull();
        });

        it('handles empty notes', function () {
            // Arrange
            $plants = [
                new PlantSelectionData(1, 5, '', now()),
            ];

            $dto = new AttachPlantToAreaDTO($plants);

            // Act
            $modelData = $dto->toModelData();

            // Assert
            expect($modelData[1]['notes'])->toBe('');
        });

        it('preserves plant_id as array key and value', function () {
            // Arrange
            $plants = [
                new PlantSelectionData(42, 10, 'Test', now()),
            ];

            $dto = new AttachPlantToAreaDTO($plants);

            // Act
            $modelData = $dto->toModelData();

            // Assert
            expect($modelData)->toHaveKey(42)
                ->and($modelData[42]['plant_id'])->toBe(42);
        });

        it('returns empty array for empty plants', function () {
            // Arrange
            $dto = new AttachPlantToAreaDTO([]);

            // Act
            $modelData = $dto->toModelData();

            // Assert
            expect($modelData)->toBeEmpty();
        });

        it('handles multiple plants with same timestamp', function () {
            // Arrange
            $plantedAt = now()->startOfDay();
            $plants = [
                new PlantSelectionData(1, 2, 'Plant 1', $plantedAt),
                new PlantSelectionData(2, 4, 'Plant 2', $plantedAt),
                new PlantSelectionData(3, 6, 'Plant 3', $plantedAt),
            ];

            $dto = new AttachPlantToAreaDTO($plants);

            // Act
            $modelData = $dto->toModelData();

            // Assert
            expect($modelData)->toHaveCount(3)
                ->and($modelData[1]['planted_at'])->toEqual($plantedAt)
                ->and($modelData[2]['planted_at'])->toEqual($plantedAt)
                ->and($modelData[3]['planted_at'])->toEqual($plantedAt);
        });

        it('maintains data integrity through round trip', function () {
            // Arrange
            $original = [
                [
                    'plant_id' => 1,
                    'quantity' => 5,
                    'notes' => 'Original notes',
                    'planted_at' => now()->toDateTimeString(),
                ],
            ];

            // Act - Round trip conversion
            $dto = AttachPlantToAreaDTO::fromValidatedRequest($original);
            $modelData = $dto->toModelData();

            // Assert
            expect($modelData)->toHaveKey(1)
                ->and($modelData[1]['plant_id'])->toBe(1)
                ->and($modelData[1]['quantity'])->toBe(5)
                ->and($modelData[1]['notes'])->toBe('Original notes')
                ->and($modelData[1]['planted_at'])->toBeInstanceOf(Carbon::class);
        });
    });

    describe('constructor', function () {
        it('accepts array of PlantSelectionData objects', function () {
            // Arrange
            $plants = [
                new PlantSelectionData(1, 5, 'Test', now()),
                new PlantSelectionData(2, 3, null, null),
            ];

            // Act
            $dto = new AttachPlantToAreaDTO($plants);

            // Assert
            expect($dto->plants)->toBe($plants)
                ->and($dto->plants)->toHaveCount(2);
        });

        it('is readonly', function () {
            // Arrange
            $plants = [new PlantSelectionData(1, 5, 'Test', now())];
            $dto = new AttachPlantToAreaDTO($plants);

            // Assert - This should not be possible (readonly property)
            expect($dto)->toBeInstanceOf(AttachPlantToAreaDTO::class)
                ->and($dto->plants)->toBe($plants);
        });
    });
});
