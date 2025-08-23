<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\Area\AreaStoreDTO;
use App\Models\Area;
use App\Repositories\Contracts\AreaRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class AreaStoreAction
{
    public function __construct(
        private AreaRepositoryInterface $repository
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(AreaStoreDTO $data): Area
    {
        try {
            Log::info('Creating new area', ['name' => $data->name]);

            $area = DB::transaction(fn (): Area => $this->repository->store($data));

            Log::info('Area created successfully', ['area_id' => $area->id, 'name' => $data->name]);

            return $area;

        } catch (Throwable $e) {
            Log::error('Error creating area', ['error' => $e->getMessage(), 'name' => $data->name]);
            throw $e;
        }
    }
}
