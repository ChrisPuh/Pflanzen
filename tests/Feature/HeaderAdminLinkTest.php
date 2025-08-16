<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('shows admin panel link for admin users', function (): void {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    $response = $this->actingAs($admin)->get('/dashboard');
    
    $response->assertSee('Admin Panel');
    $response->assertSee('/admin');
});

it('hides admin panel link for regular users', function (): void {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    
    $user = User::factory()->create();
    $user->assignRole('user');
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertDontSee('Admin Panel');
});

it('hides admin panel link for users without roles', function (): void {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertDontSee('Admin Panel');
});
