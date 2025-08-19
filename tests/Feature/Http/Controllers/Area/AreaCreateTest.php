<?php

declare(strict_types=1);

use App\Enums\Area\AreaTypeEnum;
use App\Models\Garden;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->garden = Garden::factory()->create(['user_id' => $this->user->id]);
});

it('can display the create form', function (): void {
    $response = $this->actingAs($this->user)->get(route('areas.create'));

    $response->assertSuccessful()
        ->assertViewIs('areas.create')
        ->assertViewHas(['userGardens', 'areaTypes', 'isAdmin']);
});

it('can create a new area with valid data', function (): void {
    $areaData = [
        'name' => 'Test Bereich',
        'description' => 'Ein Testbereich für Pflanzen',
        'garden_id' => $this->garden->id,
        'type' => AreaTypeEnum::FlowerBed->value,
        'size_sqm' => 25.5,
        'coordinates_x' => 12.5,
        'coordinates_y' => 8.2,
        'color' => '#10b981',
        'is_active' => true,
    ];

    $response = $this->actingAs($this->user)->post(route('areas.store'), $areaData);

    $response->assertRedirect(route('areas.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('areas', [
        'name' => 'Test Bereich',
        'garden_id' => $this->garden->id,
        'type' => AreaTypeEnum::FlowerBed->value,
    ]);
});

it('can create an area with minimal data', function (): void {
    $areaData = [
        'name' => 'Minimal Bereich',
        'garden_id' => $this->garden->id,
        'type' => AreaTypeEnum::VegetableBed->value,
        'is_active' => true,
    ];

    $response = $this->actingAs($this->user)->post(route('areas.store'), $areaData);

    $response->assertRedirect(route('areas.index'));

    $this->assertDatabaseHas('areas', [
        'name' => 'Minimal Bereich',
        'description' => null,
        'size_sqm' => null,
        'coordinates' => null,
        'color' => null,
    ]);
});

it('validates required fields', function (): void {
    $response = $this->actingAs($this->user)->post(route('areas.store'), []);

    $response->assertSessionHasErrors(['name', 'garden_id', 'type']);
});

it('validates name length', function (): void {
    $response = $this->actingAs($this->user)->post(route('areas.store'), [
        'name' => str_repeat('a', 256),
        'garden_id' => $this->garden->id,
        'type' => AreaTypeEnum::FlowerBed->value,
    ]);

    $response->assertSessionHasErrors(['name']);
});

it('validates garden exists', function (): void {
    $response = $this->actingAs($this->user)->post(route('areas.store'), [
        'name' => 'Test Bereich',
        'garden_id' => 99999,
        'type' => AreaTypeEnum::FlowerBed->value,
    ]);

    $response->assertSessionHasErrors(['garden_id']);
});

it('validates area type enum', function (): void {
    $response = $this->actingAs($this->user)->post(route('areas.store'), [
        'name' => 'Test Bereich',
        'garden_id' => $this->garden->id,
        'type' => 'invalid_type',
    ]);

    $response->assertSessionHasErrors(['type']);
});

it('validates numeric fields', function (): void {
    $response = $this->actingAs($this->user)->post(route('areas.store'), [
        'name' => 'Test Bereich',
        'garden_id' => $this->garden->id,
        'type' => AreaTypeEnum::FlowerBed->value,
        'size_sqm' => 'not_a_number',
        'coordinates_x' => 'not_a_number',
        'coordinates_y' => 'not_a_number',
    ]);

    $response->assertSessionHasErrors(['size_sqm', 'coordinates_x', 'coordinates_y']);
});

it('validates color format', function (): void {
    $response = $this->actingAs($this->user)->post(route('areas.store'), [
        'name' => 'Test Bereich',
        'garden_id' => $this->garden->id,
        'type' => AreaTypeEnum::FlowerBed->value,
        'color' => 'invalid_color',
    ]);

    $response->assertSessionHasErrors(['color']);
});

it('prevents creating areas in other users gardens', function (): void {
    $otherUser = User::factory()->create();
    $otherGarden = Garden::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($this->user)->post(route('areas.store'), [
        'name' => 'Test Bereich',
        'garden_id' => $otherGarden->id,
        'type' => AreaTypeEnum::FlowerBed->value,
    ]);

    $response->assertSessionHasErrors(['garden_id']);
});

it('can pre-select garden from url parameter', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('areas.create', ['garden_id' => $this->garden->id]));

    $response->assertSuccessful()
        ->assertViewHas('selectedGarden', $this->garden);
});

it('ignores invalid garden_id parameter', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('areas.create', ['garden_id' => 99999]));

    $response->assertSuccessful()
        ->assertViewHas('selectedGarden', null);
});

it('prevents preselecting other users gardens', function (): void {
    $otherUser = User::factory()->create();
    $otherGarden = Garden::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($this->user)
        ->get(route('areas.create', ['garden_id' => $otherGarden->id]));

    $response->assertSuccessful()
        ->assertViewHas('selectedGarden', null);
});

it('requires authentication', function (): void {
    $response = $this->get(route('areas.create'));
    $response->assertRedirect(route('login'));

    $response = $this->post(route('areas.store'), []);
    $response->assertRedirect(route('login'));
});
