<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\Area\AreaDeleteDTO;
use App\Models\Area;
use App\Repositories\Contracts\AreaRepositoryInterface;
use DB;
use Log;
use Throwable;

final readonly class AreaDeleteAction
{
    public function __construct(
        private AreaRepositoryInterface $repository
    ) {}

    /**
     * Soft delete the specified area.
     *
     * @throws Throwable
     */
    public function execute(Area $area, AreaDeleteDTO $data): bool
    {
        $areaId = $area->id;
        try {
            Log::info('Deleting area', ['area_id' => $areaId]);

            $isDeleted = DB::transaction(fn (): bool => $this->repository->delete($area, $data));

            Log::info('Area deleted successfully', ['area_id' => $areaId]);

        } catch (Throwable $exception) {
            Log::error('Error deleting area', ['error' => $exception->getMessage(), 'area_id' => $areaId]);
        }

        return $isDeleted ?? false;
    }
}
