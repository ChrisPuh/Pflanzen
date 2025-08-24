<?php

declare(strict_types=1);

namespace App\Http\Requests\Area;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class AttachPlantToAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $area = $this->route('area');

        Gate::authorize('update', $area);

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'plants' => ['required', 'array', 'min:1'],
            'plants.*.plant_id' => ['required', 'integer', 'exists:plants,id'],
            'plants.*.quantity' => ['required', 'integer', 'min:1', 'max:9999'],
            'plants.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'plants.required' => 'Bitte wählen Sie mindestens eine Pflanze aus.',
            'plants.array' => 'Ungültige Pflanzenauswahl.',
            'plants.min' => 'Bitte wählen Sie mindestens eine Pflanze aus.',
            'plants.*.plant_id.required' => 'Bitte wählen Sie eine Pflanze aus.',
            'plants.*.plant_id.integer' => 'Ungültige Pflanzen-ID.',
            'plants.*.plant_id.exists' => 'Die ausgewählte Pflanze existiert nicht.',
            'plants.*.quantity.required' => 'Bitte geben Sie eine Anzahl an.',
            'plants.*.quantity.integer' => 'Die Anzahl muss eine ganze Zahl sein.',
            'plants.*.quantity.min' => 'Die Anzahl muss mindestens 1 sein.',
            'plants.*.quantity.max' => 'Die Anzahl darf maximal 9999 sein.',
            'plants.*.notes.max' => 'Die Notizen dürfen maximal 500 Zeichen lang sein.',
        ];
    }
}
