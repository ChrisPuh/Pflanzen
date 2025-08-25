<?php

declare(strict_types=1);

namespace App\Repositories\Area;

use _PHPStan_f9a2208af\Nette\NotImplementedException;
use App\DTOs\Shared\Contracts\WritableDTOInterface;
use App\Models\Area;
use App\Models\User;
use App\Repositories\Area\Contracts\AreaRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class AreaRepository implements AreaRepositoryInterface
{
    public function findById(int $id): ?Area
    {
        throw new NotImplementedException('Method not implemented yet.');
    }

    public function findWithTrashed(int $id): ?Area
    {
        throw new NotImplementedException('Method not implemented yet.');
    }

    public function getUserAreas(User $user, array $filters = [], bool $isAdmin = false): LengthAwarePaginator
    {
        throw new NotImplementedException('Method not implemented yet.');
    }

    public function getUserAreasQuery(User $user, bool $isAdmin = false): Builder
    {
        throw new NotImplementedException('Method not implemented yet.');
    }

    public function getAreaStatistics(User $user, bool $isAdmin = false): array
    {
        throw new NotImplementedException('Method not implemented yet.');
    }

    public function store(WritableDTOInterface $data): Area
    {
        return Area::query()->create($data->toModelData());
    }

    public function update(Area $area, WritableDTOInterface $data): Area
    {
        $area->update($data->toModelData());

        return $area->fresh();
    }

    public function delete(Area $area, WritableDTOInterface $data): bool
    {
        // TODO handle update withe DeleteDTO
        $area->update($data->toModelData());

        return $area->delete();
    }

    public function restore(Area $area): bool
    {
        throw new NotImplementedException('Method not implemented yet.');
    }

    public function forceDelete(Area $area): bool
    {
        throw new NotImplementedException('Method not implemented yet.');
    }
}
