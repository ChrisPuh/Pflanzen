<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses(InteractsWithViews::class);

it('applies plant theme on welcome view without relying on Vite', function () {
    // Render the welcome view directly
    $view = $this->view('welcome');

    // Body should use theme utilities
    $view->assertSee('bg-surface', false)
        ->assertSee('text-foreground', false);

    // Appearance script is present to handle system/light/dark without Vite
    $view->assertSee('setAppearance(', false);
});
