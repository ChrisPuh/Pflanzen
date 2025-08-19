<?php

declare(strict_types=1);

namespace App\Http\Requests\Garden;

use App\Enums\Garden\GardenTypeEnum;
use App\Models\Garden;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class GardenCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Garden::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'string', Rule::enum(GardenTypeEnum::class)],
            'size_sqm' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'location' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'coordinates' => ['nullable', 'array'],
            'coordinates.latitude' => ['required_with:coordinates', 'numeric', 'between:-90,90'],
            'coordinates.longitude' => ['required_with:coordinates', 'numeric', 'between:-180,180'],
            'is_active' => ['boolean'],
            'established_at' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Der Gartenname ist erforderlich.',
            'name.string' => 'Der Gartenname muss ein Text sein.',
            'name.max' => 'Der Gartenname darf nicht länger als 255 Zeichen sein.',
            'name.min' => 'Der Gartenname muss mindestens 2 Zeichen lang sein.',
            'description.string' => 'Die Beschreibung muss ein Text sein.',
            'description.max' => 'Die Beschreibung darf nicht länger als 1000 Zeichen sein.',
            'type.required' => 'Der Gartentyp ist erforderlich.',
            'type.enum' => 'Bitte wählen Sie einen gültigen Gartentyp.',
            'size_sqm.numeric' => 'Die Größe muss eine Zahl sein.',
            'size_sqm.min' => 'Die Größe kann nicht negativ sein.',
            'size_sqm.max' => 'Die Größe ist zu groß.',
            'location.string' => 'Der Standort muss ein Text sein.',
            'location.max' => 'Der Standort darf nicht länger als 255 Zeichen sein.',
            'city.string' => 'Die Stadt muss ein Text sein.',
            'city.max' => 'Die Stadt darf nicht länger als 100 Zeichen sein.',
            'postal_code.string' => 'Die Postleitzahl muss ein Text sein.',
            'postal_code.max' => 'Die Postleitzahl darf nicht länger als 10 Zeichen sein.',
            'coordinates.array' => 'Die Koordinaten müssen ein Array sein.',
            'coordinates.latitude.required_with' => 'Der Breitengrad ist erforderlich, wenn Koordinaten angegeben werden.',
            'coordinates.latitude.numeric' => 'Der Breitengrad muss eine Zahl sein.',
            'coordinates.latitude.between' => 'Der Breitengrad muss zwischen -90 und 90 liegen.',
            'coordinates.longitude.required_with' => 'Der Längengrad ist erforderlich, wenn Koordinaten angegeben werden.',
            'coordinates.longitude.numeric' => 'Der Längengrad muss eine Zahl sein.',
            'coordinates.longitude.between' => 'Der Längengrad muss zwischen -180 und 180 liegen.',
            'is_active.boolean' => 'Der Aktivitätsstatus muss wahr oder falsch sein.',
            'established_at.date' => 'Das Gründungsdatum muss ein gültiges Datum sein.',
            'established_at.before_or_equal' => 'Das Gründungsdatum kann nicht in der Zukunft liegen.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Gartenname',
            'description' => 'Beschreibung',
            'type' => 'Gartentyp',
            'size_sqm' => 'Größe in m²',
            'location' => 'Standort',
            'city' => 'Stadt',
            'postal_code' => 'Postleitzahl',
            'coordinates.latitude' => 'Breitengrad',
            'coordinates.longitude' => 'Längengrad',
            'is_active' => 'Aktiv',
            'established_at' => 'Gründungsdatum',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty coordinates to null
        if ($this->has('coordinates') && empty($this->input('coordinates.latitude')) && empty($this->input('coordinates.longitude'))) {
            $this->merge([
                'coordinates' => null,
            ]);
        }

        // Set default value for is_active if not provided
        if (! $this->has('is_active')) {
            $this->merge([
                'is_active' => true,
            ]);
        }
    }
}
