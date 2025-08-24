<?php

namespace App\Services;

use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\Models\Area;

class AreaPlantService
{
    public function __construct(){}

    public function attachPlantsToArea(Area $area,  AttachPlantToAreaDTO $data): void
    {
        $area->plants()->syncWithoutDetaching($data->toModelData());
    }
}
