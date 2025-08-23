<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Area\AreaStoreDTO;
use App\DTOs\Area\AreaUpdateDTO;
use App\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface AreaRepositoryInterface
{
    public function findById(int $id): ?Area;

    public function findWithTrashed(int $id): ?Area;

    public function getUserAreas(User $user, array $filters = [], bool $isAdmin = false): LengthAwarePaginator;

    public function getUserAreasQuery(User $user, bool $isAdmin = false): Builder;

    public function getAreaStatistics(User $user, bool $isAdmin = false): array;

    public function create(AreaStoreDTO $data): Area;

    public function update(Area $area, AreaUpdateDTO $data): Area;

    public function delete(Area $area): bool;

    public function restore(Area $area): bool;

    public function forceDelete(Area $area): bool;
}
