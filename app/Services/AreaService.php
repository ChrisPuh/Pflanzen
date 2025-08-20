<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Area\AreaTypeEnum;
use App\Models\Area;
use App\Models\Garden;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

final class AreaService
{
    /**
     * Get user's gardens for area form dropdown.
     *
     * @return Collection<int, Garden>
     */
    public function getUserGardens(User $user, bool $isAdmin = false): Collection
    {
        return Garden::query()
            ->when(! $isAdmin, function (\Illuminate\Database\Eloquent\Builder $query) use ($user): void {
                $query->where('user_id', $user->id);
            })
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get selected garden from user's gardens by ID.
     */
    public function getSelectedGarden(Collection $userGardens, ?int $gardenId): ?Garden
    {
        if ($gardenId === null) {
            return null;
        }

        return $userGardens->firstWhere('id', $gardenId);
    }

    /**
     * Get all available area types for forms.
     *
     * @return array<array{value: string, label: string, description: string, category: string}>
     */
    public function getAvailableAreaTypes(): array
    {
        return AreaTypeEnum::options()->toArray();
    }

    /**
     * Get all data needed for area create form.
     *
     * @return array<string, mixed>
     */
    public function getCreateData(User $user, bool $isAdmin = false, ?int $preselectedGardenId = null): array
    {
        $userGardens = $this->getUserGardens($user, $isAdmin);
        $selectedGarden = $this->getSelectedGarden($userGardens, $preselectedGardenId);

        return [
            'userGardens' => $userGardens,
            'selectedGarden' => $selectedGarden,
            'areaTypes' => $this->getAvailableAreaTypes(),
            'isAdmin' => $isAdmin,
        ];
    }

    /**
     * Get all data needed for area edit form.
     *
     * @return array<string, mixed>
     */
    public function getEditData(User $user, Area $area, bool $isAdmin = false): array
    {
        $userGardens = $this->getUserGardens($user, $isAdmin);

        return [
            'area' => $area,
            'userGardens' => $userGardens,
            'areaTypes' => $this->getAvailableAreaTypes(),
            'isAdmin' => $isAdmin,
        ];
    }

    /**
     * Get all data needed for areas index page.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getIndexData(User $user, array $filters, bool $isAdmin = false): array
    {
        // Get filtered and paginated areas
        $areas = $this->getFilteredAreas($user, $filters, $isAdmin);

        // Get statistics
        $statistics = $this->getAreaStatistics($user, $isAdmin);

        // Get filter options
        $filterOptions = $this->getFilterOptions($user, $isAdmin);

        return [
            'areas' => $areas,
            'gardenOptions' => $filterOptions['gardens'],
            'areaTypeOptions' => $filterOptions['areaTypes'],
            'areaCategoryOptions' => $filterOptions['categories'],
            'isAdmin' => $isAdmin,
            'totalAreas' => $statistics['total'],
            'activeAreas' => $statistics['active'],
            'plantingAreas' => $statistics['planting'],
            'filters' => $filters,
        ];
    }

    /**
     * Get all data needed for area show page.
     *
     * @return array<string, mixed>
     */
    public function getShowData(Area $area): array
    {
        $area->load(['garden', 'plants.plantType']);

        return [
            'area' => $area,
            'availablePlants' => $this->getAvailablePlantsForArea(),
        ];
    }

    /**
     * Create a new area.
     *
     * @param  array<string, mixed>  $data
     */
    public function createArea(array $data): Area
    {
        $coordinates = $this->prepareCoordinates($data);

        return Area::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'garden_id' => (int) $data['garden_id'],
            'type' => AreaTypeEnum::from($data['type']),
            'size_sqm' => $data['size_sqm'] ?? null,
            'coordinates' => $coordinates,
            'color' => $data['color'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Update an existing area.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateArea(Area $area, array $data): Area
    {
        $coordinates = $this->prepareCoordinates($data);

        $area->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'garden_id' => (int) $data['garden_id'],
            'type' => AreaTypeEnum::from($data['type']),
            'size_sqm' => $data['size_sqm'] ?? null,
            'coordinates' => $coordinates,
            'color' => $data['color'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $area->fresh();
    }

    /**
     * Soft delete (archive) an area.
     */
    public function archiveArea(Area $area): bool
    {
        // Set area as inactive before archiving
        $area->update(['is_active' => false]);

        return $area->delete();
    }

    /**
     * Restore an archived area.
     */
    public function restoreArea(Area $area): bool
    {
        $restored = $area->restore();

        if ($restored) {
            // Reactivate the area
            $area->update(['is_active' => true]);
        }

        return $restored;
    }

    /**
     * Permanently delete an area.
     */
    public function forceDeleteArea(Area $area): bool
    {
        return $area->forceDelete();
    }

    /**
     * Add plants to an area.
     *
     * @param  array<string, mixed>  $data
     */
    public function addPlantsToArea(Area $area, array $data): void
    {
        foreach ($data['plants'] as $plantData) {
            $plantId = (int) $plantData['plant_id'];
            $quantity = (int) $plantData['quantity'];
            $notes = $plantData['notes'] ?? null;

            // Check if plant is already in this area
            $existingPivot = $area->plants()->where('plant_id', $plantId)->first();

            if ($existingPivot) {
                // Update quantity by adding to existing
                $newQuantity = $existingPivot->pivot->quantity + $quantity;
                $area->plants()->updateExistingPivot($plantId, [
                    'quantity' => $newQuantity,
                    'notes' => $notes ?: $existingPivot->pivot->notes,
                    'planted_at' => now(),
                ]);
            } else {
                // Attach new plant
                $area->plants()->attach($plantId, [
                    'quantity' => $quantity,
                    'notes' => $notes,
                    'planted_at' => now(),
                ]);
            }
        }
    }

    /**
     * Remove a plant from an area.
     */
    public function removePlantFromArea(Area $area, Plant $plant): void
    {
        $area->plants()->detach($plant->id);
    }

    /**
     * Get available plants that can be added to an area.
     *
     * @return Collection<int, Plant>
     */
    public function getAvailablePlantsForArea(): Collection
    {
        return Plant::query()
            ->with('plantType')
            ->select('id', 'name', 'latin_name', 'plant_type_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get filtered and paginated areas.
     *
     * @param  array<string, mixed>  $filters
     */
    private function getFilteredAreas(User $user, array $filters, bool $isAdmin): LengthAwarePaginator
    {
        $areasQuery = Area::query()
            ->with(['garden:id,name,type', 'plants:id,name'])
            ->whereHas('garden', function (\Illuminate\Database\Eloquent\Builder $query) use ($isAdmin, $user): void {
                if (! $isAdmin) {
                    $query->where('user_id', $user->id);
                }
            })
            ->latest();

        // Apply filters
        if (! empty($filters['garden_id'])) {
            $areasQuery->where('garden_id', (int) $filters['garden_id']);
        }

        if (! empty($filters['type'])) {
            $areasQuery->where('type', (string) $filters['type']);
        }

        if (! empty($filters['category'])) {
            $areasQuery->byCategory((string) $filters['category']);
        }

        if (! empty($filters['search'])) {
            $search = (string) $filters['search'];
            $areasQuery->where(function (\Illuminate\Database\Eloquent\Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $activeValue = $filters['active'];
            if ($activeValue === '1' || $activeValue === 1 || $activeValue === true) {
                $areasQuery->active();
            } elseif ($activeValue === '0' || $activeValue === 0 || $activeValue === false) {
                $areasQuery->where('is_active', false);
            }
        }

        return $areasQuery->paginate(12)->withQueryString();
    }

    /**
     * Get area statistics.
     *
     * @return array<string, int>
     */
    private function getAreaStatistics(User $user, bool $isAdmin): array
    {
        $baseQuery = (fn (): \Illuminate\Database\Eloquent\Builder => Area::query()->whereHas('garden', function (\Illuminate\Database\Eloquent\Builder $query) use ($isAdmin, $user): void {
            if (! $isAdmin) {
                $query->where('user_id', $user->id);
            }
        }));

        return [
            'total' => $baseQuery()->count(),
            'active' => $baseQuery()->active()->count(),
            'planting' => $baseQuery()->whereIn('type', [
                AreaTypeEnum::FlowerBed->value,
                AreaTypeEnum::VegetableBed->value,
                AreaTypeEnum::HerbBed->value,
                AreaTypeEnum::Meadow->value,
                AreaTypeEnum::TreeArea->value,
            ])->count(),
        ];
    }

    /**
     * Get filter options for the index page.
     *
     * @return array<string, mixed>
     */
    private function getFilterOptions(User $user, bool $isAdmin): array
    {
        $userGardens = $this->getUserGardens($user, $isAdmin);

        // Format garden options
        $gardenOptions = $userGardens->mapWithKeys(function (Garden $garden) use ($isAdmin) {
            $label = $garden->name;
            if ($isAdmin) {
                $label .= ' ('.$garden->type->getLabel().')';
            }

            return [$garden->id => $label];
        });

        // Format area type options
        $areaTypeOptions = collect(AreaTypeEnum::options())->mapWithKeys(fn (array $type): array => [$type['value'] => $type['label']]);

        // Format area category options
        $areaCategoryOptions = collect(AreaTypeEnum::cases())
            ->map(fn (AreaTypeEnum $type): string => $type->category())
            ->unique()
            ->values()
            ->mapWithKeys(fn (string $category): array => [$category => $category]);

        return [
            'gardens' => $gardenOptions,
            'areaTypes' => $areaTypeOptions,
            'categories' => $areaCategoryOptions,
        ];
    }

    /**
     * Prepare coordinates array from request data.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, float|int|null>|null
     */
    private function prepareCoordinates(array $data): ?array
    {
        if (! isset($data['coordinates_x']) && ! isset($data['coordinates_y'])) {
            return null;
        }

        return [
            'x' => $data['coordinates_x'] ?? null,
            'y' => $data['coordinates_y'] ?? null,
        ];
    }
}
