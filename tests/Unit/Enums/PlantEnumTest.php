<?php

declare(strict_types=1);

use App\Enums\PlantCategory;
use App\Enums\PlantType;

test('plant type enum has values method', function () {
    $values = PlantType::values();

    expect($values)->toBeArray()
        ->and($values)->toContain('herb', 'flower', 'tree', 'shrub')
        ->and(count($values))->toBe(count(PlantType::cases()));
});

test('plant category enum has values method', function () {
    $values = PlantCategory::values();

    expect($values)->toBeArray()
        ->and($values)->toContain('indoor', 'outdoor', 'medicinal', 'ornamental')
        ->and(count($values))->toBe(count(PlantCategory::cases()));
});

test('plant type enum has label method', function () {
    expect(PlantType::Herb->getLabel())->toBe('Kräuter')
        ->and(PlantType::Flower->getLabel())->toBe('Blumen')
        ->and(PlantType::Tree->getLabel())->toBe('Bäume')
        ->and(PlantType::Succulent->getLabel())->toBe('Sukkulenten')
        ->and(PlantType::Aquatic->getLabel())->toBe('Wasserpflanzen');
});

test('plant category enum has label method', function () {
    expect(PlantCategory::Indoor->getLabel())->toBe('Zimmerpflanzen')
        ->and(PlantCategory::Outdoor->getLabel())->toBe('Gartenpflanzen')
        ->and(PlantCategory::Medicinal->getLabel())->toBe('Heilpflanzen')
        ->and(PlantCategory::Ornamental->getLabel())->toBe('Zierpflanzen')
        ->and(PlantCategory::Toxic->getLabel())->toBe('Giftige Pflanzen');
});
