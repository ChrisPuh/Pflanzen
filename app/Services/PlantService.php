<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PlantCategoryEnum;
use App\Enums\PlantTypeEnum;
use App\Models\Plant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class PlantService
{
    /**
     * Get filtered and paginated plants based on the provided filters.
     *
     * @param  array<string>  $categories
     */
    public function getFilteredPlants(
        ?string $search = null,
        ?string $type = null,
        array $categories = [],
        int $perPage = 12
    ): LengthAwarePaginator {
        return Plant::query()
            ->with(['plantType', 'categories'])
            ->when($search, fn (Builder $query, string $search): Builder => $this->applySearchFilter($query, $search))
            ->when($type, fn (Builder $query, string $type): Builder => $this->applyTypeFilter($query, $type))
            ->when($categories !== [], fn (Builder $query): Builder => $this->applyCategoryFilter($query, $categories))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get all available plant types for the filter dropdown.
     *
     * @return array<PlantTypeEnum>
     */
    public function getAvailablePlantTypes(): array
    {
        return PlantTypeEnum::cases();
    }

    /**
     * Get all available plant categories for the filter dropdown.
     *
     * @return array<PlantCategoryEnum>
     */
    public function getAvailablePlantCategories(): array
    {
        return PlantCategoryEnum::cases();
    }

    /**
     * Get a plant with its relationships loaded for display.
     */
    public function getPlantForDisplay(Plant $plant): Plant
    {
        return $plant->load(['plantType', 'categories']);
    }

    /**
     * Get related plants based on the given plant's type and categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Plant>
     */
    public function getRelatedPlants(Plant $plant, int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        return Plant::query()
            ->with(['plantType', 'categories'])
            ->where('id', '!=', $plant->id)
            ->where(function (Builder $query) use ($plant): void {
                // Same type
                $query->where('plant_type_id', $plant->plant_type_id);

                // Or shares categories
                if ($plant->categories->isNotEmpty()) {
                    $categoryIds = $plant->categories->pluck('id')->toArray();
                    $query->orWhereHas('categories', function (Builder $query) use ($categoryIds): void {
                        $query->whereIn('categories.id', $categoryIds);
                    });
                }
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Get all data needed for plants index page.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getIndexData(array $filters): array
    {
        $search = $filters['search'] ?? null;
        $type = $filters['type'] ?? null;
        $categories = $filters['categories'] ?? [];

        $plants = $this->getFilteredPlants(
            search: $search,
            type: $type,
            categories: $categories
        );

        $plantTypes = $this->getAvailablePlantTypes();
        $plantCategories = $this->getAvailablePlantCategories();

        // Format plant types for select component
        $plantTypesOptions = collect($plantTypes)->mapWithKeys(fn (PlantTypeEnum $plantType): array => [$plantType->value => $plantType->getLabel()]);

        // Format plant categories for checkbox options
        $plantCategoriesOptions = collect($plantCategories)->mapWithKeys(fn (PlantCategoryEnum $category): array => [$category->value => $category->getLabel()]);

        return [
            'plants' => $plants,
            'search' => $search,
            'selectedType' => $type,
            'selectedCategories' => $categories,
            'plantTypesOptions' => $plantTypesOptions,
            'plantCategoriesOptions' => $plantCategoriesOptions,
            'stats' => $this->getPlantStatistics(),
        ];
    }

    /**
     * Get all data needed for plant show page.
     *
     * @return array<string, mixed>
     */
    public function getShowData(Plant $plant): array
    {
        $plantWithRelations = $this->getPlantForDisplay($plant);
        $relatedPlants = $this->getRelatedPlants($plant);

        return [
            'plant' => $plantWithRelations,
            'relatedPlants' => $relatedPlants,
        ];
    }

    /**
     * Get plant statistics for the index page.
     *
     * @return array{total: int, by_type: array<string, int>, by_category: array<string, int>}
     */
    public function getPlantStatistics(): array
    {
        $total = Plant::count();

        $byType = Plant::query()
            ->select('plant_type_id')
            ->selectRaw('COUNT(*) as count')
            ->with('plantType')
            ->groupBy('plant_type_id')
            ->get()
            ->mapWithKeys(fn (object $item): array => [
                $item->plantType->name->getLabel() => $item->count,
            ])
            ->toArray();

        $byCategory = Plant::query()
            ->join('category_plant', 'plants.id', '=', 'category_plant.plant_id')
            ->join('categories', 'category_plant.category_id', '=', 'categories.id')
            ->select('categories.name')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('categories.name')
            ->get()
            ->mapWithKeys(fn (object $item): array => [
                PlantCategoryEnum::from($item->name)->getLabel() => $item->count,
            ])
            ->toArray();

        return [
            'total' => $total,
            'by_type' => $byType,
            'by_category' => $byCategory,
        ];
    }

    /**
     * Apply search filter to the query.
     */
    private function applySearchFilter(Builder $query, string $search): Builder
    {
        return $query->where(fn (Builder $query): Builder => $query
            ->where('name', 'like', "%{$search}%")
            ->orWhere('latin_name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
        );
    }

    /**
     * Apply type filter to the query.
     */
    private function applyTypeFilter(Builder $query, string $type): Builder
    {
        return $query->whereHas('plantType', function (Builder $query) use ($type): Builder {
            $enumValue = PlantTypeEnum::from($type);

            return $query->where('name', $enumValue);
        });
    }

    /**
     * Apply category filter to the query.
     *
     * @param  array<string>  $categories
     */
    private function applyCategoryFilter(Builder $query, array $categories): Builder
    {
        return $query->whereHas('categories', function (Builder $query) use ($categories): Builder {
            $enumValues = array_map(
                fn (string $value): PlantCategoryEnum => PlantCategoryEnum::from($value),
                $categories
            );

            return $query->whereIn('name', $enumValues);
        });
    }
}
