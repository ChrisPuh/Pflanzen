<?php

declare(strict_types=1);

use App\DTOs\Area\AreaFilterOptionsDTO;
use Illuminate\Support\Collection;

describe('AreaFilterOptionsDTO', function () {
    it('creates instance with all collections', function () {
        $gardens = collect([1 => 'Main Garden', 2 => 'Greenhouse']);
        $areaTypes = collect(['vegetable_bed' => 'Vegetable Bed', 'flower_bed' => 'Flower Bed']);
        $categories = collect(['outdoor' => 'Outdoor', 'indoor' => 'Indoor']);

        $dto = new AreaFilterOptionsDTO(
            gardens: $gardens,
            areaTypes: $areaTypes,
            categories: $categories
        );

        expect($dto->gardens)->toBe($gardens)
            ->and($dto->areaTypes)->toBe($areaTypes)
            ->and($dto->categories)->toBe($categories);
    });

    it('creates instance with empty collections', function () {
        $gardens = collect();
        $areaTypes = collect();
        $categories = collect();

        $dto = new AreaFilterOptionsDTO(
            gardens: $gardens,
            areaTypes: $areaTypes,
            categories: $categories
        );

        expect($dto->gardens)->toBeInstanceOf(Collection::class)
            ->and($dto->gardens)->toBeEmpty()
            ->and($dto->areaTypes)->toBeInstanceOf(Collection::class)
            ->and($dto->areaTypes)->toBeEmpty()
            ->and($dto->categories)->toBeInstanceOf(Collection::class)
            ->and($dto->categories)->toBeEmpty();
    });

    describe('getter methods', function () {
        beforeEach(function () {
            $this->gardens = collect([1 => 'Test Garden', 2 => 'Second Garden']);
            $this->areaTypes = collect(['herb_bed' => 'Herb Bed', 'vegetable_bed' => 'Vegetable Bed']);
            $this->categories = collect(['seasonal' => 'Seasonal', 'permanent' => 'Permanent']);

            $this->dto = new AreaFilterOptionsDTO(
                gardens: $this->gardens,
                areaTypes: $this->areaTypes,
                categories: $this->categories
            );
        });

        it('returns gardens collection', function () {
            expect($this->dto->getGardens())->toBe($this->gardens)
                ->and($this->dto->getGardens())->toBeInstanceOf(Collection::class);
        });

        it('returns area types collection', function () {
            expect($this->dto->getAreaTypes())->toBe($this->areaTypes)
                ->and($this->dto->getAreaTypes())->toBeInstanceOf(Collection::class);
        });

        it('returns categories collection', function () {
            expect($this->dto->getCategories())->toBe($this->categories)
                ->and($this->dto->getCategories())->toBeInstanceOf(Collection::class);
        });
    });

    describe('toArray', function () {
        it('converts all collections to arrays', function () {
            $gardens = collect([1 => 'Garden One', 2 => 'Garden Two']);
            $areaTypes = collect(['flower_bed' => 'Flower Bed', 'vegetable_bed' => 'Vegetable Bed']);
            $categories = collect(['outdoor' => 'Outdoor', 'protected' => 'Protected']);

            $dto = new AreaFilterOptionsDTO(
                gardens: $gardens,
                areaTypes: $areaTypes,
                categories: $categories
            );

            $result = $dto->toArray();

            expect($result)->toHaveKey('gardens')
                ->and($result)->toHaveKey('areaTypes')
                ->and($result)->toHaveKey('categories')
                ->and($result['gardens'])->toBe($gardens->toArray())
                ->and($result['areaTypes'])->toBe($areaTypes->toArray())
                ->and($result['categories'])->toBe($categories->toArray());
        });

        it('converts empty collections to empty arrays', function () {
            $dto = new AreaFilterOptionsDTO(
                gardens: collect(),
                areaTypes: collect(),
                categories: collect()
            );

            $result = $dto->toArray();

            expect($result)->toBe([
                'gardens' => [],
                'areaTypes' => [],
                'categories' => [],
            ]);
        });

        it('preserves array keys and values', function () {
            $gardens = collect(['garden_1' => 'First Garden', 'garden_2' => 'Second Garden']);
            $areaTypes = collect(['type_a' => 'Type A', 'type_b' => 'Type B']);
            $categories = collect(['cat_1' => 'Category 1', 'cat_2' => 'Category 2']);

            $dto = new AreaFilterOptionsDTO(
                gardens: $gardens,
                areaTypes: $areaTypes,
                categories: $categories
            );

            $result = $dto->toArray();

            expect($result['gardens'])->toBe(['garden_1' => 'First Garden', 'garden_2' => 'Second Garden'])
                ->and($result['areaTypes'])->toBe(['type_a' => 'Type A', 'type_b' => 'Type B'])
                ->and($result['categories'])->toBe(['cat_1' => 'Category 1', 'cat_2' => 'Category 2']);
        });

        it('returns correct array structure for view usage', function () {
            $dto = new AreaFilterOptionsDTO(
                gardens: collect([1 => 'Garden']),
                areaTypes: collect(['bed' => 'Bed']),
                categories: collect(['outdoor' => 'Outdoor'])
            );

            $result = $dto->toArray();

            // Verify the structure is appropriate for views
            expect($result)->toBeArray()
                ->and(array_keys($result))->toBe(['gardens', 'areaTypes', 'categories'])
                ->and($result['gardens'])->toBeArray()
                ->and($result['areaTypes'])->toBeArray()
                ->and($result['categories'])->toBeArray();
        });
    });

    describe('collection handling', function () {
        it('handles collections with mixed key types', function () {
            $gardens = collect([1 => 'Numeric Key', 'string_key' => 'String Key']);
            $areaTypes = collect(['mixed' => 'Mixed', 0 => 'Zero']);
            $categories = collect(['cat' => 'Category', 5 => 'Five']);

            $dto = new AreaFilterOptionsDTO(
                gardens: $gardens,
                areaTypes: $areaTypes,
                categories: $categories
            );

            expect($dto->getGardens())->toBe($gardens)
                ->and($dto->getAreaTypes())->toBe($areaTypes)
                ->and($dto->getCategories())->toBe($categories);

            $result = $dto->toArray();
            
            expect($result['gardens'])->toBe([1 => 'Numeric Key', 'string_key' => 'String Key'])
                ->and($result['areaTypes'])->toBe(['mixed' => 'Mixed', 0 => 'Zero'])
                ->and($result['categories'])->toBe(['cat' => 'Category', 5 => 'Five']);
        });

        it('handles collections with only values', function () {
            $gardens = collect(['Garden A', 'Garden B']);
            $areaTypes = collect(['Type X', 'Type Y']);
            $categories = collect(['Cat 1', 'Cat 2']);

            $dto = new AreaFilterOptionsDTO(
                gardens: $gardens,
                areaTypes: $areaTypes,
                categories: $categories
            );

            $result = $dto->toArray();

            expect($result['gardens'])->toBe(['Garden A', 'Garden B'])
                ->and($result['areaTypes'])->toBe(['Type X', 'Type Y'])
                ->and($result['categories'])->toBe(['Cat 1', 'Cat 2']);
        });
    });

    it('is readonly', function () {
        $dto = new AreaFilterOptionsDTO(
            gardens: collect(),
            areaTypes: collect(),
            categories: collect()
        );
        $reflection = new ReflectionClass($dto);

        expect($reflection->isReadOnly())->toBeTrue();
    });

    it('properties are readonly', function () {
        $dto = new AreaFilterOptionsDTO(
            gardens: collect(),
            areaTypes: collect(),
            categories: collect()
        );
        $reflection = new ReflectionClass($dto);

        foreach ($reflection->getProperties() as $property) {
            expect($property->isReadOnly())->toBeTrue();
        }
    });
});