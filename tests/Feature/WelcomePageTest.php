<?php

declare(strict_types=1);

use function Pest\Laravel\get;

describe('Welcome Page', function () {
    it('can display the welcome page', function () {
        $response = get('/');

        $response->assertStatus(200)
            ->assertSee('Willkommen bei')
            ->assertSee('Pflanzen')
            ->assertSee('Jetzt starten')
            ->assertSee('Anmelden');
    });

    it('shows registration and login links for guests', function () {
        $response = get('/');

        $response->assertStatus(200)
            ->assertSee('Registrieren')
            ->assertSee('Anmelden');
    });

    it('uses the guest layout', function () {
        $response = get('/');

        $response->assertStatus(200)
            ->assertSee('Alle Rechte vorbehalten'); // Footer text from guest layout
    });
});