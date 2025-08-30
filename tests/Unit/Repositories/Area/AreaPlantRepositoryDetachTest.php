<?php

declare(strict_types=1);

use App\DTOs\Area\Actions\DetachPlantFromAreaDTO;
use App\Models\Area;
use App\Models\Plant;
use App\Repositories\Area\AreaPlantRepository;

describe('AreaPlantRepository - Detach Functionality', function () {
    beforeEach(function () {
        $this->repository = new AreaPlantRepository();
    });

    it('can detach plant from area successfully', function () {
        $area = Area::factory()->create();
        $plant = Plant::factory()->create();

        // First attach the plant
        $area->plants()->attach($plant->id, [
            'quantity' => 5,
            'notes' => 'Test notes',
            'planted_at' => now(),
        ]);

        // Verify plant is attached
        expect($area->plants()->where('plant_id', $plant->id)->exists())->toBeTrue();

        // Create DTO and detach
        $dto = new DetachPlantFromAreaDTO(plantId: $plant->id);
        $result = $this->repository->detachPlantFromArea($area, $dto);

        expect($result)->toBeTrue();
        expect($area->plants()->where('plant_id', $plant->id)->exists())->toBeFalse();
    });

    it('returns false when trying to detach non-existent plant', function () {
        $area = Area::factory()->create();
        $plant = Plant::factory()->create();

        // Don't attach the plant, just try to detach
        $dto = new DetachPlantFromAreaDTO(plantId: $plant->id);
        $result = $this->repository->detachPlantFromArea($area, $dto);

        expect($result)->toBeFalse();
    });

    it('only detaches specified plant and leaves others', function () {
        $area = Area::factory()->create();
        $plant1 = Plant::factory()->create();
        $plant2 = Plant::factory()->create();

        // Attach both plants
        $area->plants()->attach($plant1->id, [
            'quantity' => 3,
            'planted_at' => now(),
        ]);
        $area->plants()->attach($plant2->id, [
            'quantity' => 2,
            'planted_at' => now(),
        ]);

        // Detach only plant1
        $dto = new DetachPlantFromAreaDTO(plantId: $plant1->id);
        $result = $this->repository->detachPlantFromArea($area, $dto);

        expect($result)->toBeTrue();
        expect($area->plants()->where('plant_id', $plant1->id)->exists())->toBeFalse();
        expect($area->plants()->where('plant_id', $plant2->id)->exists())->toBeTrue();
    });
});
