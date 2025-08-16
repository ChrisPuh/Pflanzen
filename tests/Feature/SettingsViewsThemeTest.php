<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses(InteractsWithViews::class);

it('applies theme utilities on settings profile view', function () {
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    // Provide an empty error bag so Blade components that expect $errors won't fail
    $this->withViewErrors([]);

    $view = $this->view('settings.profile', ['user' => $user]);

    $view->assertSee('bg-surface-2', false)
        ->assertSee('border-default', false)
        ->assertSee('text-foreground', false)
        ->assertSee('text-primary', false)
        ->assertSee('text-muted', false);
});
