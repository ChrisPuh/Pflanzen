<?php

declare(strict_types=1);

use App\Enums\PlantCategoryEnum;
use App\Enums\PlantTypeEnum;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('accepts valid filter parameters', function (): void {
    $response = $this->get(route('plants.index', [
        'search' => 'test search',
        'type' => PlantTypeEnum::Herb->value,
        'categories' => [PlantCategoryEnum::Outdoor->value, PlantCategoryEnum::Edible->value],
        'page' => 2,
    ]));

    $response->assertSuccessful();
});

it('accepts empty parameters', function (): void {
    $response = $this->get(route('plants.index'));

    $response->assertSuccessful();
});

it('accepts null parameters', function (): void {
    $response = $this->get(route('plants.index', [
        'search' => null,
        'type' => null,
        'categories' => null,
        'page' => null,
    ]));

    $response->assertSuccessful();
});

it('rejects invalid plant type', function (): void {
    $response = $this->get(route('plants.index', [
        'type' => 'invalid_plant_type',
    ]));

    $response->assertSessionHasErrors('type');
});

it('rejects invalid category values', function (): void {
    $response = $this->get(route('plants.index', [
        'categories' => ['valid_category', 'invalid_category'],
    ]));

    $response->assertSessionHasErrors('categories.1');
});

it('rejects search parameter that is too long', function (): void {
    $longSearch = str_repeat('a', 256); // 256 characters, max is 255

    $response = $this->get(route('plants.index', [
        'search' => $longSearch,
    ]));

    $response->assertSessionHasErrors('search');
});

it('rejects non-integer page parameter', function (): void {
    $response = $this->get(route('plants.index', [
        'page' => 'not_a_number',
    ]));

    $response->assertSessionHasErrors('page');
});

it('rejects page parameter less than 1', function (): void {
    $response = $this->get(route('plants.index', [
        'page' => 0,
    ]));

    $response->assertSessionHasErrors('page');
});

it('accepts all valid plant types', function (PlantTypeEnum $plantType): void {
    $response = $this->get(route('plants.index', [
        'type' => $plantType->value,
    ]));

    $response->assertSuccessful();
})->with(fn (): array => array_map(
    fn (PlantTypeEnum $enum): array => [$enum],
    PlantTypeEnum::cases()
));

it('accepts all valid plant categories', function (PlantCategoryEnum $category): void {
    $response = $this->get(route('plants.index', [
        'categories' => [$category->value],
    ]));

    $response->assertSuccessful();
})->with(fn (): array => array_map(
    fn (PlantCategoryEnum $enum): array => [$enum],
    PlantCategoryEnum::cases()
));

it('accepts multiple valid categories', function (): void {
    $categories = [
        PlantCategoryEnum::Outdoor->value,
        PlantCategoryEnum::Indoor->value,
        PlantCategoryEnum::Edible->value,
    ];

    $response = $this->get(route('plants.index', [
        'categories' => $categories,
    ]));

    $response->assertSuccessful();
});

it('accepts categories as non-array and converts it', function (): void {
    $response = $this->get(route('plants.index', [
        'categories' => PlantCategoryEnum::Outdoor->value, // Single value, not array
    ]));

    $response->assertSessionHasErrors('categories');
});
