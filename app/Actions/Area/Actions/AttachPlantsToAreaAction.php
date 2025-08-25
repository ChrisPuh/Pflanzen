<?php

declare(strict_types=1);

namespace App\Actions\Area\Actions;

use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\Models\Area;
use App\Repositories\Area\Contracts\AreaPlantRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class AttachPlantsToAreaAction
{
    public function __construct(private AreaPlantRepositoryInterface $repository) {}

    /**
     * @throws Throwable
     */
    public function execute(Area $area, AttachPlantToAreaDTO $data): array
    {
        $areaId = $area->id;
        try {
            Log::info('Attaching plants to area', ['area_id' => $areaId]);

            $attached = DB::transaction(fn (): array => $this->repository->attachPlantsToArea($area, $data));

            Log::info('Plants attached to area successfully', ['area_id' => $areaId, 'attached_plants' => $attached]);

        } catch (Throwable $exception) {
            Log::error('Failed to attach plants to area', ['area_id' => $areaId, 'error' => $exception->getMessage()]);
            throw $exception;
        }

        return $attached;
    }
}
