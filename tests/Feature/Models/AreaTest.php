<?php

declare(strict_types=1);

use App\Enums\Area\AreaTypeEnum;
use App\Models\Area;
use App\Models\Garden;
use App\Models\Plant;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can create an area', function (): void {
    $area = Area::factory()->active()->create();

    expect($area)
        ->toBeInstanceOf(Area::class)
        ->and($area->garden)->toBeInstanceOf(Garden::class)
        ->and($area->type)->toBeInstanceOf(AreaTypeEnum::class)
        ->and($area->name)->toBeString()
        ->and($area->is_active)->toBeTrue();
});

it('can soft delete an area', function (): void {
    $area = Area::factory()->create();

    $area->delete();

    expect($area->deleted_at)->not->toBeNull()
        ->and(Area::count())->toBe(0)
        ->and(Area::withTrashed()->count())->toBe(1);
});

it('can restore a soft deleted area', function (): void {
    $area = Area::factory()->create();
    $area->delete();

    $area->restore();

    expect($area->deleted_at)->toBeNull()
        ->and(Area::count())->toBe(1);
});

it('belongs to a garden', function (): void {
    $garden = Garden::factory()->create();
    $area = Area::factory()->for($garden)->create();

    expect($area->garden)->toBeInstanceOf(Garden::class)
        ->and($area->garden->id)->toBe($garden->id);
});

it('can have plants', function (): void {
    $area = Area::factory()->create();
    $plants = Plant::factory(3)->create();

    $area->plants()->attach($plants, [
        'planted_at' => now(),
        'notes' => 'Test planting',
    ]);

    expect($area->plants)->toHaveCount(3)
        ->and($area->plants->first())->toBeInstanceOf(Plant::class);
});

it('can scope by active status', function (): void {
    Area::factory()->active()->count(3)->create();
    Area::factory()->inactive()->count(2)->create();

    $activeAreas = Area::active()->get();

    expect($activeAreas)->toHaveCount(3)
        ->and($activeAreas->every(fn (Area $area) => $area->is_active))->toBeTrue();
});

it('can scope by type', function (): void {
    Area::factory()->state(['type' => AreaTypeEnum::FlowerBed])->count(2)->create();
    Area::factory()->state(['type' => AreaTypeEnum::VegetableBed])->count(3)->create();

    $flowerBeds = Area::byType(AreaTypeEnum::FlowerBed)->get();

    expect($flowerBeds)->toHaveCount(2)
        ->and($flowerBeds->every(fn (Area $area) => $area->type === AreaTypeEnum::FlowerBed))->toBeTrue();
});

it('can scope by category', function (): void {
    Area::factory()->state(['type' => AreaTypeEnum::FlowerBed])->create();
    Area::factory()->state(['type' => AreaTypeEnum::VegetableBed])->create();
    Area::factory()->state(['type' => AreaTypeEnum::HerbBed])->create();
    Area::factory()->state(['type' => AreaTypeEnum::Pool])->create();

    $plantingAreas = Area::byCategory('Pflanzbereich')->get();

    expect($plantingAreas)->toHaveCount(3);
});

it('can scope for a specific garden', function (): void {
    $garden1 = Garden::factory()->create();
    $garden2 = Garden::factory()->create();

    Area::factory()->for($garden1)->count(3)->create();
    Area::factory()->for($garden2)->count(2)->create();

    $garden1Areas = Area::forGarden($garden1->id)->get();

    expect($garden1Areas)->toHaveCount(3)
        ->and($garden1Areas->every(fn (Area $area) => $area->garden_id === $garden1->id))->toBeTrue();
});

it('formats size correctly', function (): void {
    $area = Area::factory()->state(['size_sqm' => 25.75])->create();

    expect($area->formatted_size)->toBe('25,75 m²');
});

it('handles null size', function (): void {
    $area = Area::factory()->state(['size_sqm' => null])->create();

    expect($area->formatted_size)->toBe('Größe nicht angegeben');
});

it('can check if it has coordinates', function (): void {
    $areaWithCoordinates = Area::factory()->withCoordinates(10.5, 20.3)->create();
    $areaWithoutCoordinates = Area::factory()->state(['coordinates' => null])->create();

    expect($areaWithCoordinates->hasCoordinates())->toBeTrue()
        ->and($areaWithoutCoordinates->hasCoordinates())->toBeFalse();
});

it('can get coordinates', function (): void {
    $area = Area::factory()->withCoordinates(15.5, 25.7)->create();

    expect($area->getXCoordinate())->toBe(15.5)
        ->and($area->getYCoordinate())->toBe(25.7);
});

it('can set coordinates', function (): void {
    $area = Area::factory()->create();

    $area->setCoordinates(30.0, 40.0);

    expect($area->hasCoordinates())->toBeTrue()
        ->and($area->getXCoordinate())->toBe(30.0)
        ->and($area->getYCoordinate())->toBe(40.0);
});

it('can check if it has dimensions', function (): void {
    $areaWithDimensions = Area::factory()->state([
        'dimensions' => ['length' => 5.0, 'width' => 3.0],
    ])->create();
    $areaWithoutDimensions = Area::factory()->state(['dimensions' => null])->create();

    expect($areaWithDimensions->hasDimensions())->toBeTrue()
        ->and($areaWithoutDimensions->hasDimensions())->toBeFalse();
});

it('can get dimensions', function (): void {
    $area = Area::factory()->state([
        'dimensions' => ['length' => 10.5, 'width' => 8.2, 'height' => 2.5],
    ])->create();

    expect($area->getLength())->toBe(10.5)
        ->and($area->getWidth())->toBe(8.2)
        ->and($area->getHeight())->toBe(2.5);
});

it('can set dimensions', function (): void {
    $area = Area::factory()->create();

    $area->setDimensions(12.0, 8.0, 3.0);

    expect($area->hasDimensions())->toBeTrue()
        ->and($area->getLength())->toBe(12.0)
        ->and($area->getWidth())->toBe(8.0)
        ->and($area->getHeight())->toBe(3.0);
});

it('returns display color based on type category', function (): void {
    $plantingArea = Area::factory()->state([
        'type' => AreaTypeEnum::FlowerBed,
        'color' => null,
    ])->create();

    $waterFeature = Area::factory()->state([
        'type' => AreaTypeEnum::Pool,
        'color' => null,
    ])->create();

    expect($plantingArea->getDisplayColor())->toBe('#22c55e')
        ->and($waterFeature->getDisplayColor())->toBe('#3b82f6');
});

it('returns custom color when set', function (): void {
    $area = Area::factory()->withColor('#ff0000')->create();

    expect($area->getDisplayColor())->toBe('#ff0000');
});

it('can check if it is a planting area', function (): void {
    $plantingArea = Area::factory()->state(['type' => AreaTypeEnum::FlowerBed])->create();
    $nonPlantingArea = Area::factory()->state(['type' => AreaTypeEnum::Pool])->create();

    expect($plantingArea->isPlantingArea())->toBeTrue()
        ->and($nonPlantingArea->isPlantingArea())->toBeFalse();
});

it('can check if it is a water feature', function (): void {
    $waterFeature = Area::factory()->state(['type' => AreaTypeEnum::Pond])->create();
    $nonWaterFeature = Area::factory()->state(['type' => AreaTypeEnum::Lawn])->create();

    expect($waterFeature->isWaterFeature())->toBeTrue()
        ->and($nonWaterFeature->isWaterFeature())->toBeFalse();
});

it('can check if it is a building', function (): void {
    $building = Area::factory()->state(['type' => AreaTypeEnum::Greenhouse])->create();
    $nonBuilding = Area::factory()->state(['type' => AreaTypeEnum::Meadow])->create();

    expect($building->isBuilding())->toBeTrue()
        ->and($nonBuilding->isBuilding())->toBeFalse();
});

it('can get and set metadata values', function (): void {
    $area = Area::factory()->create();

    $area->setMetadataValue('irrigation', 'drip');
    $area->setMetadataValue('sun_exposure', 'full_sun');

    expect($area->getMetadataValue('irrigation'))->toBe('drip')
        ->and($area->getMetadataValue('sun_exposure'))->toBe('full_sun')
        ->and($area->getMetadataValue('non_existent', 'default'))->toBe('default');
});

it('can use factory states', function (): void {
    $plantingArea = Area::factory()->plantingArea()->create();
    $building = Area::factory()->building()->create();
    $waterFeature = Area::factory()->waterFeature()->create();

    expect($plantingArea->isPlantingArea())->toBeTrue()
        ->and($building->isBuilding())->toBeTrue()
        ->and($waterFeature->isWaterFeature())->toBeTrue();
});
