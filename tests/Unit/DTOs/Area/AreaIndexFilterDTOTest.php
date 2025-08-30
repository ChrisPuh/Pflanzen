<?php

declare(strict_types=1);

use App\DTOs\Area\AreaIndexFilterDTO;
use App\Enums\Area\AreaTypeEnum;
use App\Http\Requests\Area\AreaIndexRequest;
use Illuminate\Foundation\Http\FormRequest;

describe('AreaIndexFilterDTO', function () {
    it('creates instance with all parameters', function () {
        $dto = new AreaIndexFilterDTO(
            search: 'test search',
            garden_id: 1,
            type: AreaTypeEnum::VegetableBed,
            category: 'outdoor',
            active: true
        );

        expect($dto->search)->toBe('test search')
            ->and($dto->garden_id)->toBe(1)
            ->and($dto->type)->toBe(AreaTypeEnum::VegetableBed)
            ->and($dto->category)->toBe('outdoor')
            ->and($dto->active)->toBeTrue();
    });

    it('creates instance with default null values', function () {
        $dto = new AreaIndexFilterDTO();

        expect($dto->search)->toBeNull()
            ->and($dto->garden_id)->toBeNull()
            ->and($dto->type)->toBeNull()
            ->and($dto->category)->toBeNull()
            ->and($dto->active)->toBeNull();
    });

    describe('fromRequest', function () {
        it('creates instance from request with all filled values', function () {
            $request = new AreaIndexRequest([
                'search' => 'my garden search',
                'garden_id' => '42',
                'type' => AreaTypeEnum::FlowerBed->value,
                'category' => 'indoor',
                'active' => '1',
            ]);

            $dto = AreaIndexFilterDTO::fromRequest($request);

            expect($dto->search)->toBe('my garden search')
                ->and($dto->garden_id)->toBe(42)
                ->and($dto->type)->toBe(AreaTypeEnum::FlowerBed)
                ->and($dto->category)->toBe('indoor')
                ->and($dto->active)->toBeTrue();
        });

        it('creates instance from request with empty values', function () {
            $request = new AreaIndexRequest([]);

            $dto = AreaIndexFilterDTO::fromRequest($request);

            expect($dto->search)->toBeNull()
                ->and($dto->garden_id)->toBeNull()
                ->and($dto->type)->toBeNull()
                ->and($dto->category)->toBeNull()
                ->and($dto->active)->toBeNull();
        });

        it('creates instance from request with active false', function () {
            $request = new AreaIndexRequest([
                'active' => '0',
            ]);

            $dto = AreaIndexFilterDTO::fromRequest($request);

            expect($dto->active)->toBeFalse();
        });

        it('handles invalid area type gracefully', function () {
            $request = new AreaIndexRequest([
                'type' => 'invalid_type',
            ]);

            $dto = AreaIndexFilterDTO::fromRequest($request);

            expect($dto->type)->toBeNull();
        });

        it('handles type casting correctly', function () {
            $request = new AreaIndexRequest([
                'search' => 123, // Will be cast to string
                'garden_id' => '456', // Will be cast to int
                'category' => 789, // Will be cast to string
                'active' => 1, // Will be cast to bool
            ]);

            $dto = AreaIndexFilterDTO::fromRequest($request);

            expect($dto->search)->toBe('123')
                ->and($dto->garden_id)->toBe(456)
                ->and($dto->category)->toBe('789')
                ->and($dto->active)->toBeTrue();
        });
    });

    describe('validation rules', function () {
        it('returns correct validation rules', function () {
            $rules = AreaIndexFilterDTO::rules();

            expect($rules)->toHaveKey('search')
                ->and($rules)->toHaveKey('garden_id')
                ->and($rules)->toHaveKey('type')
                ->and($rules)->toHaveKey('category')
                ->and($rules)->toHaveKey('active')
                ->and($rules)->toHaveKey('page');

            expect($rules['search'])->toContain('nullable')
                ->and($rules['search'])->toContain('string')
                ->and($rules['search'])->toContain('max:255');

            expect($rules['garden_id'])->toContain('nullable')
                ->and($rules['garden_id'])->toContain('integer')
                ->and($rules['garden_id'])->toContain('exists:gardens,id');

            expect($rules['active'])->toContain('nullable')
                ->and($rules['active'])->toContain('boolean');
        });
    });

    describe('validation messages', function () {
        it('returns correct validation messages in German', function () {
            $messages = AreaIndexFilterDTO::messages();

            expect($messages)->toHaveKey('search.string')
                ->and($messages)->toHaveKey('garden_id.integer')
                ->and($messages)->toHaveKey('type.enum')
                ->and($messages)->toHaveKey('active.boolean');

            expect($messages['search.string'])->toBe('Der Suchbegriff muss ein Text sein.')
                ->and($messages['garden_id.exists'])->toBe('Der ausgewählte Garten existiert nicht.')
                ->and($messages['type.enum'])->toBe('Der ausgewählte Bereichstyp ist ungültig.')
                ->and($messages['active.boolean'])->toBe('Der Aktiv-Status muss wahr oder falsch sein.');
        });
    });

    it('is readonly', function () {
        $dto = new AreaIndexFilterDTO();
        $reflection = new ReflectionClass($dto);

        expect($reflection->isReadOnly())->toBeTrue();
    });

    it('properties are readonly', function () {
        $dto = new AreaIndexFilterDTO();
        $reflection = new ReflectionClass($dto);

        foreach ($reflection->getProperties() as $property) {
            expect($property->isReadOnly())->toBeTrue();
        }
    });
});