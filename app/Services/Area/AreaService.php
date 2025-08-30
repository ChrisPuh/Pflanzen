<?php

declare(strict_types=1);

namespace App\Services\Area;

use App\Actions\Area\AreaDeleteAction;
use App\Actions\Area\AreaStoreAction;
use App\Actions\Area\AreaUpdateAction;
use App\DTOs\Area\AreaDeleteDTO;
use App\DTOs\Area\AreaIndexFilterDTO;
use App\DTOs\Area\AreaStoreDTO;
use App\DTOs\Area\AreaUpdateDTO;
use App\Models\Area;
use App\Models\Plant;
use App\Models\PlantType;
use App\Models\User;
use App\Queries\Area\AreaEditQuery;
use App\Queries\Area\AreaFilterOptionsQuery;
use App\Queries\Area\AreaIndexQuery;
use App\Queries\Area\AreaShowQuery;
use App\Queries\Area\AreaStatisticsQuery;
use App\Services\GardenService;
use App\Services\PlantService;
use Throwable;

final readonly class AreaService
{
    public function __construct(
        private AreaStoreAction $storeAction,
        private AreaUpdateAction $updateAction,
        private AreaDeleteAction $deleteAction,
        private AreaIndexQuery $indexQuery,
        private AreaShowQuery $showQuery,
        private AreaEditQuery $editQuery,
        private AreaStatisticsQuery $statisticsQuery,
        private AreaFilterOptionsQuery $filterOptionsQuery,
        private GardenService $gardenService,
        private PlantService $plantService,
    ) {}

    /**
     * Get all data needed for area create form.
     *
     * @return array<string, mixed>
     */
    public function getCreateData(int $userId, bool $isAdmin = false, ?int $preselectedGardenId = null): array
    {
        // Get user gardens
        $userGardens = $this->gardenService->getUserGardensForDropdown($userId, $isAdmin);
        $selectedGarden = $this->gardenService->getSelectedGarden($userGardens, $preselectedGardenId);

        // Get area types
        $areaTypes = \App\Enums\Area\AreaTypeEnum::options()->toArray();

        return [
            'userGardens' => $userGardens,
            'selectedGarden' => $selectedGarden,
            'areaTypes' => $areaTypes,
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
        // Get area with relationships
        $area = $this->editQuery->execute($area->id);

        // Get user gardens
        $userGardens = $this->gardenService->getUserGardensForDropdown($user->id, $isAdmin);

        // Get area types
        $areaTypes = \App\Enums\Area\AreaTypeEnum::options()->toArray();

        return [
            'area' => $area,
            'userGardens' => $userGardens,
            'areaTypes' => $areaTypes,
            'isAdmin' => $isAdmin,
        ];
    }

    /**
     * Get all data needed for areas index page.
     *
     * @return array<string, mixed>
     */
    public function getIndexData(int $userId, AreaIndexFilterDTO $filter, bool $isAdmin = false): array
    {
        // Get filtered and paginated areas
        $areas = $this->indexQuery
            ->execute(
                user_id: $userId,
                filter: $filter,
                isAdmin: $isAdmin
            );

        // Get statistics
        $statistics = $this->statisticsQuery
            ->execute(user_id: $userId, isAdmin: $isAdmin);

        // Get filter options
        $filterOptions = $this->filterOptionsQuery
            ->execute(user_id: $userId, isAdmin: $isAdmin);

        return [
            'areas' => $areas,
            'gardenOptions' => $filterOptions->getGardens(),
            'areaTypeOptions' => $filterOptions->getAreaTypes(),
            'areaCategoryOptions' => $filterOptions->getCategories(),
            'totalAreas' => $statistics->total,
            'activeAreas' => $statistics->active,
            'plantingAreas' => $statistics->planting,
            'isAdmin' => $isAdmin,
            'filters' => $filter,
        ];
    }

    /**
     * Get all data needed for area show page.
     *
     * @return array<string, mixed>
     */
    public function getShowData(Area $area): array
    {
        // Get area with relationships
        $area = $this->showQuery->execute($area->id);

        // Get available plants
        $availablePlants = $this->plantService->getAvailablePlantsForArea();

        return [
            'area' => $area,
            'availablePlants' => $availablePlants,
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
    public function deleteArea(AreaDeleteDTO $data): bool
    {
        // 1. Action ausführen (macht die eigentliche Arbeit)
        // $area = $this->deleteAction->execute($area);

        // 2. TODO implement Cache invalidieren
        // $this->cache->clearAreaCache();

        // 3. TODO  implement Benachrichtigung senden
        // $this->notifications->sendAreaDeletedNotification($area);

        return $this->deleteAction->execute($data);
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
     * Get filtered plants for area excluding already planted ones.
     */
    public function getFilteredPlantsForArea(Area $area, string $search = '', ?int $plantTypeId = null): \Illuminate\Support\Collection
    {
        $plants = $this->plantService->getAvailablePlantsForArea();

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
}
