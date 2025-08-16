<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('shows dashboard link in admin panel user menu for admin users', function (): void {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    $response = $this->actingAs($admin)->get('/admin');
    
    $response->assertSee('Dashboard');
    $response->assertSee('/dashboard');
});

it('denies access to admin panel for regular users', function (): void {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    
    $user = User::factory()->create();
    $user->assignRole('user');
    
    $response = $this->actingAs($user)->get('/admin');
    
    $response->assertForbidden();
});
