<?php

declare(strict_types=1);

use App\DTOs\Area\Actions\DetachPlantFromAreaDTO;
use App\Models\Area;
use App\Services\Area\Contracts\AreaPlantServiceInterface;

describe('AreaPlantService - Detach Functionality', function () {
    it('can detach plant from area through service interface', function () {
        $area = Area::factory()->create();
        $plant = App\Models\Plant::factory()->create();

        // First attach the plant
        $area->plants()->attach($plant->id, [
            'quantity' => 2,
            'notes' => 'Test notes',
            'planted_at' => now(),
        ]);

        // Verify plant is attached
        expect($area->plants()->where('plant_id', $plant->id)->exists())->toBeTrue();

        $dto = DetachPlantFromAreaDTO::fromIds($plant->id);
        $service = app(AreaPlantServiceInterface::class);

        expect(fn () => $service->detachPlantFromArea($area, $dto))
            ->not->toThrow(Exception::class);

        // Verify plant is detached
        $area->refresh();
        expect($area->plants()->where('plant_id', $plant->id)->exists())->toBeFalse();
    });

    it('handles non-existent plant detachment gracefully', function () {
        $area = Area::factory()->create();
        $plant = App\Models\Plant::factory()->create();

        // Don't attach the plant, just try to detach
        $dto = DetachPlantFromAreaDTO::fromIds($plant->id);
        $service = app(AreaPlantServiceInterface::class);

        expect(fn () => $service->detachPlantFromArea($area, $dto))
            ->not->toThrow(Exception::class);
    });
});
