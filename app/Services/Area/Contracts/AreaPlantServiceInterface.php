<?php

declare(strict_types=1);

namespace App\Services\Area\Contracts;

use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\Models\Area;

interface AreaPlantServiceInterface
{
    public function attachPlantsToArea(Area $area, AttachPlantToAreaDTO $data): void;
}
