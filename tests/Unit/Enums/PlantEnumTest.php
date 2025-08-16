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
    expect(PlantType::Herb->label())->toBe('Kräuter')
        ->and(PlantType::Flower->label())->toBe('Blumen')
        ->and(PlantType::Tree->label())->toBe('Bäume')
        ->and(PlantType::Succulent->label())->toBe('Sukkulenten')
        ->and(PlantType::Aquatic->label())->toBe('Wasserpflanzen');
});

test('plant category enum has label method', function () {
    expect(PlantCategory::Indoor->label())->toBe('Zimmerpflanzen')
        ->and(PlantCategory::Outdoor->label())->toBe('Gartenpflanzen')
        ->and(PlantCategory::Medicinal->label())->toBe('Heilpflanzen')
        ->and(PlantCategory::Ornamental->label())->toBe('Zierpflanzen')
        ->and(PlantCategory::Toxic->label())->toBe('Giftige Pflanzen');
});
