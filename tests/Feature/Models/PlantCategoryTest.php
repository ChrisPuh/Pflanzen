<?php

declare(strict_types=1);

use App\Enums\PlantCategoryEnum;
use App\Models\Category;

test('category can be created with factory', function () {
    $category = Category::first();

    expect($category)->toBeInstanceOf(Category::class)
        ->and($category->name)->toBeInstanceOf(PlantCategoryEnum::class);
});

test('category name field uses enum', function () {
    $category = Category::firstOrCreate(['name' => PlantCategoryEnum::Indoor], Category::factory()->make(['name' => PlantCategoryEnum::Indoor])->toArray());

    expect($category->name)->toBe(PlantCategoryEnum::Indoor);
});

test('category has many-to-many relationship with plants', function () {
    $category = Category::first();

    expect($category->plants())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
});
