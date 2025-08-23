<?php

declare(strict_types=1);

namespace App\DTOs\Area;

final readonly class AreaCreateDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public int $gardenId,
        public string $type,
        public ?float $sizeSqm = null,
        public ?array $coordinates = null,
        public ?string $color = null,
        public bool $isActive = true,
    ) {}

    /**
     * Create an instance of AreaCreateDTO from request data.
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            gardenId: $data['garden_id'],
            type: $data['type'],
            sizeSqm: isset($data['size_sqm']) ? (float) $data['size_sqm'] : null,
            coordinates: self::prepareCoordinates($data),
            color: $data['color'] ?? null,
            isActive: $data['is_active'] ?? true,
        );
    }

    /**
     * Convert the DTO to an associative array.
     *
     * @return array<string, mixed>
     */
    public function toModelData(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'garden_id' => $this->gardenId,
            'type' => $this->type,
            'size_sqm' => $this->sizeSqm,
            'coordinates' => $this->coordinates,
            'color' => $this->color,
            'is_active' => $this->isActive,
        ];
    }

    /**
     * Prepare coordinates array from request data.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, float|int|null>|null
     */
    private static function prepareCoordinates(array $data): ?array
    {
        if (! isset($data['coordinates_x']) && ! isset($data['coordinates_y'])) {
            return null;
        }

        return [
            'x' => $data['coordinates_x'] ?? null,
            'y' => $data['coordinates_y'] ?? null,
        ];
    }
}
