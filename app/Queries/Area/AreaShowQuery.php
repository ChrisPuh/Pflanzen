<?php

declare(strict_types=1);

namespace App\Queries\Area;

use App\Models\Area;
use App\Repositories\Area\Contracts\AreaRepositoryInterface;
use App\Services\PlantService;
use Illuminate\Database\Eloquent\Collection;

final readonly class AreaShowQuery
{
    public function __construct(
        private AreaRepositoryInterface $repository,
        private PlantService $plantService
    ) {
    }

    public function execute(int $areaId): array
    {
        // Get area with required relationships via repository
        $area = $this->repository->queryForShow($areaId)->firstOrFail();

        return [
            'area' => $area,
            'availablePlants' => $this->plantService->getAvailablePlantsForArea(),
        ];
    }
}
