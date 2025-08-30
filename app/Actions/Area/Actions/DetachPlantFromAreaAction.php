<?php

declare(strict_types=1);

namespace App\Actions\Area\Actions;

use App\DTOs\Area\Actions\DetachPlantFromAreaDTO;
use App\Models\Area;
use App\Repositories\Area\Contracts\AreaPlantRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class DetachPlantFromAreaAction
{
    public function __construct(private AreaPlantRepositoryInterface $repository) {}

    /**
     * @throws Throwable
     */
    public function execute(Area $area, DetachPlantFromAreaDTO $data): bool
    {
        $areaId = $area->id;
        try {
            Log::info('Detaching plant from area', ['area_id' => $areaId, 'plant_id' => $data->plantId]);

            $detached = DB::transaction(fn (): bool => $this->repository->detachPlantFromArea($area, $data));

            Log::info('Plant detached from area successfully', ['area_id' => $areaId, 'plant_id' => $data->plantId]);

        } catch (Throwable $exception) {
            Log::error('Failed to detach plant from area', ['area_id' => $areaId, 'plant_id' => $data->plantId, 'error' => $exception->getMessage()]);
            throw $exception;
        }

        return $detached;
    }
}
