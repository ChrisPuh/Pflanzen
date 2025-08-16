<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses(InteractsWithViews::class);

it('applies plant theme utilities on dashboard', function () {
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    $view = $this->view('dashboard');

    // Card surface + semantic color usage
    $view->assertSee('bg-surface-2', false)
        ->assertSee('border-default', false)
        ->assertSee('text-foreground', false)
        ->assertSee('text-primary', false)
        ->assertSee('bg-primary-soft', false);
});
