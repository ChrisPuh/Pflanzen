<?php

declare(strict_types=1);

use App\DTOs\Area\Actions\DetachPlantFromAreaDTO;

describe('DetachPlantFromAreaDTO', function () {
    it('can be created from validated request', function () {
        $validated = ['plant_id' => 123];

        $dto = DetachPlantFromAreaDTO::fromValidatedRequest($validated);

        expect($dto->plantId)->toBe(123);
    });

    it('can be created from IDs', function () {
        $dto = DetachPlantFromAreaDTO::fromIds(456);

        expect($dto->plantId)->toBe(456);
    });

    it('converts to model data correctly', function () {
        $dto = new DetachPlantFromAreaDTO(plantId: 789);

        $modelData = $dto->toModelData();

        expect($modelData)->toBe([
            'plant_id' => 789,
        ]);
    });

    it('handles string plant_id conversion in fromValidatedRequest', function () {
        $validated = ['plant_id' => '999'];

        $dto = DetachPlantFromAreaDTO::fromValidatedRequest($validated);

        expect($dto->plantId)->toBe(999);
    });
});
