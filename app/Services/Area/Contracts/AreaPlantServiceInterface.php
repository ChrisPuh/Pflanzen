<?php

declare(strict_types=1);

namespace App\Services\Area\Contracts;

use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\DTOs\Area\Actions\DetachPlantFromAreaDTO;
use App\Models\Area;

interface AreaPlantServiceInterface
{
    public function attachPlantsToArea(Area $area, AttachPlantToAreaDTO $data): void;

    public function detachPlantFromArea(Area $area, DetachPlantFromAreaDTO $data): void;
}
