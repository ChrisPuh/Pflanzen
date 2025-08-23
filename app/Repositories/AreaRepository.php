<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Area\AreaStoreDTO;
use App\Models\Area;
use App\Models\User;
use App\Repositories\Contracts\AreaRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class AreaRepository implements AreaRepositoryInterface
{
    public function findById(int $id): ?Area
    {
        // TODO: Implement findById() method.
    }

    public function findWithTrashed(int $id): ?Area
    {
        // TODO: Implement findWithTrashed() method.
    }

    public function getUserAreas(User $user, array $filters = [], bool $isAdmin = false): LengthAwarePaginator
    {
        // TODO: Implement getUserAreas() method.
    }

    public function getUserAreasQuery(User $user, bool $isAdmin = false): Builder
    {
        // TODO: Implement getUserAreasQuery() method.
    }

    public function getAreaStatistics(User $user, bool $isAdmin = false): array
    {
        // TODO: Implement getAreaStatistics() method.
    }

    public function create(AreaStoreDTO $data): Area
    {
        return Area::query()->create($data->toModelData());
    }

    public function update(Area $area, array $data): Area
    {
        // TODO: Implement update() method.
    }

    public function delete(Area $area): bool
    {
        // TODO: Implement delete() method.
    }

    public function restore(Area $area): bool
    {
        // TODO: Implement restore() method.
    }

    public function forceDelete(Area $area): bool
    {
        // TODO: Implement forceDelete() method.
    }
}
