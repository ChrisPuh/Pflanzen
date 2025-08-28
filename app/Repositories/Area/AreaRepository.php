<?php

declare(strict_types=1);

namespace App\Repositories\Area;

use App\DTOs\Shared\Contracts\WritableDTOInterface;
use App\Models\Area;
use App\Repositories\Area\Contracts\AreaRepositoryInterface;
use App\Repositories\Shared\AbstractEloquentRepository;
use Illuminate\Database\Eloquent\Builder;

final class AreaRepository extends AbstractEloquentRepository implements AreaRepositoryInterface
{
    public function queryForUser(int $user_id, bool $isAdmin): Builder
    {
        return $this->queryForUserBase(user_id: $user_id, isAdmin: $isAdmin)
            ->with(['garden:id,name,type', 'plants:id,name']);
    }

    public function queryForUserStatistics(int $user_id, bool $isAdmin): Builder
    {
        return $this->queryForUserBase(user_id: $user_id, isAdmin: $isAdmin);
    }

    public function store(WritableDTOInterface $data): Area
    {
        return $this->baseQuery()->create($data->toModelData());
    }

    public function update(Area $area, WritableDTOInterface $data): Area
    {
        $area->update($data->toModelData());

        return $area->fresh();
    }

    public function delete(Area $area, WritableDTOInterface $data): bool
    {
        $area->update($data->toModelData());

        return $area->delete();
    }

    public function getModelClass(): string
    {
        return Area::class;
    }
}

