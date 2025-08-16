<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses(InteractsWithViews::class);

it('renders the dashboard view with page layout title and subtitle', function () {
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    $view = $this->view('dashboard');

    $view->assertSee(__('Dashboard'));
    $view->assertSee(__('Welcome to the dashboard'));
});
