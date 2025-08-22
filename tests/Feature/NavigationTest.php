<?php

declare(strict_types=1);

use function Pest\Laravel\get;

describe('Guest Navigation', function () {
    it('shows home link on about page', function () {
        $response = get('/about');

        $response->assertStatus(200)
            ->assertSee('Home');
    });

    it('shows about link on home page', function () {
        $response = get('/');

        $response->assertStatus(200)
            ->assertSee('Über uns');
    });

    it('can navigate from home to about', function () {
        $response = get('/');
        $response->assertStatus(200);

        $response = get('/about');
        $response->assertStatus(200)
            ->assertSee('Über ' . config('app.name'));
    });

    it('can navigate from about to home', function () {
        $response = get('/about');
        $response->assertStatus(200);

        $response = get('/');
        $response->assertStatus(200)
            ->assertSee('Willkommen bei');
    });

    it('logo links to home page', function () {
        $response = get('/about');

        $response->assertStatus(200)
            ->assertSee('href="' . route('home') . '"', false); // Logo should link to home
    });

    it('shows active indicator on home page', function () {
        $response = get('/');

        $response->assertStatus(200)
            ->assertSee('text-primary border-b-2 border-primary', false); // Home should be active
    });

    it('shows active indicator on about page', function () {
        $response = get('/about');

        $response->assertStatus(200)
            ->assertSee('text-primary border-b-2 border-primary', false); // About should be active
    });

    it('shows navigation next to logo on desktop', function () {
        $response = get('/');

        $response->assertStatus(200)
            ->assertSee('hidden md:flex items-center space-x-6', false); // Nav next to logo container
    });

    it('shows mobile navigation', function () {
        $response = get('/');

        $response->assertStatus(200)
            ->assertSee('md:hidden pb-3 pt-2', false); // Mobile nav container
    });
});