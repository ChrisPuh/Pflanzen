<?php

declare(strict_types=1);

namespace App\Queries\Area;

use App\Models\Area;
use App\Repositories\Area\Contracts\AreaRepositoryInterface;

final readonly class AreaEditQuery
{
    public function __construct(private AreaRepositoryInterface $repository) {}

    public function execute(int $areaId): Area
    {
        // Get area via repository
        return $this->repository->queryForShow($areaId)->firstOrFail();
    }
}
