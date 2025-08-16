<?php

declare(strict_types=1);

use App\Enums\PlantType as PlantTypeEnum;
use App\Models\PlantType;

test('plant type can be created with factory', function () {
    $plantType = PlantType::factory()->create();

    expect($plantType)->toBeInstanceOf(PlantType::class)
        ->and($plantType->name)->toBeInstanceOf(PlantTypeEnum::class);
})->skip('This test is skipped because it es not necessary. Types are seedet at migration.');

test('plant type name field uses enum', function () {
    $plantType = PlantType::firstOrCreate(['name' => PlantTypeEnum::Herb], PlantType::factory()->make(['name' => PlantTypeEnum::Herb])->toArray());

    expect($plantType->name)->toBe(PlantTypeEnum::Herb);
});

test('plant type has relationship with plants', function () {
    $plantType = PlantType::first();

    expect($plantType->plants())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
});
