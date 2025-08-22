<?php

declare(strict_types=1);

use Livewire\Livewire;
use function Pest\Laravel\get;

describe('Cookie Banner', function () {
    it('shows cookie banner on welcome page', function () {
        $response = get('/');

        $response->assertStatus(200)
            ->assertSee('Cookies', false)
            ->assertSee('Datenschutz')
            ->assertSee('Wir verwenden nur technisch notwendige Cookies')
            ->assertSee('Alle akzeptieren')
            ->assertSee('Einstellungen');
    });

    it('shows cookie banner on about page', function () {
        $response = get('/about');

        $response->assertStatus(200)
            ->assertSee('Cookies', false)
            ->assertSee('Datenschutz')
            ->assertSee('Alle akzeptieren');
    });

    it('shows cookie banner on privacy page', function () {
        $response = get('/privacy');

        $response->assertStatus(200)
            ->assertSee('Cookies', false)
            ->assertSee('Datenschutz');
    });

    it('shows cookie banner on terms page', function () {
        $response = get('/terms');

        $response->assertStatus(200)
            ->assertSee('Cookies', false)
            ->assertSee('Datenschutz');
    });

    it('includes cookie banner component', function () {
        $response = get('/');

        $response->assertStatus(200)
            ->assertSeeLivewire('cookie-banner');
    });

    it('shows settings panel content when settings are toggled', function () {
        Livewire::test(\App\Livewire\CookieBanner::class)
            ->assertSet('showBanner', true)
            ->assertSet('showSettings', false)
            ->call('toggleSettings')
            ->assertSet('showSettings', true)
            ->assertSee('Cookie-Einstellungen')
            ->assertSee('Notwendige Cookies')
            ->assertSee('Session-Management')
            ->assertSee('Theme-Präferenz')
            ->assertSee('Nur notwendige');
    });

    it('shows link to privacy policy', function () {
        $response = get('/');

        $response->assertStatus(200)
            ->assertSee('href="' . route('privacy') . '"', false)
            ->assertSee('Mehr erfahren');
    });

    it('shows future cookie categories as disabled', function () {
        Livewire::test(\App\Livewire\CookieBanner::class)
            ->call('toggleSettings')
            ->assertSee('Analyse-Cookies')
            ->assertSee('Marketing-Cookies')
            ->assertSee('Aktuell nicht verwendet');
    });

    it('can accept all cookies and hide banner', function () {
        Livewire::test(\App\Livewire\CookieBanner::class)
            ->assertSet('showBanner', true)
            ->call('acceptAll')
            ->assertSet('showBanner', false);
    });

    it('can accept essential cookies only and hide banner', function () {
        Livewire::test(\App\Livewire\CookieBanner::class)
            ->assertSet('showBanner', true)
            ->call('acceptEssential')
            ->assertSet('showBanner', false);
    });
});