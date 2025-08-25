<?php

declare(strict_types=1);

namespace App\DTOs\Area\Actions;

use Illuminate\Support\Carbon;

final readonly class PlantSelectionData
{
    public function __construct(
        public int $plantId,
        public int $quantity,
        public ?string $notes,
        public ?Carbon $plantedAt,
    ) {}
}
