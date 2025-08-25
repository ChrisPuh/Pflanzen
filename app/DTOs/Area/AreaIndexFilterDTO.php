<?php

declare(strict_types=1);

namespace App\DTOs\Area;

use App\Enums\Area\AreaTypeEnum;
use App\Http\Requests\Area\AreaIndexRequest;
use Illuminate\Validation\Rule;

final readonly class AreaIndexFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?int $garden_id = null,
        public ?AreaTypeEnum $type = null,
        public ?string $category = null,
        public ?bool $active = null,
    ) {}

    public static function fromRequest(AreaIndexRequest $request): self
    {
        return new self(
            search: $request->filled('search') ? $request->string('search')->toString() : null,
            garden_id: $request->filled('garden_id') ? $request->integer('garden_id') : null,
            type: $request->filled('type') ? AreaTypeEnum::tryFrom($request->string('type')->toString()) : null,
            category: $request->filled('category') ? $request->string('category')->toString() : null,
            active: $request->has('active') ? $request->boolean('active') : null,
        );
    }

    public static function rules(): array
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

    public static function messages(): array
    {
        return [
            'search.string' => 'Der Suchbegriff muss ein Text sein.',
            'search.max' => 'Der Suchbegriff darf maximal :max Zeichen lang sein.',
            'garden_id.integer' => 'Die Garten-ID muss eine Zahl sein.',
            'garden_id.exists' => 'Der ausgewählte Garten existiert nicht.',
            'type.enum' => 'Der ausgewählte Bereichstyp ist ungültig.',
            'category.string' => 'Die Kategorie muss ein Text sein.',
            'category.max' => 'Die Kategorie darf maximal :max Zeichen lang sein.',
            'active.boolean' => 'Der Aktiv-Status muss wahr oder falsch sein.',
            'page.integer' => 'Die Seitenzahl muss eine Zahl sein.',
            'page.min' => 'Die Seitenzahl muss mindestens :min sein.',
        ];
    }
}
