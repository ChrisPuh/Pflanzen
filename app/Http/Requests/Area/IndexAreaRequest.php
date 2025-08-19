<?php

declare(strict_types=1);

namespace App\Http\Requests\Area;

use App\Enums\Area\AreaTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class IndexAreaRequest extends FormRequest
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
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'garden_id' => ['nullable', 'integer', 'exists:gardens,id'],
            'type' => ['nullable', Rule::enum(AreaTypeEnum::class)],
            'category' => ['nullable', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'search.max' => 'Der Suchbegriff darf maximal 255 Zeichen lang sein.',
            'garden_id.integer' => 'Die Garten-ID muss eine Zahl sein.',
            'garden_id.exists' => 'Der ausgewählte Garten existiert nicht.',
            'category.max' => 'Die Kategorie darf maximal 255 Zeichen lang sein.',
            'page.integer' => 'Die Seitenzahl muss eine Zahl sein.',
            'page.min' => 'Die Seitenzahl muss mindestens 1 sein.',
        ];
    }
}
