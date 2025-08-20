<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows admin users to access panel', function (): void {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $panel = Filament::getDefaultPanel();

    expect($admin->canAccessPanel($panel))->toBeTrue();
});

it('denies regular users access to panel', function (): void {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);

    $user = User::factory()->create();
    $user->assignRole('user');

    $panel = Filament::getDefaultPanel();

    expect($user->canAccessPanel($panel))->toBeFalse();
});

it('denies users without roles access to panel', function (): void {
    $user = User::factory()->create();

    $panel = Filament::getDefaultPanel();

    expect($user->canAccessPanel($panel))->toBeFalse();
});
