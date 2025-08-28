<?php

namespace App\Repositories\Garden\Contracts;

use App\Models\Garden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface GardenRepositoryInterface
{
    /**
     * Get user's gardens for dropdown options.
     *
     * @return Collection<int, Garden>
     */
    public function getForDropdown(int $userId, bool $isAdmin): Collection;

    /**
     * Get formatted garden options for filter dropdown.
     *
     * @return SupportCollection<int, string>
     */
    public function getFilterOptions(int $userId, bool $isAdmin): SupportCollection;


}
