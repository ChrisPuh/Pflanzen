<?php

declare(strict_types=1);

namespace App\Services\Area;

use App\Actions\Area\AreaDeleteAction;
use App\Actions\Area\AreaStoreAction;
use App\Actions\Area\AreaUpdateAction;
use App\DTOs\Area\AreaDeleteDTO;
use App\DTOs\Area\AreaStoreDTO;
use App\DTOs\Area\AreaUpdateDTO;
use App\Enums\Area\AreaTypeEnum;
use App\Models\Area;
use App\Models\Garden;
use App\Models\Plant;
use App\Models\PlantType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

final readonly class AreaService
{
    public function __construct(
        private AreaStoreAction $storeAction,
        private AreaUpdateAction $updateAction,
        private AreaDeleteAction $deleteAction,
    ) {}

    /**
     * Get user's gardens for area form dropdown.
     *
     * @return Collection<int, Garden>
     */
    public function getUserGardens(User $user, bool $isAdmin = false): Collection
    {
        return Garden::query()
            ->when(! $isAdmin, function (Builder $query) use ($user): void {
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
     * TODO handle exceptions and errors
     *
     * @throws Throwable
     */
    public function storeArea(AreaStoreDTO $data): Area
    {
        // 1. Action ausführen (macht die eigentliche Arbeit)
        // $area = $this->createAction->execute($data);

        // 2. TODO implement Cache invalidieren
        // $this->cache->clearAreaCache();

        // 3. TODO  implement Benachrichtigung senden
        // $this->notifications->sendAreaCreatedNotification($area);

        return $this->storeAction->execute($data);
    }

    /**
     * Update an existing area.
     *
     *  TODO handle exceptions and errors
     *
     * @throws Throwable
     */
    public function updateArea(Area $area, AreaUpdateDTO $data): Area
    {
        // 1. Action ausführen (macht die eigentliche Arbeit)
        // $area = $this->createAction->execute($data);

        // 2. TODO implement Cache invalidieren
        // $this->cache->clearAreaCache();

        // 3. TODO  implement Benachrichtigung senden
        // $this->notifications->sendAreaUpdatedNotification($area);
        return $area = $this->updateAction->execute($area, $data);
    }

    /**
     * Soft delete (delete) an area.
     *
     * @throws Throwable
     */
    public function deleteArea(Area $area, AreaDeleteDTO $data): bool
    {
        // 1. Action ausführen (macht die eigentliche Arbeit)
        // $area = $this->deleteAction->execute($area);

        // 2. TODO implement Cache invalidieren
        // $this->cache->clearAreaCache();

        // 3. TODO  implement Benachrichtigung senden
        // $this->notifications->sendAreaDeletedNotification($area);

        return $this->deleteAction->execute($area, $data);
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
            ->select('id', 'name', 'latin_name', 'description', 'plant_type_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get filtered plants for area excluding already planted ones.
     */
    public function getFilteredPlantsForArea(Area $area, string $search = '', ?int $plantTypeId = null): \Illuminate\Support\Collection
    {
        $plants = $this->getAvailablePlantsForArea();

        // Filter already planted plants in this area
        $plants = $plants->filter(fn (Plant $plant): bool => ! $area->plants()->where('plant_id', $plant->id)->exists());

        // Apply search filter
        if ($search !== '' && $search !== '0') {
            $plants = $plants->filter(function (Plant $plant) use ($search): bool {
                $searchLower = mb_strtolower($search);

                return str_contains(mb_strtolower($plant->name), $searchLower) ||
                    ($plant->latin_name && str_contains(mb_strtolower($plant->latin_name), $searchLower)) ||
                    ($plant->description && str_contains(mb_strtolower($plant->description), $searchLower));
            });
        }

        // Apply plant type filter
        if ($plantTypeId !== null && $plantTypeId !== 0) {
            $plants = $plants->filter(fn (Plant $plant): bool => $plant->plant_type_id === $plantTypeId);
        }

        return $plants->sortBy('name')->values();
    }

    /**
     * Get plant type options for dropdowns.
     *
     * @return array<int, string>
     */
    public function getPlantTypeOptions(): array
    {
        return PlantType::orderBy('name')
            ->get()
            ->mapWithKeys(fn (PlantType $type): array => [$type->id => $type->name->getLabel()])
            ->toArray();
    }

    public function getArchivedArea(int $areaId): Area
    {
        return Area::withTrashed()->findOrFail($areaId);
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
            ->whereHas('garden', function (Builder $query) use ($isAdmin, $user): void {
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
            $areasQuery->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
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
        $baseQuery = (fn (): Builder => Area::query()->whereHas('garden', function (Builder $query) use ($isAdmin, $user): void {
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
}
