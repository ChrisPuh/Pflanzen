<?php

declare(strict_types=1);

use App\Enums\Area\AreaTypeEnum;
use App\Models\Area;
use App\Models\Garden;
use App\Models\User;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->garden = Garden::factory()->create(['user_id' => $this->user->id]);
    $this->area = Area::factory()->create([
        'garden_id' => $this->garden->id,
        'name' => 'Original Area',
        'type' => AreaTypeEnum::FlowerBed,
        'description' => 'Original description',
        'size_sqm' => 20.5,
        'coordinates' => ['x' => 10.0, 'y' => 5.0],
        'color' => '#ff5733',
        'is_active' => true,
    ]);
});

describe('GET /areas/{area}/edit', function () {
    it('can display the edit form', function (): void {
        $response = $this->actingAs($this->user)->get(route('areas.edit', $this->area));

        $response->assertSuccessful()
            ->assertViewIs('areas.edit')
            ->assertViewHas(['area', 'userGardens', 'areaTypes', 'isAdmin']);
    });

    it('denies access for non-owners', function (): void {
        $otherUser = User::factory()->create();
        $otherGarden = Garden::factory()->create(['user_id' => $otherUser->id]);
        $otherArea = Area::factory()->create(['garden_id' => $otherGarden->id]);

        $response = $this->actingAs($this->user)->get(route('areas.edit', $otherArea));

        $response->assertForbidden();
    });

    it('requires authentication', function (): void {
        $response = $this->get(route('areas.edit', $this->area));

        $response->assertRedirect(route('login'));
    });
});

describe('PUT /areas/{area}', function () {
    it('can update area with valid data', function (): void {
        $updateData = [
            'name' => 'Updated Area Name',
            'description' => 'Updated description',
            'garden_id' => $this->garden->id,
            'type' => AreaTypeEnum::VegetableBed->value,
            'size_sqm' => 30.0,
            'coordinates_x' => 15.0,
            'coordinates_y' => 10.0,
            'color' => '#10b981',
            'is_active' => false,
        ];

        $response = $this->actingAs($this->user)->put(route('areas.update', $this->area), $updateData);

        $response->assertRedirect(route('areas.show', $this->area))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('areas', [
            'id' => $this->area->id,
            'name' => 'Updated Area Name',
            'description' => 'Updated description',
            'type' => AreaTypeEnum::VegetableBed->value,
            'size_sqm' => 30.0,
            'color' => '#10b981',
            'is_active' => false,
        ]);

        // Check coordinates JSON
        $updatedArea = $this->area->fresh();
        expect($updatedArea->coordinates)->toEqual(['x' => 15.0, 'y' => 10.0]);
    });

    it('can update area with minimal data', function (): void {
        $updateData = [
            'name' => 'Minimal Updated Area',
            'garden_id' => $this->garden->id,
            'type' => AreaTypeEnum::Lawn->value,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->user)->put(route('areas.update', $this->area), $updateData);

        $response->assertRedirect(route('areas.show', $this->area))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('areas', [
            'id' => $this->area->id,
            'name' => 'Minimal Updated Area',
            'type' => AreaTypeEnum::Lawn->value,
            'description' => null,
            'size_sqm' => null,
            'color' => null,
        ]);
    });

    it('validates required fields', function (): void {
        $response = $this->actingAs($this->user)->put(route('areas.update', $this->area), []);

        $response->assertSessionHasErrors(['name', 'garden_id', 'type']);
    });

    it('validates name length', function (): void {
        $updateData = [
            'name' => str_repeat('a', 256), // Too long
            'garden_id' => $this->garden->id,
            'type' => AreaTypeEnum::FlowerBed->value,
        ];

        $response = $this->actingAs($this->user)->put(route('areas.update', $this->area), $updateData);

        $response->assertSessionHasErrors('name');
    });

    it('validates garden exists', function (): void {
        $updateData = [
            'name' => 'Test Area',
            'garden_id' => 99999, // Non-existent garden
            'type' => AreaTypeEnum::FlowerBed->value,
        ];

        $response = $this->actingAs($this->user)->put(route('areas.update', $this->area), $updateData);

        $response->assertSessionHasErrors('garden_id');
    });

    it('validates area type enum', function (): void {
        $updateData = [
            'name' => 'Test Area',
            'garden_id' => $this->garden->id,
            'type' => 'invalid_type',
        ];

        $response = $this->actingAs($this->user)->put(route('areas.update', $this->area), $updateData);

        $response->assertSessionHasErrors('type');
    });

    it('validates numeric fields', function (): void {
        $updateData = [
            'name' => 'Test Area',
            'garden_id' => $this->garden->id,
            'type' => AreaTypeEnum::FlowerBed->value,
            'size_sqm' => 'not-a-number',
            'coordinates_x' => 'not-a-number',
            'coordinates_y' => 'not-a-number',
        ];

        $response = $this->actingAs($this->user)->put(route('areas.update', $this->area), $updateData);

        $response->assertSessionHasErrors(['size_sqm', 'coordinates_x', 'coordinates_y']);
    });

    it('validates color format', function (): void {
        $updateData = [
            'name' => 'Test Area',
            'garden_id' => $this->garden->id,
            'type' => AreaTypeEnum::FlowerBed->value,
            'color' => 'invalid-color',
        ];

        $response = $this->actingAs($this->user)->put(route('areas.update', $this->area), $updateData);

        $response->assertSessionHasErrors('color');
    });

    it('prevents updating areas in other users gardens', function (): void {
        $otherUser = User::factory()->create();
        $otherGarden = Garden::factory()->create(['user_id' => $otherUser->id]);

        $updateData = [
            'name' => 'Test Area',
            'garden_id' => $otherGarden->id,
            'type' => AreaTypeEnum::FlowerBed->value,
        ];

        $response = $this->actingAs($this->user)->put(route('areas.update', $this->area), $updateData);

        $response->assertSessionHasErrors('garden_id');
    });

    it('denies access for non-owners', function (): void {
        $otherUser = User::factory()->create();
        $otherGarden = Garden::factory()->create(['user_id' => $otherUser->id]);
        $otherArea = Area::factory()->create(['garden_id' => $otherGarden->id]);

        $updateData = [
            'name' => 'Test Area',
            'garden_id' => $otherGarden->id,
            'type' => AreaTypeEnum::FlowerBed->value,
        ];

        $response = $this->actingAs($this->user)->put(route('areas.update', $otherArea), $updateData);

        $response->assertForbidden();
    });

    it('requires authentication', function (): void {
        $updateData = [
            'name' => 'Test Area',
            'garden_id' => $this->garden->id,
            'type' => AreaTypeEnum::FlowerBed->value,
        ];

        $response = $this->put(route('areas.update', $this->area), $updateData);

        $response->assertRedirect(route('login'));
    });

    it('can move area to different garden of same user', function (): void {
        $secondGarden = Garden::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'name' => 'Moved Area',
            'garden_id' => $secondGarden->id,
            'type' => AreaTypeEnum::FlowerBed->value,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->user)->put(route('areas.update', $this->area), $updateData);

        $response->assertRedirect(route('areas.show', $this->area))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('areas', [
            'id' => $this->area->id,
            'garden_id' => $secondGarden->id,
        ]);
    });
});
