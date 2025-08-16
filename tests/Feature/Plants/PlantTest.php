<?php

declare(strict_types=1);

use App\Enums\PlantCategoryEnum;
use App\Enums\PlantTypeEnum;
use App\Models\Plant;
use App\Models\PlantCategory;
use App\Models\PlantType;

test('plant can be created with factory', function () {
    $plant = Plant::factory()->withCategories([
        PlantCategoryEnum::Indoor,
        PlantCategoryEnum::Medicinal,
    ])->create();

    expect($plant)->toBeInstanceOf(Plant::class)
        ->and($plant->name)->toBeString()
        ->and($plant->plantType)->toBeInstanceOf(PlantType::class)
        ->and($plant->plantCategories)->toBeInstanceOf(Illuminate\Database\Eloquent\Collection::class)
        ->and($plant->plantCategories->count())->toBe(2);
});

test('plant attributes are fillable', function () {
    $plantType = PlantType::firstOrCreate(['name' => PlantTypeEnum::Herb], PlantType::factory()->make(['name' => PlantTypeEnum::Herb])->toArray());
    $medicinalCategory = PlantCategory::firstOrCreate(['name' => PlantCategoryEnum::Medicinal], PlantCategory::factory()->make(['name' => PlantCategoryEnum::Medicinal])->toArray());
    $indoorCategory = PlantCategory::firstOrCreate(['name' => PlantCategoryEnum::Indoor], PlantCategory::factory()->make(['name' => PlantCategoryEnum::Indoor])->toArray());

    $data = [
        'name' => 'Test Plant',
        'latin_name' => 'Testus plantus',
        'description' => 'A test plant for testing purposes',
        'plant_type_id' => $plantType->id,
    ];

    $plant = Plant::create($data);
    $plant->plantCategories()->attach([$medicinalCategory->id, $indoorCategory->id]);

    expect($plant->name)->toBe('Test Plant')
        ->and($plant->latin_name)->toBe('Testus plantus')
        ->and($plant->description)->toBe('A test plant for testing purposes')
        ->and($plant->plantType->name)->toBe(PlantTypeEnum::Herb)
        ->and($plant->plantCategories->count())->toBe(2)
        ->and($plant->plantCategories->pluck('name')->toArray())->toContain(PlantCategoryEnum::Medicinal, PlantCategoryEnum::Indoor);
});

test('plant latin_name can be null', function () {
    $plant = Plant::factory()->create(['latin_name' => null]);

    expect($plant->latin_name)->toBeNull();
});

test('plant description can be null', function () {
    $plant = Plant::factory()->create(['description' => null]);

    expect($plant->description)->toBeNull();
});

test('plant has many-to-many category relationship', function () {
    $plant = Plant::factory()->withCategories([
        PlantCategoryEnum::Outdoor,
        PlantCategoryEnum::Aromatic,
    ])->create();

    expect($plant->plantType)->toBeInstanceOf(PlantType::class)
        ->and($plant->plantCategories)->toBeInstanceOf(Illuminate\Database\Eloquent\Collection::class)
        ->and($plant->plantType->name)->toBeInstanceOf(PlantTypeEnum::class)
        ->and($plant->plantCategories->count())->toBe(2);

    $plant->plantCategories->each(function ($category) {
        expect($category->name)->toBeInstanceOf(PlantCategoryEnum::class);
    });
});

test('plant type relationship works', function () {
    $plantType = PlantType::firstOrCreate(['name' => PlantTypeEnum::Flower], PlantType::factory()->make(['name' => PlantTypeEnum::Flower])->toArray());
    $plant = Plant::factory()->create(['plant_type_id' => $plantType->id]);

    expect($plant->plantType->name)->toBe(PlantTypeEnum::Flower);
});

test('plant can have multiple categories', function () {
    $indoorCategory = PlantCategory::query()->where('name', PlantCategoryEnum::Indoor)->first();
    $medicinalCategory = PlantCategory::query()->where('name', PlantCategoryEnum::Medicinal)->first();
    $aromaticCategory = PlantCategory::query()->where('name', PlantCategoryEnum::Aromatic)->first();

    $plant = Plant::factory()->create();

    $plant->plantCategories()->attach([
        $indoorCategory->id,
        $medicinalCategory->id,
        $aromaticCategory->id,
    ]);

    expect($plant->plantCategories->count())->toBe(3)
        ->and($plant->plantCategories->pluck('name')->toArray())->toContain(
            PlantCategoryEnum::Indoor,
            PlantCategoryEnum::Medicinal,
            PlantCategoryEnum::Aromatic
        );
});
