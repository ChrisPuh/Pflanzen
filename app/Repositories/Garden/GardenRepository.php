<?php

declare(strict_types=1);

namespace App\Repositories\Garden;

use App\Models\Garden;
use App\Repositories\Garden\Contracts\GardenRepositoryInterface;
use App\Repositories\Shared\AbstractEloquentRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final class GardenRepository extends AbstractEloquentRepository implements GardenRepositoryInterface
{
    public function getModelClass(): string
    {
        return Garden::class;
    }

    public function getForDropdown(int $userId, bool $isAdmin): Collection
    {
        return $this->baseQuery()
            ->when(! $isAdmin, fn (Builder $query) => $query->where('user_id', $userId))
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();
    }

    public function getFilterOptions(int $userId, bool $isAdmin): SupportCollection
    {
        $gardens = $this->getForDropdown($userId, $isAdmin);

        return $gardens->mapWithKeys(function (Garden $garden) use ($isAdmin) {
            $label = $garden->name;
            if ($isAdmin) {
                $label .= ' ('.$garden->type->getLabel().')';
            }

            return [$garden->id => $label];
        });
    }
}
