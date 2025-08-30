<?php

declare(strict_types=1);

namespace App\Repositories\Area;

use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\DTOs\Area\Actions\DetachPlantFromAreaDTO;
use App\Models\Area;
use App\Repositories\Area\Contracts\AreaPlantRepositoryInterface;

final class AreaPlantRepository implements AreaPlantRepositoryInterface
{
    public function attachPlantsToArea(Area $area, AttachPlantToAreaDTO $data): array
    {
        return $area->plants()->syncWithoutDetaching($data->toModelData());
    }

    public function detachPlantFromArea(Area $area, DetachPlantFromAreaDTO $data): bool
    {
        $detached = $area->plants()->detach($data->plantId);

        return $detached > 0;
    }
}
