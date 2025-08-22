<?php

declare(strict_types=1);

use function Pest\Laravel\get;

describe('Legal Pages', function () {
    it('can display the privacy policy page', function () {
        $response = get('/privacy');

        $response->assertStatus(200)
            ->assertSee('Datenschutzerklärung')
            ->assertSee('Verantwortlicher')
            ->assertSee('Erfassung und Verwendung von Daten')
            ->assertSee('Cookies und Tracking');
    });

    it('can display the terms of service page', function () {
        $response = get('/terms');

        $response->assertStatus(200)
            ->assertSee('Allgemeine Geschäftsbedingungen')
            ->assertSee('Geltungsbereich')
            ->assertSee('Beschreibung der Dienste')
            ->assertSee('Nutzungsregeln');
    });

    it('shows legal links in footer on welcome page', function () {
        $response = get('/');

        $response->assertStatus(200)
            ->assertSee('Datenschutz')
            ->assertSee('AGB')
            ->assertSee('href="' . route('privacy') . '"', false)
            ->assertSee('href="' . route('terms') . '"', false);
    });

    it('shows legal links in footer on about page', function () {
        $response = get('/about');

        $response->assertStatus(200)
            ->assertSee('Datenschutz')
            ->assertSee('AGB')
            ->assertSee('href="' . route('privacy') . '"', false)
            ->assertSee('href="' . route('terms') . '"', false);
    });

    it('legal pages use guest layout', function () {
        $response = get('/privacy');
        $response->assertStatus(200)
            ->assertSee('Alle Rechte vorbehalten'); // Footer text from guest layout

        $response = get('/terms');
        $response->assertStatus(200)
            ->assertSee('Alle Rechte vorbehalten'); // Footer text from guest layout
    });

    it('privacy page shows current date', function () {
        $response = get('/privacy');

        $response->assertStatus(200)
            ->assertSee('Stand: ' . date('d.m.Y'));
    });

    it('terms page shows current date', function () {
        $response = get('/terms');

        $response->assertStatus(200)
            ->assertSee('Stand: ' . date('d.m.Y'));
    });
});