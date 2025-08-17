<?php

declare(strict_types=1);

use App\Enums\PlantCategoryEnum;
use App\Enums\PlantTypeEnum;
use App\Models\Category;
use App\Models\Plant;
use App\Models\PlantType;
use App\Services\PlantService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->plantService = new PlantService();

    // Create plant types
    $this->herbType = PlantType::firstOrCreate(['name' => PlantTypeEnum::Herb], [
        'description' => 'Herb plants',
    ]);
    $this->flowerType = PlantType::firstOrCreate(['name' => PlantTypeEnum::Flower], [
        'description' => 'Flower plants',
    ]);

    // Create categories
    $this->outdoorCategory = Category::firstOrCreate(['name' => PlantCategoryEnum::Outdoor], [
        'description' => 'Outdoor plants',
    ]);
    $this->indoorCategory = Category::firstOrCreate(['name' => PlantCategoryEnum::Indoor], [
        'description' => 'Indoor plants',
    ]);
});

it('gets filtered plants without any filters', function (): void {
    Plant::factory()->count(5)->create([
        'plant_type_id' => $this->herbType->id,
    ]);

    $result = $this->plantService->getFilteredPlants();

    expect($result->total())->toBe(5)
        ->and($result->items())->toHaveCount(5);
});

it('filters plants by search term', function (): void {
    Plant::factory()->create([
        'name' => 'Test Plant',
        'plant_type_id' => $this->herbType->id,
    ]);
    Plant::factory()->create([
        'name' => 'Other Plant',
        'plant_type_id' => $this->herbType->id,
    ]);

    $result = $this->plantService->getFilteredPlants(search: 'Test');

    expect($result->total())->toBe(1)
        ->and($result->first()->name)->toBe('Test Plant');
});

it('filters plants by type', function (): void {
    Plant::factory()->create(['plant_type_id' => $this->herbType->id]);
    Plant::factory()->create(['plant_type_id' => $this->flowerType->id]);

    $result = $this->plantService->getFilteredPlants(type: PlantTypeEnum::Herb->value);

    expect($result->total())->toBe(1)
        ->and($result->first()->plant_type_id)->toBe($this->herbType->id);
});

it('filters plants by categories', function (): void {
    $plantWithOutdoor = Plant::factory()->create(['plant_type_id' => $this->herbType->id]);
    $plantWithoutCategory = Plant::factory()->create(['plant_type_id' => $this->herbType->id]);

    $plantWithOutdoor->categories()->attach($this->outdoorCategory->id);

    $result = $this->plantService->getFilteredPlants(categories: [PlantCategoryEnum::Outdoor->value]);

    expect($result->total())->toBe(1)
        ->and($result->first()->id)->toBe($plantWithOutdoor->id);
});

it('respects custom per page parameter', function (): void {
    Plant::factory()->count(15)->create(['plant_type_id' => $this->herbType->id]);

    $result = $this->plantService->getFilteredPlants(perPage: 5);

    expect($result->total())->toBe(15)
        ->and($result->perPage())->toBe(5)
        ->and($result->items())->toHaveCount(5);
});

it('gets plant statistics correctly', function (): void {
    // Create plants with different types
    Plant::factory()->count(3)->create(['plant_type_id' => $this->herbType->id]);
    Plant::factory()->count(2)->create(['plant_type_id' => $this->flowerType->id]);

    // Create plants with categories
    $plant1 = Plant::factory()->create(['plant_type_id' => $this->herbType->id]);
    $plant2 = Plant::factory()->create(['plant_type_id' => $this->herbType->id]);

    $plant1->categories()->attach($this->outdoorCategory->id);
    $plant2->categories()->attach([$this->outdoorCategory->id, $this->indoorCategory->id]);

    $stats = $this->plantService->getPlantStatistics();

    expect($stats)->toHaveKeys(['total', 'by_type', 'by_category'])
        ->and($stats['total'])->toBe(7)
        ->and($stats['by_type'])->toHaveKey(PlantTypeEnum::Herb->getLabel())
        ->and($stats['by_type'][PlantTypeEnum::Herb->getLabel()])->toBe(5)
        ->and($stats['by_type'][PlantTypeEnum::Flower->getLabel()])->toBe(2)
        ->and($stats['by_category'])->toHaveKey(PlantCategoryEnum::Outdoor->getLabel())
        ->and($stats['by_category'][PlantCategoryEnum::Outdoor->getLabel()])->toBe(2)
        ->and($stats['by_category'][PlantCategoryEnum::Indoor->getLabel()])->toBe(1);
});
