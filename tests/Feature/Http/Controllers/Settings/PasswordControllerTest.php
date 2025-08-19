<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create([
        'password' => Hash::make('current-password'),
    ]);
});

it('can access password settings page', function (): void {
    $response = $this->actingAs($this->user)->get('/settings/password');

    $response->assertSuccessful()
        ->assertViewIs('settings.password')
        ->assertViewHas('user', $this->user);
});

it('can update password with valid current password', function (): void {
    $response = $this->actingAs($this->user)->put('/settings/password', [
        'current_password' => 'current-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertRedirect('/')
        ->assertSessionHas('status', 'password-updated');

    $this->user->refresh();
    expect(Hash::check('new-password', $this->user->password))->toBeTrue();
});

it('requires current password to update', function (): void {
    $response = $this->actingAs($this->user)->put('/settings/password', [
        'current_password' => 'wrong-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertSessionHasErrors(['current_password']);
});

it('requires password confirmation', function (): void {
    $response = $this->actingAs($this->user)->put('/settings/password', [
        'current_password' => 'current-password',
        'password' => 'new-password',
        'password_confirmation' => 'different-password',
    ]);

    $response->assertSessionHasErrors(['password']);
});

it('requires minimum password length', function (): void {
    $response = $this->actingAs($this->user)->put('/settings/password', [
        'current_password' => 'current-password',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors(['password']);
});

it('redirects guests to login page', function (): void {
    $response = $this->get('/settings/password');

    $response->assertRedirect('/login');
});
