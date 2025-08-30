<?php

declare(strict_types=1);

namespace App\Repositories\Area\Contracts;

use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\DTOs\Area\Actions\DetachPlantFromAreaDTO;
use App\Models\Area;

interface AreaPlantRepositoryInterface
{
    public function attachPlantsToArea(Area $area, AttachPlantToAreaDTO $data): array;

    public function detachPlantFromArea(Area $area, DetachPlantFromAreaDTO $data): bool;
}
