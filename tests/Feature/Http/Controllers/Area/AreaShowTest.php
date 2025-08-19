<?php

declare(strict_types=1);

use App\Models\Area;
use App\Models\Garden;
use App\Models\Plant;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->garden = Garden::factory()->create(['user_id' => $this->user->id]);
    $this->area = Area::factory()->create(['garden_id' => $this->garden->id]);
});

it('can display the show page', function (): void {
    $response = $this->actingAs($this->user)->get(route('areas.show', $this->area));

    $response->assertSuccessful()
        ->assertViewIs('areas.show')
        ->assertViewHas('area', $this->area);
});

it('shows area details correctly', function (): void {
    $response = $this->actingAs($this->user)->get(route('areas.show', $this->area));

    $response->assertSee($this->area->name)
        ->assertSee($this->area->type->getLabel())
        ->assertSee($this->garden->name);
});

it('shows plants in the area', function (): void {
    $plants = Plant::factory()->count(2)->create();
    $this->area->plants()->attach($plants);

    $response = $this->actingAs($this->user)->get(route('areas.show', $this->area));

    foreach ($plants as $plant) {
        $response->assertSee($plant->name);
    }
});

it('prevents access to other users areas', function (): void {
    $otherUser = User::factory()->create();
    $otherGarden = Garden::factory()->create(['user_id' => $otherUser->id]);
    $otherArea = Area::factory()->create(['garden_id' => $otherGarden->id]);

    $response = $this->actingAs($this->user)->get(route('areas.show', $otherArea));

    $response->assertForbidden();
});

it('requires authentication', function (): void {
    $response = $this->get(route('areas.show', $this->area));

    $response->assertRedirect(route('login'));
});
