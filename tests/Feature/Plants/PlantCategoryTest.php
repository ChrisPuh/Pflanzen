<?php

declare(strict_types=1);

use App\Enums\PlantCategory as PlantCategoryEnum;
use App\Models\PlantCategory;

test('plant category can be created with factory', function () {
    $plantCategory = PlantCategory::factory()->create();

    expect($plantCategory)->toBeInstanceOf(PlantCategory::class)
        ->and($plantCategory->name)->toBeInstanceOf(PlantCategoryEnum::class);
});

test('plant category name field uses enum', function () {
    $plantCategory = PlantCategory::factory()->create(['name' => PlantCategoryEnum::Indoor]);

    expect($plantCategory->name)->toBe(PlantCategoryEnum::Indoor);
});

test('plant category has many-to-many relationship with plants', function () {
    $plantCategory = PlantCategory::factory()->create();

    expect($plantCategory->plants())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
});
