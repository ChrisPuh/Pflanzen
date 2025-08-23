<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\Area\AreaUpdateDTO;
use App\Models\Area;
use DB;
use Log;
use Throwable;

final readonly class AreaUpdateAction
{
    /**
     * @throws Throwable
     */
    public function execute(Area $area, AreaUpdateDTO $data): Area
    {
        try {
            Log::info('Updating area', ['area_id' => $area->id]);

            DB::transaction(fn() => $area->update($data->toModelData()));
            Log::info('Area updated successfully', ['area_id' => $area->id]);

        } catch (Throwable $exception) {
            Log::error('Error updating area', ['error' => $exception->getMessage(), 'area_id' => $area->id]);
            throw $exception;
        }

        return $area->fresh();
    }
}
