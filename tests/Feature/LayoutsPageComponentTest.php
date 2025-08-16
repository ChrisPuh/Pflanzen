<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses(InteractsWithViews::class);

it('renders title, subtitle, event url and actions', function () {
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);
    $title = 'Pflanzen';
    $subtitle = 'Verwalte deine Pflanzen';
    $eventUrl = 'https://example.com/event';

    $view = $this->blade('<x-layouts.page :title="$title" :subtitle="$subtitle" :event-url="$eventUrl">
        <x-slot:actions>
            <button type="button">Aktion</button>
        </x-slot:actions>
        <div>Inhalt</div>
    </x-layouts.page>', compact('title', 'subtitle', 'eventUrl'));

    $view->assertSee($title)
        ->assertSee($subtitle)
        ->assertSee($eventUrl)
        ->assertSee('Aktion')
        ->assertSee('Inhalt');
});
