<?php

declare(strict_types=1);

use App\Models\Plant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('allows access to valid plant show requests', function (): void {
    $plant = Plant::factory()->create();

    $response = $this->get(route('plants.show', $plant));

    $response->assertSuccessful();
});

it('requires authentication for plant show requests', function (): void {
    $plant = Plant::factory()->create();

    auth()->logout();

    $response = $this->get(route('plants.show', $plant));

    $response->assertRedirect(route('login'));
});

it('returns 404 for non-existent plants', function (): void {
    $response = $this->get(route('plants.show', 99999));

    $response->assertNotFound();
});

it('handles plant show request validation correctly', function (): void {
    $plant = Plant::factory()->create();

    // Test with various parameters that should be ignored
    $response = $this->get(route('plants.show', $plant).'?invalid=param&test=123');

    $response->assertSuccessful()
        ->assertViewIs('plants.show')
        ->assertViewHas('plant');
});
