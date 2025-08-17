<?php

declare(strict_types=1);

use App\Enums\PlantCategoryEnum;
use App\Enums\PlantTypeEnum;
use App\Services\PlantService;

it('returns all available plant types', function (): void {
    $service = new PlantService();
    $types = $service->getAvailablePlantTypes();

    expect($types)->toBeArray()
        ->and($types)->toHaveCount(10) // All enum cases
        ->and($types[0])->toBeInstanceOf(PlantTypeEnum::class);
});

it('returns all available plant categories', function (): void {
    $service = new PlantService();
    $categories = $service->getAvailablePlantCategories();

    expect($categories)->toBeArray()
        ->and($categories)->toHaveCount(10) // All enum cases
        ->and($categories[0])->toBeInstanceOf(PlantCategoryEnum::class);
});
