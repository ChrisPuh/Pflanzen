<?php

declare(strict_types=1);

namespace App\DTOs\Area;

use App\DTOs\Shared\Contracts\WritableDTOInterface;

final class AreaDeleteDTO implements WritableDTOInterface
{
    public function __construct(

        public bool $isActive = false,
        // TODO add reason for deletion
        // public ?string $reason = null,
    ) {}

    public static function fromValidatedRequest(array $validated): self
    {
        return new self(
            isActive: (bool) $validated['is_active']
            // reason: isset($validated['reason']) ? (string) $validated['reason'] : null,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toModelData(): array
    {
        return [
            'is_active' => $this->isActive,
        ];
    }
}
