<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Garden\GardenTypeEnum;
use App\Models\Garden;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class GardenService
{
    /**
     * Get user's gardens for dropdown selections.
     *
     * @return Collection<int, Garden>
     */
    public function getUserGardensForDropdown(int $user_id, bool $isAdmin = false): Collection
    {
        return Garden::query()
            ->when(!$isAdmin, function (Builder $query) use ($user_id): void {
                $query->where('user_id', $user_id);
            })
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get selected garden from a collection by ID.
     */
    public function getSelectedGarden(Collection $userGardens, ?int $gardenId): ?Garden
    {
        if ($gardenId === null) {
            return null;
        }

        return $userGardens->firstWhere('id', $gardenId);
    }

    /**
     * Get filtered and paginated gardens for a user.
     */
    public function getGardensForUser(
        User $user,
        bool $isAdmin = false,
        int $perPage = 12
    ): LengthAwarePaginator {
        return Garden::query()
            ->when(! $isAdmin, function (Builder $query) use ($user): void {
                $query->forUser($user);
            })
            ->with(['user', 'areas'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get a garden with its relationships loaded for display.
     */
    public function getGardenForDisplay(Garden $garden): Garden
    {
        return $garden->load(['user', 'areas']);
    }

    /**
     * Create a new garden for the user.
     *
     * @param  array<string, mixed>  $data
     */
    public function createGarden(User $user, array $data): Garden
    {
        // Process coordinates if provided
        if (isset($data['coordinates']) && is_array($data['coordinates'])) {
            $coordinates = [
                'latitude' => $data['coordinates']['latitude'],
                'longitude' => $data['coordinates']['longitude'],
            ];
            $data['coordinates'] = $coordinates;
        } else {
            $data['coordinates'] = null;
        }

        // Convert established_at to Carbon if provided
        if (isset($data['established_at']) && ! empty($data['established_at'])) {
            $data['established_at'] = \Carbon\Carbon::parse($data['established_at']);
        }

        // Create the garden
        return $user->gardens()->create($data);
    }

    /**
     * Get garden statistics for the index page.
     *
     * @return array{total: int, active: int, total_plants: int, by_type: array<string, int>}
     */
    public function getGardenStatistics(User $user, bool $isAdmin = false): array
    {
        $query = Garden::query()
            ->when(! $isAdmin, function (Builder $query) use ($user): void {
                $query->forUser($user);
            });

        $totalGardens = $query->count();
        $activeGardens = (clone $query)->where('is_active', true)->count();

        // Get total plants across all gardens through areas - use direct DB query
        $totalPlants = \Illuminate\Support\Facades\DB::table('areas')
            ->join('area_plant', 'areas.id', '=', 'area_plant.area_id')
            ->join('gardens', 'areas.garden_id', '=', 'gardens.id')
            ->when(! $isAdmin, function (\Illuminate\Database\Query\Builder $query) use ($user): void {
                $query->where('gardens.user_id', $user->id);
            })
            ->sum('area_plant.quantity');

        // Get gardens by type
        $byType = (clone $query)
            ->select('type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->mapWithKeys(fn (object $item): array => [
                $item->type->getLabel() => $item->count,
            ])
            ->toArray();

        return [
            'total' => $totalGardens,
            'active' => $activeGardens,
            'total_plants' => $totalPlants,
            'by_type' => $byType,
        ];
    }

    /**
     * Get complete gardens index data with statistics
     *
     * @return array{gardens: LengthAwarePaginator, stats: array<string, int>, hasArchivedGardens: bool}
     */
    public function getGardensIndexData(User $user, bool $isAdmin = false, int $perPage = 12): array
    {
        // Get paginated gardens
        $gardens = Garden::query()
            ->when(! $isAdmin, function (Builder $query) use ($user): void {
                $query->forUser($user);
            })
            ->with(['user', 'areas'])
            ->latest()
            ->paginate($perPage);

        // Calculate statistics
        $totalCount = $gardens->total();
        $activeCount = 0;
        $inactiveCount = 0;
        $totalAreas = 0;
        $activeAreas = 0;
        $totalPlants = 0;

        foreach ($gardens as $garden) {
            if ($garden->is_active) {
                $activeCount++;
            } else {
                $inactiveCount++;
            }

            $totalAreas += $garden->areas->count();
            $activeAreas += $garden->areas->where('is_active', true)->count();

            // Calculate plant quantity for this garden directly and add it to the garden object
            $gardenPlantQuantity = \Illuminate\Support\Facades\DB::table('areas')
                ->join('area_plant', 'areas.id', '=', 'area_plant.area_id')
                ->where('areas.garden_id', $garden->id)
                ->sum('area_plant.quantity');

            $garden->plant_count = (int) $gardenPlantQuantity;
            $totalPlants += $garden->plant_count;
        }

        $stats = [
            'total_gardens' => $totalCount,
            'active_gardens' => $activeCount,
            'inactive_gardens' => $inactiveCount,
            'total_areas' => $totalAreas,
            'active_areas' => $activeAreas,
            'total_plants' => $totalPlants,
        ];

        // Check for archived gardens
        $hasArchivedGardens = $this->getArchivedGardensForUser(
            user: $user,
            isAdmin: $isAdmin
        )->isNotEmpty();

        return [
            'gardens' => $gardens,
            'stats' => $stats,
            'hasArchivedGardens' => $hasArchivedGardens,
        ];
    }

    /**
     * Update an existing garden.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateGarden(Garden $garden, array $data): Garden
    {
        // Process coordinates if provided
        if (isset($data['coordinates']) && is_array($data['coordinates'])) {
            $coordinates = [
                'latitude' => $data['coordinates']['latitude'],
                'longitude' => $data['coordinates']['longitude'],
            ];
            $data['coordinates'] = $coordinates;
        } else {
            $data['coordinates'] = null;
        }

        // Convert established_at to Carbon if provided
        if (isset($data['established_at']) && ! empty($data['established_at'])) {
            $data['established_at'] = \Carbon\Carbon::parse($data['established_at']);
        }

        // Update the garden
        $garden->update($data);

        return $garden->fresh();
    }

    /**
     * Get all available garden types.
     *
     * @return array<GardenTypeEnum>
     */
    public function getAvailableGardenTypes(): array
    {
        return GardenTypeEnum::cases();
    }

    /**
     * Get all data needed for garden create form.
     *
     * @return array<string, mixed>
     */
    public function getCreateData(): array
    {
        return [
            'gardenTypes' => $this->getAvailableGardenTypes(),
        ];
    }

    /**
     * Get all data needed for garden edit form.
     *
     * @return array<string, mixed>
     */
    public function getEditData(Garden $garden): array
    {
        return [
            'garden' => $garden,
            'gardenTypes' => $this->getAvailableGardenTypes(),
        ];
    }

    /**
     * Get all data needed for garden show page.
     *
     * @return array<string, mixed>
     */
    public function getShowData(Garden $garden): array
    {
        $garden->load(['user', 'areas.plants']);

        // Calculate areas statistics
        $activeAreas = $garden->areas->where('is_active', true)->count();
        $plantingAreas = $garden->areas->filter(fn (\App\Models\Area $area): bool => $area->isPlantingArea())->count();

        // Calculate total plants in this garden
        $totalPlants = \Illuminate\Support\Facades\DB::table('areas')
            ->join('area_plant', 'areas.id', '=', 'area_plant.area_id')
            ->where('areas.garden_id', $garden->id)
            ->sum('area_plant.quantity');

        // Add plant quantity for each area
        $garden->areas->each(function (\App\Models\Area $area): void {
            $area->plant_quantity = $area->plants()->sum('area_plant.quantity') ?: 0;
        });

        return [
            'garden' => $garden,
            'areasStats' => [
                'total' => $garden->areas->count(),
                'active' => $activeAreas,
                'planting' => $plantingAreas,
                'total_plants' => (int) $totalPlants,
            ],
        ];
    }

    /**
     * Get all data needed for gardens archived page.
     *
     * @return array<string, mixed>
     */
    public function getArchivedData(User $user, bool $isAdmin = false): array
    {
        $archivedGardens = $this->getArchivedGardensForUser($user, $isAdmin);

        return [
            'gardens' => $archivedGardens,
            'isAdmin' => $isAdmin,
        ];
    }

    /**
     * Get recently active gardens for a user.
     *
     * @return Collection<int, Garden>
     */
    public function getRecentlyActiveGardens(User $user, int $limit = 5): Collection
    {
        return Garden::query()
            ->forUser($user)
            ->with(['areas'])
            ->where('is_active', true)
            ->latest('updated_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get gardens by location for a user.
     *
     * @return Collection<int, Garden>
     */
    public function getGardensByLocation(User $user, string $city): Collection
    {
        return Garden::query()
            ->forUser($user)
            ->with(['areas'])
            ->where('city', $city)
            ->latest()
            ->get();
    }

    /**
     * Get garden summary for dashboard.
     *
     * @return array{gardens_count: int, active_gardens: int, total_plants: int, largest_garden: ?Garden}
     */
    public function getDashboardSummary(User $user): array
    {
        $userGardens = Garden::query()->forUser($user);

        $gardensCount = $userGardens->count();
        $activeGardens = (clone $userGardens)->where('is_active', true)->count();

        // Calculate total plants directly from database
        $totalPlants = \Illuminate\Support\Facades\DB::table('areas')
            ->join('area_plant', 'areas.id', '=', 'area_plant.area_id')
            ->join('gardens', 'areas.garden_id', '=', 'gardens.id')
            ->where('gardens.user_id', $user->id)
            ->sum('area_plant.quantity');

        $largestGarden = (clone $userGardens)
            ->where('size_sqm', '>', 0)
            ->orderByDesc('size_sqm')
            ->first();

        return [
            'gardens_count' => $gardensCount,
            'active_gardens' => $activeGardens,
            'total_plants' => $totalPlants,
            'largest_garden' => $largestGarden,
        ];
    }

    /**
     * Archive (soft delete) a garden.
     */
    public function archiveGarden(Garden $garden): bool
    {
        // Set garden as inactive before archiving
        $garden->update(['is_active' => false]);

        // Soft delete the garden
        return $garden->delete();
    }

    /**
     * Restore an archived garden.
     */
    public function restoreGarden(Garden $garden): bool
    {
        // Restore the garden
        $restored = $garden->restore();

        if ($restored) {
            // Reactivate the garden
            $garden->update(['is_active' => true]);
        }

        return $restored;
    }

    /**
     * Get archived gardens for a user.
     *
     * @return Collection<int, Garden>
     */
    public function getArchivedGardensForUser(User $user, bool $isAdmin = false): Collection
    {
        return Garden::onlyTrashed()
            ->when(! $isAdmin, function (Builder $query) use ($user): void {
                $query->forUser($user);
            })
            ->with(['user', 'areas'])
            ->latest('deleted_at')
            ->get();
    }

    /**
     * Search gardens by name or description.
     *
     * @return Collection<int, Garden>
     */
    public function searchGardens(User $user, string $search, bool $isAdmin = false): Collection
    {
        return Garden::query()
            ->when(! $isAdmin, function (Builder $query) use ($user): void {
                $query->forUser($user);
            })
            ->with(['user', 'areas'])
            ->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            })
            ->latest()
            ->get();
    }

    public function getArchivedGardenById(int $gardenId): Garden
    {
        return Garden::onlyTrashed()->findOrFail($gardenId);
    }
}
