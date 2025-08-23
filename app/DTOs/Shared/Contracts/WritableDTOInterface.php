<?php

declare(strict_types=1);

namespace App\DTOs\Shared\Contracts;

interface WritableDTOInterface
{
    /**
     * Transform the DTO into an array suitable for model operations.
     *
     * @return array<string, mixed>
     */
    public function toModelData(): array;
}
