<?php

declare(strict_types=1);

namespace App\Queries\Area;

use App\Enums\Area\AreaTypeEnum;
use App\Models\Garden;
use App\Models\User;
use App\Services\GardenService;
use Illuminate\Database\Eloquent\Collection;

final readonly class AreaCreateQuery
{
    public function __construct(private GardenService $gardenService)
    {
    }

    public function execute(int $userId, bool $isAdmin, ?int $preselectedGardenId = null): array
    {
        // Get user gardens via GardenService
        $userGardens = $this->gardenService->getUserGardensForDropdown($userId, $isAdmin);
        $selectedGarden = $this->gardenService->getSelectedGarden($userGardens, $preselectedGardenId);

        return [
            'userGardens' => $userGardens,
            'selectedGarden' => $selectedGarden,
            'areaTypes' => $this->getAvailableAreaTypes(),
            'isAdmin' => $isAdmin,
        ];
    }

    private function getAvailableAreaTypes(): array
    {
        return AreaTypeEnum::options()->toArray();
    }
}
