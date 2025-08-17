<?php

declare(strict_types=1);

namespace App\Http\Requests\Plants;

use App\Enums\PlantCategoryEnum;
use App\Enums\PlantTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlantsIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', Rule::enum(PlantTypeEnum::class)],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', Rule::enum(PlantCategoryEnum::class)],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'search.max' => 'Der Suchbegriff darf maximal 255 Zeichen lang sein.',
            'type.enum' => 'Der ausgewählte Pflanzentyp ist ungültig.',
            'categories.array' => 'Die Kategorien müssen als Array übermittelt werden.',
            'categories.*.enum' => 'Eine oder mehrere ausgewählte Kategorien sind ungültig.',
            'page.integer' => 'Die Seitenzahl muss eine ganze Zahl sein.',
            'page.min' => 'Die Seitenzahl muss mindestens 1 sein.',
        ];
    }

    /**
     * Get the validated search parameter.
     */
    public function getSearch(): ?string
    {
        return $this->validated('search');
    }

    /**
     * Get the validated type parameter.
     */
    public function getType(): ?string
    {
        return $this->validated('type');
    }

    /**
     * Get the validated categories parameter.
     *
     * @return array<string>
     */
    public function getCategories(): array
    {
        return $this->validated('categories', []);
    }
}
