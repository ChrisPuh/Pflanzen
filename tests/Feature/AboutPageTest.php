<?php

declare(strict_types=1);

use App\Models\Garden;
use App\Models\Plant;
use App\Models\User;

use function Pest\Laravel\get;

describe('About Page', function () {
    it('can display the about page', function () {
        $response = get('/about');

        $response->assertStatus(200)
            ->assertSee('Über '.config('app.name'))
            ->assertSee('in Zahlen')
            ->assertSee('Unsere Mission');
    });

    it('displays database statistics correctly', function () {
        $response = get('/about');

        $response->assertStatus(200)
            ->assertSee('Pflanzen verfügbar')
            ->assertSee('Verschiedene Pflanzentypen')
            ->assertSee('Kategorien')
            ->assertSee('Registrierte Benutzer')
            ->assertSee('Verwaltete Gärten')
            ->assertSee('Organisierte Bereiche');
    });

    it('shows actual database counts', function () {
        // Get current counts
        $plantsCount = Plant::count();
        $usersCount = User::count();
        $gardensCount = Garden::count();

        $response = get('/about');

        $response->assertStatus(200);

        // Check that numbers are displayed (they should be formatted with number_format)
        if ($plantsCount > 0) {
            $response->assertSee(number_format($plantsCount));
        }
        if ($usersCount > 0) {
            $response->assertSee(number_format($usersCount));
        }
        if ($gardensCount > 0) {
            $response->assertSee(number_format($gardensCount));
        }
    });

    it('uses the guest layout', function () {
        $response = get('/about');

        $response->assertStatus(200)
            ->assertSee('Alle Rechte vorbehalten'); // Footer text from guest layout
    });

    it('shows navigation link to about page', function () {
        $response = get('/');

        $response->assertStatus(200)
            ->assertSee('Über uns');
    });

    it('includes call to action buttons', function () {
        $response = get('/about');

        $response->assertStatus(200)
            ->assertSee('Jetzt mitmachen')
            ->assertSee('Zur Startseite');
    });
});
