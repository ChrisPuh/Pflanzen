<?php

declare(strict_types=1);

namespace App\Queries\Area;

use App\Enums\Area\AreaTypeEnum;
use App\Repositories\Area\Contracts\AreaRepositoryInterface;
use App\Services\GardenService;

final readonly class AreaEditQuery
{
    public function __construct(
        private AreaRepositoryInterface $repository,
        private GardenService $gardenService
    ) {
    }

    public function execute(int $userId, int $areaId, bool $isAdmin): array
    {
        // Get area via repository
        $area = $this->repository->queryForShow($areaId)->firstOrFail();

        // Get user gardens via GardenService - need to create User instance
        $userGardens = $this->gardenService->getUserGardensForDropdown($userId, $isAdmin);

        return [
            'area' => $area,
            'userGardens' => $userGardens,
            'areaTypes' => $this->getAvailableAreaTypes(),
            'isAdmin' => $isAdmin,
        ];
    }

    private function getAvailableAreaTypes(): array
    {
        return AreaTypeEnum::options()->toArray();
    }
}
