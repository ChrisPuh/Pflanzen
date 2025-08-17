<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects verified users to dashboard from notice page', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertRedirect('/dashboard');
});

it('shows verification notice for unverified users', function (): void {
    $user = User::factory()->create(['email_verified_at' => null]);

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertSuccessful()
        ->assertViewIs('auth.verify-email');
});

it('requires authentication for verification notice', function (): void {
    $response = $this->get('/verify-email');

    $response->assertRedirect('/login');
});
