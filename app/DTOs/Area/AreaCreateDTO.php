<?php

declare(strict_types=1);

namespace App\DTOs\Area;

use App\Enums\Area\AreaTypeEnum;
use OpenSpout\Common\Exception\InvalidArgumentException;

final readonly class AreaCreateDTO
{
    public function __construct(
        public string $name,
        public int $gardenId,
        public AreaTypeEnum $type,
        public bool $isActive = true,

        public ?string $description = null,
        public ?float $sizeSqm = null,
        public ?array $coordinates = null,
        public ?string $color = null,
    ) {}

    /**
     * Create an instance of AreaCreateDTO from request data.
     *
     * @throws InvalidArgumentException
     */
    public static function fromValidatedRequest(array $validated): self
    {
        return new self(
            name: (string) $validated['name'],
            gardenId: (int) $validated['garden_id'],
            type: AreaTypeEnum::tryFrom($validated['type'] ?? throw new InvalidArgumentException('Invalid area type')) ?? throw new InvalidArgumentException('Invalid area type'),
            isActive: (bool) $validated['is_active'],

            description: isset($validated['description'])
                ? (string) $validated['description']
                : null,
            sizeSqm: isset($validated['size_sqm'])
                ? (float) $validated['size_sqm']
                : null,
            coordinates: self::prepareCoordinates($validated),
            color: isset($validated['color'])
                ? (string) $validated['color']
                : null,
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
