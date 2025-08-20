<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

it('can access appearance settings page', function (): void {
    $response = $this->actingAs($this->user)->get('/settings/appearance');

    $response->assertSuccessful()
        ->assertViewIs('settings.appearance');
});

it('redirects guests to login page', function (): void {
    $response = $this->get('/settings/appearance');

    $response->assertRedirect('/login');
});
