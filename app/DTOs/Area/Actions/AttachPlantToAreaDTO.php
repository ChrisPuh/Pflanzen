<?php

namespace App\DTOs\Area\Actions;

use App\DTOs\Shared\Contracts\WritableDTOInterface;
use Illuminate\Support\Carbon;

readonly class AttachPlantToAreaDTO implements WritableDTOInterface
{
    /**
     * @param array<int, PlantSelectionData> $plants
     */
    public function __construct(
        public array $plants,
    )
    {
    }


    public static function fromValidatedRequest(array $validated): self
    {
        $plants = [];
        foreach ($validated ?? [] as $plantData) {
            $plants[] = new PlantSelectionData(
                plantId: (int)$plantData['plant_id'],
                quantity: (int)$plantData['quantity'],
                notes: $plantData['notes'] ?? null,
                plantedAt: isset($plantData['planted_at'])
                    ? Carbon::parse($plantData['planted_at'])
                    : null,
            );
        }

        return new self($plants);
    }

    /**
     * @inheritDoc
     */
    public function toModelData(): array
    {
        $modelData = [];

        foreach ($this->plants as $key => $plant) {
            $modelData[$plant->plantId] = [
                'quantity' => $plant->quantity,
                'notes' => $plant->notes,
                'planted_at' => $plant->plantedAt ?? now(),
                'plant_id' => $plant->plantId,
            ];
        }
        return $modelData;
    }
}
