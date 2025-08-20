<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses(InteractsWithViews::class);

it('shows Settings folder with Profile, Password and Appearance links in the sidebar', function () {
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);

    $view = $this->view('dashboard');

    // Parent label
    $view->assertSee('Settings');

    // Children labels
    $view->assertSee('Profile');
    $view->assertSee('Password');
    $view->assertSee('Appearance');

    // Ensure the hrefs are present
    $view->assertSee(route('settings.profile.edit'), false);
    $view->assertSee(route('settings.password.edit'), false);
    $view->assertSee(route('settings.appearance.edit'), false);
});
