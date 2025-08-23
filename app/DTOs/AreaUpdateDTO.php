<?php

namespace App\DTOs;

use App\Enums\Area\AreaTypeEnum;

final readonly class AreaUpdateDTO
{
    public function __construct(
        public string       $name,
        public int          $gardenId,
        public AreaTypeEnum $type,
        public bool         $isActive,
        public ?string      $description = null,
        public ?float       $sizeSqm = null,
        public ?array       $coordinates = null,
        public ?string      $color = null,
    )
    {
    }

    /**
     * Create an instance from validated array data.
     */
    public static function fromValidated(array $validated): self
    {
        return new self(
            name: (string)$validated['name'],
            gardenId: (int)$validated['garden_id'],
            type: AreaTypeEnum::from($validated['type']),
            isActive: (bool)$validated['is_active'],
            description: (string)$validated['description'] ?? null,
            sizeSqm: (float)$validated['size_sqm'] ?? null,
            coordinates: self::prepareCoordinatesFromArray($validated),
            color: (string)$validated['color'] ?? null,
        );
    }

    /**
     * Convert the DTO to an associative array for model updates.
     *
     * @return array<string, mixed>
     */
    public function toModelData(): array
    {
        return [
            'name' => (string)$this->name,
            'description' => $this->description !== null ? (string)$this->description : null,
            'garden_id' => (int)$this->gardenId,
            'type' => $this->type->value,
            'size_sqm' => $this->sizeSqm !== null ? (float)$this->sizeSqm : null,
            'coordinates' => $this->coordinates, // Already array|null
            'color' => $this->color !== null ? (string)$this->color : null,
            'is_active' => (bool)$this->isActive,
        ];
    }

    /**
     * Prepare coordinates from validated array.
     *
     * TODO implement a CoordinateValueObject
     */
    private static function prepareCoordinatesFromArray(array $validated): ?array
    {
        $x = $validated['coordinates_x'] ?? null;
        $y = $validated['coordinates_y'] ?? null;

        if ($x === null && $y === null) {
            return null;
        }

        return [
            'x' => $x,
            'y' => $y,
        ];
    }
}
