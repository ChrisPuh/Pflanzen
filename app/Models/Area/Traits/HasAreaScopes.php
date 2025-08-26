<?php

declare(strict_types=1);

namespace App\Models\Area\Traits;

use App\Enums\Area\AreaTypeEnum;
use Illuminate\Database\Eloquent\Builder;

trait HasAreaScopes
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByType(Builder $query, ?AreaTypeEnum $type): Builder
    {
        return $query->when($type instanceof AreaTypeEnum, fn ($query) => $query->where('type', $type));
    }

    public function scopeByCategory(Builder $query, ?string $category): Builder
    {
        return $query->when($category !== null && $category !== '' && $category !== '0' && $category !== '0', function ($q) use ($category): void {
            $typesInCategory = collect(AreaTypeEnum::cases())
                ->filter(fn (AreaTypeEnum $type): bool => $type->category() === $category)
                ->pluck('value')
                ->toArray();

            $q->whereIn('type', $typesInCategory);
        });
    }

    public function scopeForGarden(Builder $query, ?int $gardenId): Builder
    {
        return $query->when($gardenId !== null && $gardenId !== 0, fn ($q) => $q->where('garden_id', $gardenId));
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || $term === '' || $term === '0') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }
}
