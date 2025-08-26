<?php

declare(strict_types=1);

namespace App\Repositories\Area;

use App\DTOs\Shared\Contracts\WritableDTOInterface;
use App\Models\Area;
use App\Models\User;
use App\Repositories\Area\Contracts\AreaRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

final class AreaRepository implements AreaRepositoryInterface
{
    public function queryForUser(User $user, bool $isAdmin): Builder
    {
        return Area::query()
            ->with(['garden:id,name,type', 'plants:id,name'])
            ->when(! $isAdmin, fn ($q) => $q->whereHas('garden', fn ($q2) => $q2->where('user_id', $user->id)
            ));
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
        $area->update($data->toModelData());

        return $area->delete();
    }
}
