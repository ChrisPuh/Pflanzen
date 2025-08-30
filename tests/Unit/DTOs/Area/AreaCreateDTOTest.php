<?php

declare(strict_types=1);

use App\DTOs\Area\AreaStoreDTO;
use App\Enums\Area\AreaTypeEnum;
use OpenSpout\Common\Exception\InvalidArgumentException;

describe('AreaStoreDTO', function () {
    it(/**
     * @throws InvalidArgumentException
     */ 'creates instance from validated request with all data', function () {
        $data = [
            'name' => 'Test Area',
            'garden_id' => 1,
            'type' => AreaTypeEnum::VegetableBed->value,
            'is_active' => true,
            'description' => 'Test description',
            'size_sqm' => 25.5,
            'coordinates_x' => 10.5,
            'coordinates_y' => 20.3,
            'color' => '#FF0000',
        ];

        $dto = AreaStoreDTO::fromValidatedRequest($data);

        expect($dto->name)->toBe('Test Area')
            ->and($dto->gardenId)->toBe(1)
            ->and($dto->type)->toBe(AreaTypeEnum::VegetableBed)
            ->and($dto->isActive)->toBeTrue()
            ->and($dto->description)->toBe('Test description')
            ->and($dto->sizeSqm)->toBe(25.5)
            ->and($dto->coordinates)->toBe(['x' => 10.5, 'y' => 20.3])
            ->and($dto->color)->toBe('#FF0000');
    });

    it('creates instance with minimal required data', function () {
        $data = [
            'name' => 'Minimal Area',
            'garden_id' => 2,
            'type' => AreaTypeEnum::FlowerBed->value,
            'is_active' => false,
        ];

        $dto = AreaStoreDTO::fromValidatedRequest($data);

        expect($dto->name)->toBe('Minimal Area')
            ->and($dto->gardenId)->toBe(2)
            ->and($dto->type)->toBe(AreaTypeEnum::FlowerBed)
            ->and($dto->isActive)->toBeFalse()
            ->and($dto->description)->toBeNull()
            ->and($dto->sizeSqm)->toBeNull()
            ->and($dto->coordinates)->toBeNull()
            ->and($dto->color)->toBeNull();
    });

    it('handles coordinates when only one coordinate is provided', function () {
        $data = [
            'name' => 'Test',
            'garden_id' => 1,
            'type' => AreaTypeEnum::HerbBed->value,
            'is_active' => true,
            'coordinates_x' => 15.0,
        ];

        $dto = AreaStoreDTO::fromValidatedRequest($data);

        expect($dto->coordinates)->toBe(['x' => 15.0, 'y' => null]);
    });

    it('converts to model data correctly', function () {
        $dto = new AreaStoreDTO(
            name: 'Test Area',
            gardenId: 1,
            type: AreaTypeEnum::VegetableBed,
            isActive: true,
            description: 'Test desc',
            sizeSqm: 30.0,
            coordinates: ['x' => 5.0, 'y' => 10.0],
            color: '#00FF00'
        );

        $modelData = $dto->toModelData();

        expect($modelData)->toBe([
            'name' => 'Test Area',
            'description' => 'Test desc',
            'garden_id' => 1,
            'type' => AreaTypeEnum::VegetableBed,
            'size_sqm' => 30.0,
            'coordinates' => ['x' => 5.0, 'y' => 10.0],
            'color' => '#00FF00',
            'is_active' => true,
        ]);
    });
});
