<?php

declare(strict_types=1);

namespace App\Repositories\Area;

use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\Models\Area;
use App\Repositories\Area\Contracts\AreaPlantRepositoryInterface;

final class AreaPlantRepository implements AreaPlantRepositoryInterface
{
    public function attachPlantsToArea(Area $area, AttachPlantToAreaDTO $data): array
    {
        return $area->plants()->syncWithoutDetaching($data->toModelData());
    }
}
