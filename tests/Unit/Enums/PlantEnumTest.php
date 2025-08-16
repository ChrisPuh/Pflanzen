<?php

declare(strict_types=1);

use App\Enums\PlantCategoryEnum;
use App\Enums\PlantTypeEnum;

test('plant type enum has values method', function () {
    $values = PlantTypeEnum::values();

    expect($values)->toBeArray()
        ->and($values)->toContain('herb', 'flower', 'tree', 'shrub')
        ->and(count($values))->toBe(count(PlantTypeEnum::cases()));
});

test('plant category enum has values method', function () {
    $values = PlantCategoryEnum::values();

    expect($values)->toBeArray()
        ->and($values)->toContain('indoor', 'outdoor', 'medicinal', 'ornamental')
        ->and(count($values))->toBe(count(PlantCategoryEnum::cases()));
});

test('plant type enum has label method', function () {
    expect(PlantTypeEnum::Herb->getLabel())->toBe('Kräuter')
        ->and(PlantTypeEnum::Flower->getLabel())->toBe('Blumen')
        ->and(PlantTypeEnum::Tree->getLabel())->toBe('Bäume')
        ->and(PlantTypeEnum::Succulent->getLabel())->toBe('Sukkulenten')
        ->and(PlantTypeEnum::Aquatic->getLabel())->toBe('Wasserpflanzen');
});

test('plant category enum has label method', function () {
    expect(PlantCategoryEnum::Indoor->getLabel())->toBe('Zimmerpflanzen')
        ->and(PlantCategoryEnum::Outdoor->getLabel())->toBe('Gartenpflanzen')
        ->and(PlantCategoryEnum::Medicinal->getLabel())->toBe('Heilpflanzen')
        ->and(PlantCategoryEnum::Ornamental->getLabel())->toBe('Zierpflanzen')
        ->and(PlantCategoryEnum::Toxic->getLabel())->toBe('Giftige Pflanzen');
});
