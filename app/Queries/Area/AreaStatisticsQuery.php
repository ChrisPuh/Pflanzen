<?php

declare(strict_types=1);

namespace App\Queries\Area;

use App\DTOs\Area\AreaStatisticsDTO;
use App\Enums\Area\AreaTypeEnum;
use App\Repositories\Area\AreaRepository;

final readonly class AreaStatisticsQuery
{
    public function __construct(
        private AreaRepository $repository,
    )
    {
    }

    /**
     * Get area statistics for a user.
     */
    public function execute(int $user_id, bool $isAdmin = false): AreaStatisticsDTO
    {
        $baseQuery = fn() => $this->repository->queryForUserStatistics(user_id: $user_id, isAdmin: $isAdmin);

        return new AreaStatisticsDTO(
            total: $baseQuery()->count(),
            active: $baseQuery()->active()->count(),
            planting: $baseQuery()->whereIn('type', AreaTypeEnum::getPlantingAreaValues())->count(),
        );
    }


}
