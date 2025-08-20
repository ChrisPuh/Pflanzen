<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Garden\GardenTypeEnum;
use App\Models\Garden;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class GardenService
{
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

        // Get total plants across all gardens through areas
        $totalPlants = 0;
        $gardens = (clone $query)->with('areas')->get();
        foreach ($gardens as $garden) {
            $totalPlants += $garden->plants()->count();
        }

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
            $totalPlants += $garden->plants()->count();
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
     * Get recently active gardens for a user.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Garden>
     */
    public function getRecentlyActiveGardens(User $user, int $limit = 5): \Illuminate\Database\Eloquent\Collection
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
     * @return \Illuminate\Database\Eloquent\Collection<int, Garden>
     */
    public function getGardensByLocation(User $user, string $city): \Illuminate\Database\Eloquent\Collection
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

        $totalPlants = 0;
        $gardensWithAreas = (clone $userGardens)->with('areas')->get();
        foreach ($gardensWithAreas as $garden) {
            $totalPlants += $garden->plants()->count();
        }

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
     * @return \Illuminate\Database\Eloquent\Collection<int, Garden>
     */
    public function getArchivedGardensForUser(User $user, bool $isAdmin = false): \Illuminate\Database\Eloquent\Collection
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
     * @return \Illuminate\Database\Eloquent\Collection<int, Garden>
     */
    public function searchGardens(User $user, string $search, bool $isAdmin = false): \Illuminate\Database\Eloquent\Collection
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
}
