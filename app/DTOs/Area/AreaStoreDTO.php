<?php

declare(strict_types=1);

namespace App\DTOs\Area;

use App\DTOs\Shared\Contracts\WritableDTOInterface;
use App\Enums\Area\AreaTypeEnum;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use OpenSpout\Common\Exception\InvalidArgumentException;

final readonly class AreaStoreDTO implements WritableDTOInterface
{
    public function __construct(
        public string       $name,
        public int          $gardenId,
        public AreaTypeEnum $type,
        public bool         $isActive = true,

        public ?string      $description = null,
        public ?float       $sizeSqm = null,
        public ?array       $coordinates = null,
        public ?string      $color = null,
    )
    {
    }


    public static function fromValidatedRequest(array $validated): self
    {
        return new self(
            name: (string)$validated['name'],
            gardenId: (int)$validated['garden_id'],
            type: AreaTypeEnum::tryFrom($validated['type']),
            isActive: (bool)$validated['is_active'],

            description: isset($validated['description'])
                ? (string)$validated['description']
                : null,
            sizeSqm: isset($validated['size_sqm'])
                ? (float)$validated['size_sqm']
                : null,
            coordinates: self::prepareCoordinates($validated),
            color: isset($validated['color'])
                ? (string)$validated['color']
                : null,
        );
    }

    /**
     * Get the validation rules for the DTO.
     *
     * @return array{
     *     name: array{string, string, string},
     *     garden_id: array{string, string, string},
     *     type: array{string, string, Enum},
     *     is_active: array{string, string},
     *     description: array{string, string}|array{string},
     *     size_sqm: array{string, string, string, string}|array{string},
     *     coordinates_x: array{string, string, string, string}|array{string},
     *     coordinates_y: array{string, string, string, string}|array{string},
     *     color: array{string, string, string}|array{string}
     * }
     */
    public static function getValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'garden_id' => ['required', 'numeric', 'exists:gardens,id'],
            'type' => ['required', 'string', Rule::enum(AreaTypeEnum::class)],
            'is_active' => ['required', 'boolean'],

            'description' => ['nullable', 'string', 'max:1000'],
            'size_sqm' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'coordinates_x' => ['nullable', 'numeric', 'min:-999999.99', 'max:999999.99'],
            'coordinates_y' => ['nullable', 'numeric', 'min:-999999.99', 'max:999999.99'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    /**
     * Get the custom validation messages for the DTO.
     *
     * @return array{
     *     name: array{'required': string,'max': string},
     *     garden_id: array{'required': string,'numeric': string,'exists': string},
     *     type: array{'required': string,'in': string},
     *     is_active: array{'required': string,'boolean': string},
     *     description: array{'max': string}|array{},
     *     size_sqm: array{'numeric': string,'min': string,'max':string}|array{},
     *     coordinates_x: array{'numeric': string,'min': string,'max':string}|array{},
     *     coordinates_y: array{'numeric': string,'min': string,'max':string}|array{},
     *     color: array{'regex': string}|array{}
     * }
     */
    public static function getValidationMessages(): array
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

    /**
     * Convert the DTO to an associative array.
     *
     * @return array<string, mixed>
     */
    public function toModelData(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'garden_id' => $this->gardenId,
            'type' => $this->type,
            'size_sqm' => $this->sizeSqm,
            'coordinates' => $this->coordinates,
            'color' => $this->color,
            'is_active' => $this->isActive,
        ];
    }

    /**
     * Prepare coordinates array from request data.
     *
     * @param array<string, mixed> $data
     * @return array<string, float|int|null>|null
     */
    private static function prepareCoordinates(array $data): ?array
    {
        if (!isset($data['coordinates_x']) && !isset($data['coordinates_y'])) {
            return null;
        }

        return [
            'x' => $data['coordinates_x'] ?? null,
            'y' => $data['coordinates_y'] ?? null,
        ];
    }
}
