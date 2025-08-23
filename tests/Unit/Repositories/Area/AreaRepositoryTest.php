<?php

declare(strict_types=1);

use App\DTOs\Area\AreaCreateDTO;
use App\Enums\Area\AreaTypeEnum;
use App\Models\Area;
use App\Models\Garden;
use App\Repositories\AreaRepository;

describe('AreaRepository', function () {

    beforeEach(function () {
        $this->repository = new AreaRepository();
        $this->garden = Garden::factory()->create();
    });

    describe('create', function () {
        it('creates area with all data', function () {
            $dto = new AreaCreateDTO(
                name: 'Test Vegetable Patch',
                gardenId: $this->garden->id,
                type: AreaTypeEnum::VegetableBed,
                isActive: true,
                description: 'Growing tomatoes and peppers',
                sizeSqm: 25.5,
                coordinates: ['x' => 10.5, 'y' => 20.3],
                color: '#00FF00'
            );

            $area = $this->repository->create($dto);

            expect($area)
                ->toBeInstanceOf(Area::class)
                ->id->not->toBeNull()
                ->name->toBe('Test Vegetable Patch')
                ->garden_id->toBe($this->garden->id)
                ->type->toBe(AreaTypeEnum::VegetableBed)
                ->is_active->toBeTrue()
                ->description->toBe('Growing tomatoes and peppers')
                ->size_sqm->toBe(25.5)
                ->coordinates->toBe(['x' => 10.5, 'y' => 20.3])
                ->color->toBe('#00FF00');

            $this->assertDatabaseHas('areas', [
                'name' => 'Test Vegetable Patch',
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::VegetableBed->value,
                'is_active' => true,
                'description' => 'Growing tomatoes and peppers',
                'size_sqm' => 25.5,
                'color' => '#00FF00',
            ]);
        });

        it('creates area with minimal required data', function () {
            $dto = new AreaCreateDTO(
                name: 'Minimal Area',
                gardenId: $this->garden->id,
                type: AreaTypeEnum::HerbBed,
                isActive: false
            );

            $area = $this->repository->create($dto);

            expect($area)
                ->toBeInstanceOf(Area::class)
                ->name->toBe('Minimal Area')
                ->garden_id->toBe($this->garden->id)
                ->type->toBe(AreaTypeEnum::HerbBed)
                ->is_active->toBeFalse()
                ->description->toBeNull()
                ->size_sqm->toBeNull()
                ->coordinates->toBeNull()
                ->color->toBeNull();
        });

        it('handles json coordinates correctly', function () {
            $coordinates = ['x' => 15.75, 'y' => 30.25];
            $dto = new AreaCreateDTO(
                name: 'Coordinates Test',
                gardenId: $this->garden->id,
                type: AreaTypeEnum::FlowerBed,
                coordinates: $coordinates
            );

            $area = $this->repository->create($dto);

            expect($area->coordinates)->toBe($coordinates);

            // Verify it's stored as JSON in database
            $this->assertDatabaseHas('areas', [
                'name' => 'Coordinates Test',
                'coordinates' => json_encode($coordinates),
            ]);
        });

        it('sets timestamps automatically', function () {
            $dto = new AreaCreateDTO(
                name: 'Timestamp Test',
                gardenId: $this->garden->id,
                type: AreaTypeEnum::VegetableBed
            );

            $area = $this->repository->create($dto);

            expect($area->created_at)->not->toBeNull()
                ->and($area->updated_at)->not->toBeNull()
                ->and($area->created_at)->toEqual($area->updated_at);
        });
    });

});
