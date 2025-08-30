<?php

declare(strict_types=1);

namespace App\Actions\Area;

use App\DTOs\Area\AreaDeleteDTO;
use App\Models\Area;
use App\Repositories\Area\Contracts\AreaRepositoryInterface;
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
    public function execute(AreaDeleteDTO $data): bool
    {

        try {
            Log::info('Deleting area', ['area_id' => $data->areaId]);

            $isDeleted = DB::transaction(fn (): bool => $this->repository->delete($data));

            Log::info('Area deleted successfully', ['area_id' => $data->areaId]);

        } catch (Throwable $exception) {
            Log::error('Error deleting area', ['error' => $exception->getMessage(), 'area_id' => $data->areaId]);
        }

        return $isDeleted ?? false;
    }
}
