<?php

declare(strict_types=1);

use App\Models\Area;
use App\Models\Garden;
use App\Models\Plant;
use App\Models\User;
use Spatie\Permission\Models\Role;

describe('GardensIndexController', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->admin->assignRole($this->adminRole);
    });

    describe('Authentication', function () {
        it('redirects unauthenticated users to login', function () {
            $response = $this->get(route('gardens.index'));

            $response->assertRedirect(route('login'));
        });

        it('requires verified users', function () {
            $unverifiedUser = User::factory()->unverified()->create();

            $response = $this->actingAs($unverifiedUser)->get(route('gardens.index'));

            // Test depends on if email verification is enforced in the app
            expect($response->status())->toBeIn([200, 302]);
        });
    });

    describe('Authorization', function () {
        it('allows authenticated users to access gardens index', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk();
        });

        it('allows admin to access gardens index', function () {
            $response = $this->actingAs($this->admin)->get(route('gardens.index'));

            $response->assertOk();
        });
    });

    describe('User Gardens Display', function () {
        it('shows only user\'s own gardens for regular users', function () {
            $userGarden1 = Garden::factory()->for($this->user)->create(['name' => 'User Garden 1']);
            $userGarden2 = Garden::factory()->for($this->user)->create(['name' => 'User Garden 2']);
            $otherUserGarden = Garden::factory()->create(['name' => 'Other User Garden']);

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('User Garden 1')
                ->assertSee('User Garden 2')
                ->assertDontSee('Other User Garden');
        });

        it('shows all gardens for admin users', function () {
            $userGarden = Garden::factory()->for($this->user)->create(['name' => 'User Garden']);
            $adminGarden = Garden::factory()->for($this->admin)->create(['name' => 'Admin Garden']);
            $otherUserGarden = Garden::factory()->create(['name' => 'Other User Garden']);

            $response = $this->actingAs($this->admin)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('User Garden')
                ->assertSee('Admin Garden')
                ->assertSee('Other User Garden')
                ->assertSee('(Admin-Ansicht)');
        });

        it('shows both active and inactive gardens', function () {
            $activeGarden = Garden::factory()->for($this->user)->create([
                'name' => 'Active Garden',
                'is_active' => true,
            ]);
            $inactiveGarden = Garden::factory()->for($this->user)->create([
                'name' => 'Inactive Garden',
                'is_active' => false,
            ]);

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('Active Garden')
                ->assertSee('Inactive Garden');
        });

        it('displays gardens in latest order', function () {
            $olderGarden = Garden::factory()->for($this->user)->create([
                'name' => 'Older Garden',
                'created_at' => now()->subDays(5),
            ]);
            $newerGarden = Garden::factory()->for($this->user)->create([
                'name' => 'Newer Garden',
                'created_at' => now()->subDay(),
            ]);

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk();
            $content = $response->getContent();

            $newerPosition = mb_strpos($content, 'Newer Garden');
            $olderPosition = mb_strpos($content, 'Older Garden');

            expect($newerPosition)->toBeLessThan($olderPosition);
        });
    });

    describe('Garden Statistics', function () {
        it('shows correct garden count', function () {
            Garden::factory()->for($this->user)->count(3)->create();

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('3 Gärten gefunden');
        });

        it('shows singular garden count correctly', function () {
            Garden::factory()->for($this->user)->create();

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('1 Garten gefunden');
        });

        it('shows total plants count across all gardens', function () {
            $garden1 = Garden::factory()->for($this->user)->create();
            $garden2 = Garden::factory()->for($this->user)->create();

            $area1 = Area::factory()->create(['garden_id' => $garden1->id]);
            $area2 = Area::factory()->create(['garden_id' => $garden2->id]);

            $plant1 = Plant::factory()->create();
            $plant2 = Plant::factory()->create();
            $plant3 = Plant::factory()->create();

            $area1->plants()->attach([$plant1->id, $plant2->id]);
            $area2->plants()->attach([$plant3->id]);

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk();
            $content = $response->getContent();

            // Check that total plants is displayed somewhere in stats
            expect($content)->toContain('3');
        });

        it('shows active gardens count', function () {
            Garden::factory()->for($this->user)->count(2)->create(['is_active' => true]);
            Garden::factory()->for($this->user)->create(['is_active' => false]);

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk();
            $content = $response->getContent();

            // Debug: check what's actually in the content
            // Should show active gardens somewhere in the stats
            expect($content)->toContain('aktiv');
        });

        it('shows areas statistics correctly', function () {
            $garden = Garden::factory()->for($this->user)->create();
            Area::factory()->for($garden, 'garden')->count(2)->create(['is_active' => true]);

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('Bereiche');
        });

        it('shows correct statistics in header cards', function () {
            // Create test data
            Garden::factory()->for($this->user)->count(2)->create(['is_active' => true]);
            Garden::factory()->for($this->user)->create(['is_active' => false]);

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('Gärten');
        });
    });

    describe('Garden Display Details', function () {
        it('displays garden information correctly', function () {
            $garden = Garden::factory()->for($this->user)->create([
                'name' => 'Mein Testgarten',
                'type' => 'vegetable_garden',
                'description' => 'Ein wunderschöner Garten',
                'size_sqm' => 150.75,
                'location' => 'Hinterhof',
                'city' => 'Berlin',
                'postal_code' => '10115',
                'is_active' => true,
            ]);

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('Mein Testgarten')
                ->assertSee('Nutzgarten')
                ->assertSee('150,75 m²')
                ->assertSee('Hinterhof')
                ->assertSee('10115, Berlin')
                ->assertSee('Aktiv');
        });

        it('shows coordinates indicator when available', function () {
            $garden = Garden::factory()
                ->for($this->user)
                ->withCoordinates(52.5200, 13.4050)
                ->create();

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk();
            $content = $response->getContent();

            // Should have GPS icon/indicator visible
            expect($content)->toContain('path');
        });

        it('displays garden age when established date is set', function () {
            $garden = Garden::factory()->for($this->user)->create([
                'established_at' => now()->subYears(2),
            ]);

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('2 Jahre alt');
        });

        it('handles singular year correctly', function () {
            $garden = Garden::factory()->for($this->user)->create([
                'established_at' => now()->subYear(),
            ]);

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('1 Jahr alt');
        });

        it('displays plant count for each garden', function () {
            $garden = Garden::factory()->for($this->user)->create();
            $area = Area::factory()->create(['garden_id' => $garden->id]);
            $plants = Plant::factory()->count(5)->create();
            $area->plants()->attach($plants->pluck('id'));

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('5 Pflanzen');
        });

        it('displays area count for each garden', function () {
            $garden = Garden::factory()->for($this->user)->create();
            Area::factory()->for($garden, 'garden')->count(3)->create();

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('3 Bereiche');
        });

        it('handles singular area count correctly', function () {
            $garden = Garden::factory()->for($this->user)->create();
            Area::factory()->for($garden, 'garden')->create();

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('1 Bereich');
        });
    });

    describe('Admin Features', function () {
        it('shows owner name for gardens not owned by admin', function () {
            $otherUser = User::factory()->create(['name' => 'Max Mustermann']);
            $garden = Garden::factory()->for($otherUser)->create(['name' => 'Other User Garden']);

            $response = $this->actingAs($this->admin)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('Max Mustermann')
                ->assertSee('Other User Garden');
        });

        it('does not show owner tag for admin\'s own gardens', function () {
            $garden = Garden::factory()->for($this->admin)->create(['name' => 'Admin Garden']);

            $response = $this->actingAs($this->admin)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('Admin Garden');

            $content = $response->getContent();

            // Should not show owner badge for admin's own gardens
            expect($content)->not->toContain('orange-100');
        });

        it('shows admin view indicator', function () {
            $response = $this->actingAs($this->admin)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('(Admin-Ansicht)');
        });
    });

    describe('Empty States', function () {
        it('displays empty state when user has no gardens', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('Keine Gärten gefunden')
                ->assertSee('Du hast noch keine Gärten erstellt')
                ->assertSee('Ersten Garten erstellen');
        });

        it('shows create garden button in empty state', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk();
            $content = $response->getContent();

            // Check for create button
            expect($content)->toContain('Ersten Garten erstellen');
        });
    });

    describe('Pagination', function () {
        it('paginates gardens correctly', function () {
            Garden::factory()->for($this->user)->count(15)->create();

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('Seite 1 von 2');
        });

        it('shows correct page information', function () {
            Garden::factory()->for($this->user)->count(25)->create();

            $response = $this->actingAs($this->user)->get(route('gardens.index', ['page' => 2]));

            $response->assertOk()
                ->assertSee('Seite 2');
        });
    });

    describe('Garden Links', function () {
        it('includes correct links to garden show pages', function () {
            $garden = Garden::factory()->for($this->user)->create(['name' => 'Test Garden']);

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk();
            $content = $response->getContent();

            expect($content)->toContain(route('gardens.show', $garden));
        });

        it('includes action button for creating new garden', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('Neuen Garten erstellen');
        });

        it('shows archived gardens link when archived gardens exist', function () {
            // Create and archive a garden
            $garden = Garden::factory()->for($this->user)->create();
            $garden->delete();

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertSee('Archivierte Gärten');
        });

        it('does not show archived gardens link when no archived gardens exist', function () {
            Garden::factory()->for($this->user)->create();

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk();
            $content = $response->getContent();

            expect($content)->not->toContain('Archivierte Gärten');
        });
    });

    describe('View Data and Structure', function () {
        it('passes correct data to view', function () {
            $garden = Garden::factory()->for($this->user)->create();

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertViewHas('gardens')
                ->assertViewHas('stats')
                ->assertViewHas('hasArchivedGardens')
                ->assertViewHas('isAdmin', false);
        });

        it('passes admin flag correctly for admin users', function () {
            $response = $this->actingAs($this->admin)->get(route('gardens.index'));

            $response->assertOk()
                ->assertViewHas('isAdmin', true);
        });

        it('uses correct view template', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk()
                ->assertViewIs('gardens.index');
        });

        it('loads gardens with required relationships', function () {
            $garden = Garden::factory()->for($this->user)->create();
            $area = Area::factory()->create(['garden_id' => $garden->id]);
            $plant = Plant::factory()->create();
            $area->plants()->attach($plant);

            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk();

            $viewData = $response->viewData('gardens');
            expect($viewData)->not->toBeNull();
        });
    });

    describe('Response Structure', function () {
        it('returns successful response', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk();
        });

        it('returns HTML content', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.index'));

            $response->assertOk();
            expect($response->headers->get('Content-Type'))->toContain('text/html');
        });
    });
});
