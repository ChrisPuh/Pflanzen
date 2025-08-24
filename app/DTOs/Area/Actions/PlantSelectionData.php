<?php

namespace App\DTOs\Area\Actions;

use Illuminate\Support\Carbon;

readonly class PlantSelectionData
{
    public function __construct(
        public int     $plantId,
        public int     $quantity,
        public ?string $notes,
        public ?Carbon $plantedAt,
    )
    {
    }
}
