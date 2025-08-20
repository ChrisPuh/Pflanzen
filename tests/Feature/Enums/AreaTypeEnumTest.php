<?php

declare(strict_types=1);

use App\Enums\Area\AreaTypeEnum;

it('has all expected enum cases', function (): void {
    $expectedCases = [
        'FlowerBed', 'VegetableBed', 'HerbBed', 'Lawn', 'Meadow',
        'Terrace', 'Patio', 'House', 'Greenhouse', 'Pool', 'Pond',
        'Shed', 'Compost', 'Pathway', 'Rockery', 'TreeArea',
        'Playground', 'Seating', 'Storage', 'Other',
    ];

    $actualCases = array_map(fn ($case) => $case->name, AreaTypeEnum::cases());

    expect($actualCases)->toBe($expectedCases);
});

it('provides correct German labels', function (): void {
    expect(AreaTypeEnum::FlowerBed->getLabel())->toBe('Blumenbeet')
        ->and(AreaTypeEnum::VegetableBed->getLabel())->toBe('Gemüsebeet')
        ->and(AreaTypeEnum::HerbBed->getLabel())->toBe('Kräuterbeet')
        ->and(AreaTypeEnum::Lawn->getLabel())->toBe('Rasen')
        ->and(AreaTypeEnum::Pool->getLabel())->toBe('Pool')
        ->and(AreaTypeEnum::Greenhouse->getLabel())->toBe('Gewächshaus');
});

it('provides correct descriptions', function (): void {
    expect(AreaTypeEnum::FlowerBed->description())->toBe('Ein Bereich mit Zierpflanzen und Blumen')
        ->and(AreaTypeEnum::VegetableBed->description())->toBe('Ein Bereich zum Anbau von Gemüse')
        ->and(AreaTypeEnum::Pool->description())->toBe('Ein Schwimmbecken');
});

it('categorizes types correctly', function (): void {
    expect(AreaTypeEnum::FlowerBed->category())->toBe('Pflanzbereich')
        ->and(AreaTypeEnum::VegetableBed->category())->toBe('Pflanzbereich')
        ->and(AreaTypeEnum::HerbBed->category())->toBe('Pflanzbereich')
        ->and(AreaTypeEnum::Lawn->category())->toBe('Grünfläche')
        ->and(AreaTypeEnum::Meadow->category())->toBe('Grünfläche')
        ->and(AreaTypeEnum::Terrace->category())->toBe('Aufenthaltsbereich')
        ->and(AreaTypeEnum::House->category())->toBe('Gebäude')
        ->and(AreaTypeEnum::Pool->category())->toBe('Wasserelement')
        ->and(AreaTypeEnum::TreeArea->category())->toBe('Gehölz')
        ->and(AreaTypeEnum::Playground->category())->toBe('Freizeit');
});

it('correctly identifies planting areas', function (): void {
    expect(AreaTypeEnum::FlowerBed->isPlantingArea())->toBeTrue()
        ->and(AreaTypeEnum::VegetableBed->isPlantingArea())->toBeTrue()
        ->and(AreaTypeEnum::HerbBed->isPlantingArea())->toBeTrue()
        ->and(AreaTypeEnum::Meadow->isPlantingArea())->toBeTrue()
        ->and(AreaTypeEnum::Rockery->isPlantingArea())->toBeTrue()
        ->and(AreaTypeEnum::TreeArea->isPlantingArea())->toBeTrue()
        ->and(AreaTypeEnum::Pool->isPlantingArea())->toBeFalse()
        ->and(AreaTypeEnum::House->isPlantingArea())->toBeFalse()
        ->and(AreaTypeEnum::Terrace->isPlantingArea())->toBeFalse();
});

it('correctly identifies water features', function (): void {
    expect(AreaTypeEnum::Pool->isWaterFeature())->toBeTrue()
        ->and(AreaTypeEnum::Pond->isWaterFeature())->toBeTrue()
        ->and(AreaTypeEnum::FlowerBed->isWaterFeature())->toBeFalse()
        ->and(AreaTypeEnum::House->isWaterFeature())->toBeFalse()
        ->and(AreaTypeEnum::Lawn->isWaterFeature())->toBeFalse();
});

it('correctly identifies buildings', function (): void {
    expect(AreaTypeEnum::House->isBuilding())->toBeTrue()
        ->and(AreaTypeEnum::Greenhouse->isBuilding())->toBeTrue()
        ->and(AreaTypeEnum::Shed->isBuilding())->toBeTrue()
        ->and(AreaTypeEnum::Storage->isBuilding())->toBeTrue()
        ->and(AreaTypeEnum::Pool->isBuilding())->toBeFalse()
        ->and(AreaTypeEnum::FlowerBed->isBuilding())->toBeFalse()
        ->and(AreaTypeEnum::Lawn->isBuilding())->toBeFalse();
});

it('provides options with all metadata', function (): void {
    $options = AreaTypeEnum::options();

    expect($options)->toBeInstanceOf(Illuminate\Support\Collection::class)
        ->and($options->count())->toBe(20);

    $firstOption = $options->first();

    expect($firstOption)->toHaveKeys(['value', 'label', 'description', 'category']);
});

it('has correct enum values', function (): void {
    expect(AreaTypeEnum::FlowerBed->value)->toBe('flower_bed')
        ->and(AreaTypeEnum::VegetableBed->value)->toBe('vegetable_bed')
        ->and(AreaTypeEnum::HerbBed->value)->toBe('herb_bed')
        ->and(AreaTypeEnum::Pool->value)->toBe('pool')
        ->and(AreaTypeEnum::Greenhouse->value)->toBe('greenhouse');
});

it('can get all enum values as array', function (): void {
    $values = AreaTypeEnum::values();

    expect($values)->toBeArray()
        ->and($values)->toContain('flower_bed')
        ->and($values)->toContain('vegetable_bed')
        ->and($values)->toContain('pool')
        ->and(count($values))->toBe(20);
});
