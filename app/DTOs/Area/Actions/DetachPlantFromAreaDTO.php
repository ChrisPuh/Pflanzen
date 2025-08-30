<?php

declare(strict_types=1);

namespace App\DTOs\Area\Actions;

use App\DTOs\Shared\Contracts\WritableDTOInterface;

final readonly class DetachPlantFromAreaDTO implements WritableDTOInterface
{
    public function __construct(
        public int $plantId,
    ) {}

    public static function fromValidatedRequest(array $validated): self
    {
        return new self(
            plantId: (int) $validated['plant_id'],
        );
    }

    public static function fromIds(int $plantId): self
    {
        return new self(
            plantId: $plantId,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toModelData(): array
    {
        return [
            'plant_id' => $this->plantId,
        ];
    }
}
