<?php

namespace App\DTOs\Area;

final readonly class AreaStatisticsDTO
{
    public function __construct(
        public int $total,
        public int $active,
        public int $planting,
        public int $archived = 0,
        public int $buildings = 0,
        public int $waterFeatures = 0,
    )
    {
    }

    /**
     * Create from array data.
     *
     * @param array<string, int> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            total: $data['total'],
            active: $data['active'],
            planting: $data['planting'],
            archived: $data['archived'] ?? 0,
            buildings: $data['buildings'] ?? 0,
            waterFeatures: $data['waterFeatures'] ?? 0,
        );
    }

    /**
     * Convert to array.
     *
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'active' => $this->active,
            'planting' => $this->planting,
            'archived' => $this->archived,
            'buildings' => $this->buildings,
            'waterFeatures' => $this->waterFeatures,
        ];
    }
}
