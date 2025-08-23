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
     * @param  array  $data  <string, mixed>
     *
     * @throws InvalidArgumentException
     */
    public static function fromValidatedRequest(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            gardenId: (int) $data['garden_id'],
            type: AreaTypeEnum::tryFrom($data['type'] ?? throw new InvalidArgumentException('Invalid area type')) ?? throw new InvalidArgumentException('Invalid area type'),
            isActive: (bool) $data['is_active'],

            description: isset($data['description'])
                ? (string) $data['description']
                : null,
            sizeSqm: isset($data['size_sqm'])
                ? (float) $data['size_sqm']
                : null,
            coordinates: self::prepareCoordinates($data),
            color: isset($data['color'])
                ? (string) $data['color']
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
