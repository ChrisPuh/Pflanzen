<?php

declare(strict_types=1);

namespace App\Http\Requests\Area;

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
            // 'reason' => ['nullable', 'string', 'max:255'], // falls du später Reason nutzen willst
        ];
    }

    protected function prepareForValidation(): void
    {
        // User gibt das NICHT vor → wir setzen es hier
        $this->merge([
            'is_active' => false,
        ]);
    }
}
