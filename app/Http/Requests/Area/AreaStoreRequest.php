<?php

declare(strict_types=1);

namespace App\Http\Requests\Area;

use App\DTOs\Area\AreaStoreDTO;
use App\Models\Garden;
use Illuminate\Foundation\Http\FormRequest;

final class AreaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return AreaStoreDTO::getValidationRules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return AreaStoreDTO::getValidationMessages();
    }

    public function withValidator(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Contracts\Validation\Validator $validator): void {
            if ($this->filled('garden_id')) {
                $garden = Garden::find($this->integer('garden_id'));

                if ($garden && ! $this->user()->hasRole('admin') && $garden->user_id !== $this->user()->id) {
                    $validator->errors()->add('garden_id', 'Sie haben keine Berechtigung, Bereiche zu diesem Garten hinzuzufügen.');
                }
            }
        });
    }

    public function toDTO(): AreaStoreDTO
    {
        return AreaStoreDTO::fromValidatedRequest($this->validated());
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('color') && $this->input('color') === '') {
            $this->merge(['color' => null]);
        }
    }
}
