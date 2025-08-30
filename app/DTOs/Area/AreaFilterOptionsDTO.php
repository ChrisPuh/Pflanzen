<?php

declare(strict_types=1);

namespace App\DTOs\Area;

use Illuminate\Support\Collection;

final readonly class AreaFilterOptionsDTO
{
    public function __construct(
        /** @var Collection<int, string> */
        public Collection $gardens,
        /** @var Collection<string, string> */
        public Collection $areaTypes,
        /** @var Collection<string, string> */
        public Collection $categories,
    ) {}

    public function getGardens(): Collection
    {
        return $this->gardens;
    }

    public function getAreaTypes(): Collection
    {
        return $this->areaTypes;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * Convert to array for view usage.
     *
     * @return array<string, array<int|string, string>>
     */
    public function toArray(): array
    {
        return [
            'gardens' => $this->gardens->toArray(),
            'areaTypes' => $this->areaTypes->toArray(),
            'categories' => $this->categories->toArray(),
        ];
    }
}
