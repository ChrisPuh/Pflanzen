<?php

declare(strict_types=1);

use App\Actions\Area\Actions\DetachPlantFromAreaAction;
use App\DTOs\Area\Actions\DetachPlantFromAreaDTO;
use App\Models\Area;
use App\Repositories\Area\Contracts\AreaPlantRepositoryInterface;

use function Pest\Laravel\mock;

describe('DetachPlantFromAreaAction', function () {
    it('can execute detach plant operation successfully', function () {
        $area = new Area(['id' => 1]);
        $dto = new DetachPlantFromAreaDTO(plantId: 123);

        $repository = mock(AreaPlantRepositoryInterface::class);
        $repository->shouldReceive('detachPlantFromArea')
            ->once()
            ->with($area, $dto)
            ->andReturn(true);

        $action = new DetachPlantFromAreaAction($repository);
        $result = $action->execute($area, $dto);

        expect($result)->toBeTrue();
    });

    it('can handle failed detach operation', function () {
        $area = new Area(['id' => 1]);
        $dto = new DetachPlantFromAreaDTO(plantId: 123);

        $repository = mock(AreaPlantRepositoryInterface::class);
        $repository->shouldReceive('detachPlantFromArea')
            ->once()
            ->with($area, $dto)
            ->andReturn(false);

        $action = new DetachPlantFromAreaAction($repository);
        $result = $action->execute($area, $dto);

        expect($result)->toBeFalse();
    });

    it('throws exception when repository throws exception', function () {
        $area = new Area(['id' => 1]);
        $dto = new DetachPlantFromAreaDTO(plantId: 123);
        $exception = new Exception('Database error');

        $repository = mock(AreaPlantRepositoryInterface::class);
        $repository->shouldReceive('detachPlantFromArea')
            ->once()
            ->with($area, $dto)
            ->andThrow($exception);

        $action = new DetachPlantFromAreaAction($repository);

        expect(fn () => $action->execute($area, $dto))
            ->toThrow(Exception::class, 'Database error');
    });
});
