<?php

declare(strict_types=1);

namespace App\DTOs\Area;

use App\DTOs\Shared\Contracts\WritableDTOInterface;

final class AreaDeleteDTO implements WritableDTOInterface
{
    public function __construct(

        public int  $areaId,
        public string $name,
        public ?bool $isActive = false,
        // TODO add reason for deletion
        // public ?string $reason = null,
    )
    {
    }

    public static function fromValidatedRequest(array $validated): self
    {
        return new self(
            areaId: (int)$validated['id'],
            name: (string)$validated['name'],
            isActive: (bool)$validated['is_active'],
        // reason: isset($validated['reason']) ? (string) $validated['reason'] : null,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toModelData(): array
    {
        return [
            'id'=> $this->areaId,
            'is_active' => $this->isActive,
        ];
    }
}
