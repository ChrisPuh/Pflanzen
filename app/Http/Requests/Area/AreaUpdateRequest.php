<?php

declare(strict_types=1);

namespace App\Http\Requests\Area;

use App\Enums\Area\AreaTypeEnum;
use App\Models\Area;
use App\Models\Garden;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class AreaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $area = $this->route('area');

        if (! $area instanceof Area) {
            return false;
        }

        return $this->user()->can('update', $area);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'garden_id' => ['required', 'numeric', 'exists:gardens,id'],
            'type' => ['required', 'string', Rule::in(array_column(AreaTypeEnum::cases(), 'value'))],
            'size_sqm' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'coordinates_x' => ['nullable', 'numeric', 'min:-999999.99', 'max:999999.99'],
            'coordinates_y' => ['nullable', 'numeric', 'min:-999999.99', 'max:999999.99'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Der Name des Bereichs ist erforderlich.',
            'name.max' => 'Der Name darf maximal 255 Zeichen lang sein.',
            'description.max' => 'Die Beschreibung darf maximal 1000 Zeichen lang sein.',
            'garden_id.required' => 'Bitte wählen Sie einen Garten aus.',
            'garden_id.numeric' => 'Die Garten-ID muss eine Zahl sein.',
            'garden_id.exists' => 'Der ausgewählte Garten existiert nicht.',
            'type.required' => 'Bitte wählen Sie einen Bereichstyp aus.',
            'type.in' => 'Der ausgewählte Bereichstyp ist ungültig.',
            'size_sqm.numeric' => 'Die Größe muss eine Zahl sein.',
            'size_sqm.min' => 'Die Größe kann nicht negativ sein.',
            'size_sqm.max' => 'Die Größe ist zu groß.',
            'coordinates_x.numeric' => 'Die X-Koordinate muss eine Zahl sein.',
            'coordinates_y.numeric' => 'Die Y-Koordinate muss eine Zahl sein.',
            'color.regex' => 'Die Farbe muss im Hex-Format (#RRGGBB) angegeben werden.',
        ];
    }

    public function withValidator(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Contracts\Validation\Validator $validator): void {
            if ($this->filled('garden_id')) {
                $garden = Garden::find($this->integer('garden_id'));
                $area = $this->route('area');

                if ($garden && ! $this->user()->hasRole('admin') && $garden->user_id !== $this->user()->id) {
                    $validator->errors()->add('garden_id', 'Sie haben keine Berechtigung, diesem Garten Bereiche zuzuweisen.');
                }

                // Check if the area belongs to the same user as the new garden
                if ($area instanceof Area && $garden && ! $this->user()->hasRole('admin') && $area->garden->user_id !== $this->user()->id) {
                    $validator->errors()->add('area', 'Sie haben keine Berechtigung, diesen Bereich zu bearbeiten.');
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('color') && $this->input('color') === '') {
            $this->merge(['color' => null]);
        }
    }
}
