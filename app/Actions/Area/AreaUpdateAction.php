<?php

declare(strict_types=1);

namespace App\Actions\Area;

use App\DTOs\Area\AreaUpdateDTO;
use App\Models\Area;
use App\Repositories\Area\Contracts\AreaRepositoryInterface;
use DB;
use Log;
use Throwable;

final readonly class AreaUpdateAction
{
    public function __construct(
        private AreaRepositoryInterface $repository
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Area $area, AreaUpdateDTO $data): Area
    {
        try {
            Log::info('Updating area', ['area_id' => $area->id]);

            $area = DB::transaction(fn (): Area => $this->repository->update($area, $data));

            Log::info('Area updated successfully', ['area_id' => $area->id]);

        } catch (Throwable $exception) {
            Log::error('Error updating area', ['error' => $exception->getMessage(), 'area_id' => $area->id]);
            throw $exception;
        }

        return $area;
    }
}
