<?php

declare(strict_types=1);

namespace App\Http\Requests\Area;

use App\Enums\Area\AreaTypeEnum;
use App\Models\Garden;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreAreaRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'garden_id' => ['required', 'integer', 'exists:gardens,id'],
            'type' => ['required', Rule::enum(AreaTypeEnum::class)],
            'size_sqm' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'coordinates_x' => ['nullable', 'numeric', 'min:-999999.99', 'max:999999.99'],
            'coordinates_y' => ['nullable', 'numeric', 'min:-999999.99', 'max:999999.99'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
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
            'garden_id.exists' => 'Der ausgewählte Garten existiert nicht.',
            'type.required' => 'Bitte wählen Sie einen Bereichstyp aus.',
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

                if ($garden && ! $this->user()->hasRole('admin') && $garden->user_id !== $this->user()->id) {
                    $validator->errors()->add('garden_id', 'Sie haben keine Berechtigung, Bereiche zu diesem Garten hinzuzufügen.');
                }
            }
        });
    }
}
