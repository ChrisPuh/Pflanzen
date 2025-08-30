<?php

declare(strict_types=1);

namespace App\Http\Requests\Area;

use App\DTOs\Area\AreaDeleteDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class AreaDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $area = $this->route('area');

        Gate::authorize('delete', $area);

        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => ['required', 'boolean'],
            'id' => ['required', 'integer', 'exists:areas,id'],
            'name' => ['required', 'string', 'max:255'],
            // 'reason' => ['nullable', 'string', 'max:255'], // falls du später Reason nutzen willst
        ];
    }

    public function toDTO(): AreaDeleteDTO
    {
        return AreaDeleteDTO::fromValidatedRequest($this->validated());
    }

    protected function prepareForValidation(): void
    {
        $area = $this->route('area');
        // User gibt das NICHT vor → wir setzen es hier
        $this->merge([
            'is_active' => false,
            'id' => (int) $area->id,
            'name' => $area->name,
        ]);

    }
}
