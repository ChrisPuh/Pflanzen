<?php

declare(strict_types=1);

use App\DTOs\Area\AreaUpdateDTO;
use App\Enums\Area\AreaTypeEnum;

describe('AreaUpdateDTO', function () {
    it('creates instance from validated data with all fields', function () {
        $validated = [
            'name' => 'Updated Garden Area',
            'garden_id' => 42,
            'type' => AreaTypeEnum::VegetableBed->value,
            'is_active' => true,
            'description' => 'Updated description',
            'size_sqm' => 35.75,
            'coordinates_x' => 15.25,
            'coordinates_y' => 25.50,
            'color' => '#FF5733',
        ];

        $dto = AreaUpdateDTO::fromValidated($validated);

        expect($dto->name)->toBe('Updated Garden Area')
            ->and($dto->gardenId)->toBe(42)
            ->and($dto->type)->toBe(AreaTypeEnum::VegetableBed)
            ->and($dto->isActive)->toBeTrue()
            ->and($dto->description)->toBe('Updated description')
            ->and($dto->sizeSqm)->toBe(35.75)
            ->and($dto->coordinates)->toBe(['x' => 15.25, 'y' => 25.50])
            ->and($dto->color)->toBe('#FF5733');
    });

    it('creates instance with minimal required fields', function () {
        $validated = [
            'name' => 'Minimal Update',
            'garden_id' => 1,
            'type' => AreaTypeEnum::HerbBed->value,
            'is_active' => false,
        ];

        $dto = AreaUpdateDTO::fromValidated($validated);

        expect($dto->name)->toBe('Minimal Update')
            ->and($dto->gardenId)->toBe(1)
            ->and($dto->type)->toBe(AreaTypeEnum::HerbBed)
            ->and($dto->isActive)->toBeFalse()
            ->and($dto->description)->toBeNull()
            ->and($dto->sizeSqm)->toBeNull()
            ->and($dto->coordinates)->toBeNull()
            ->and($dto->color)->toBeNull();
    });

    it('handles type casting correctly', function () {
        $validated = [
            'name' => 123,        // Will be cast to string
            'garden_id' => '456', // Will be cast to int
            'type' => AreaTypeEnum::FlowerBed->value,
            'is_active' => 1,     // Will be cast to bool
            'size_sqm' => '25.5', // Will be cast to float
        ];

        $dto = AreaUpdateDTO::fromValidated($validated);

        expect($dto->name)->toBe('123')
            ->and($dto->gardenId)->toBe(456)
            ->and($dto->type)->toBe(AreaTypeEnum::FlowerBed)
            ->and($dto->isActive)->toBeTrue()
            ->and($dto->sizeSqm)->toBe(25.5);
    });

    describe('coordinates handling', function () {
        it('creates coordinates array when both coordinates provided', function () {
            $validated = [
                'name' => 'Test',
                'garden_id' => 1,
                'type' => AreaTypeEnum::VegetableBed->value,
                'is_active' => true,
                'coordinates_x' => 10.5,
                'coordinates_y' => 20.0,
            ];

            $dto = AreaUpdateDTO::fromValidated($validated);

            expect($dto->coordinates)->toBe(['x' => 10.5, 'y' => 20.0]);
        });

        it('creates coordinates array when only x coordinate provided', function () {
            $validated = [
                'name' => 'Test',
                'garden_id' => 1,
                'type' => AreaTypeEnum::HerbBed->value,
                'is_active' => true,
                'coordinates_x' => 15.0,
            ];

            $dto = AreaUpdateDTO::fromValidated($validated);

            expect($dto->coordinates)->toBe(['x' => 15.0, 'y' => null]);
        });

        it('creates coordinates array when only y coordinate provided', function () {
            $validated = [
                'name' => 'Test',
                'garden_id' => 1,
                'type' => AreaTypeEnum::FlowerBed->value,
                'is_active' => true,
                'coordinates_y' => 30.5,
            ];

            $dto = AreaUpdateDTO::fromValidated($validated);

            expect($dto->coordinates)->toBe(['x' => null, 'y' => 30.5]);
        });

        it('returns null coordinates when neither coordinate provided', function () {
            $validated = [
                'name' => 'Test',
                'garden_id' => 1,
                'type' => AreaTypeEnum::VegetableBed->value,
                'is_active' => true,
            ];

            $dto = AreaUpdateDTO::fromValidated($validated);

            expect($dto->coordinates)->toBeNull();
        });
    });

    describe('toModelData', function () {
        it('converts to model data with all fields', function () {
            $dto = new AreaUpdateDTO(
                name: 'Test Area',
                gardenId: 5,
                type: AreaTypeEnum::VegetableBed,
                isActive: true,
                description: 'Test description',
                sizeSqm: 50.25,
                coordinates: ['x' => 12.5, 'y' => 25.0],
                color: '#00FF00'
            );

            $modelData = $dto->toModelData();

            expect($modelData)->toBe([
                'name' => 'Test Area',
                'description' => 'Test description',
                'garden_id' => 5,
                'type' => AreaTypeEnum::VegetableBed->value,
                'size_sqm' => 50.25,
                'coordinates' => ['x' => 12.5, 'y' => 25.0],
                'color' => '#00FF00',
                'is_active' => true,
            ]);
        });

        it('converts to model data with null optional fields', function () {
            $dto = new AreaUpdateDTO(
                name: 'Minimal Area',
                gardenId: 2,
                type: AreaTypeEnum::HerbBed,
                isActive: false
            );

            $modelData = $dto->toModelData();

            expect($modelData)->toBe([
                'name' => 'Minimal Area',
                'description' => null,
                'garden_id' => 2,
                'type' => AreaTypeEnum::HerbBed->value,
                'size_sqm' => null,
                'coordinates' => null,
                'color' => null,
                'is_active' => false,
            ]);
        });

        it('converts enum to string value', function () {
            $dto = new AreaUpdateDTO(
                name: 'Test',
                gardenId: 1,
                type: AreaTypeEnum::FlowerBed,
                isActive: true
            );

            $modelData = $dto->toModelData();

            expect($modelData['type'])->toBe(AreaTypeEnum::FlowerBed->value)
                ->and($modelData['type'])->toBeString();
        });

        it('maintains proper types in model data', function () {
            $dto = new AreaUpdateDTO(
                name: 'Type Test',
                gardenId: 42,
                type: AreaTypeEnum::VegetableBed,
                isActive: true,
                sizeSqm: 25.5
            );

            $modelData = $dto->toModelData();

            expect($modelData['name'])->toBeString()
                ->and($modelData['garden_id'])->toBeInt()
                ->and($modelData['type'])->toBeString()
                ->and($modelData['is_active'])->toBeBool()
                ->and($modelData['size_sqm'])->toBeFloat();
        });
    });
});
