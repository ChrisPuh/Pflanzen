<?php

declare(strict_types=1);

namespace App\Queries\Area;

use App\DTOs\Area\AreaFilterOptionsDTO;
use App\Enums\Area\AreaTypeEnum;
use App\Repositories\Garden\Contracts\GardenRepositoryInterface;
use Illuminate\Support\Collection;

final readonly class AreaFilterOptionsQuery
{
    public function __construct(
        private GardenRepositoryInterface $gardenRepository,
    ) {}

    /**
     * Get filter options for area index page.
     */
    public function execute(int $user_id, bool $isAdmin = false): AreaFilterOptionsDTO
    {
        return new AreaFilterOptionsDTO(
            gardens: $this->gardenRepository->getFilterOptions($user_id, $isAdmin),
            areaTypes: $this->getAreaTypeOptions(),
            categories: $this->getAreaCategoryOptions(),
        );
    }

    /**
     * Get area type options.
     *
     * @return Collection<string, string>
     */
    private function getAreaTypeOptions(): Collection
    {
        return AreaTypeEnum::getFilterOptions();
    }

    /**
     * Get area category options.
     *
     * @return Collection<string, string>
     */
    private function getAreaCategoryOptions(): Collection
    {
        return AreaTypeEnum::getCategoryFilterOptions();
    }
}
