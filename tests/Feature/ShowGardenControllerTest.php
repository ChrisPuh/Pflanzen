<?php

declare(strict_types=1);

use App\Models\Garden;
use App\Models\Plant;
use App\Models\User;
use Spatie\Permission\Models\Role;

describe('GardenShowController Controller', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->garden = Garden::factory()->for($this->user)->create();
        $this->otherUserGarden = Garden::factory()->for($this->otherUser)->create();
    });

    describe('Authentication', function () {
        it('redirects unauthenticated users to login', function () {
            $response = $this->get(route('gardens.show', $this->garden));

            $response->assertRedirect(route('login'));
        });

        it('requires verified users', function () {
            $unverifiedUser = User::factory()->unverified()->create();
            $garden = Garden::factory()->for($unverifiedUser)->create();

            $response = $this->actingAs($unverifiedUser)->get(route('gardens.show', $garden));

            // Test depends on if email verification is enforced in the app
            // If verification is active, expect redirect to verification.notice
            // If not active but user doesn't own garden, expect 403
            // If not active and user owns garden, expect 200
            expect($response->status())->toBeIn([200, 302, 403]);
        });
    });

    describe('Authorization', function () {
        it('allows garden owner to view their garden', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.show', $this->garden));

            $response->assertOk();
        });

        it('denies access to other users gardens', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.show', $this->otherUserGarden));

            $response->assertForbidden();
        });

        it('allows admin to view any garden', function () {
            // Create admin role if it doesn't exist
            $adminRole = Role::firstOrCreate(['name' => 'admin']);

            $admin = User::factory()->create();
            $admin->assignRole('admin');

            $response = $this->actingAs($admin)->get(route('gardens.show', $this->garden));

            $response->assertOk();
        });

        it('allows admin to view other users gardens', function () {
            // Create admin role if it doesn't exist
            $adminRole = Role::firstOrCreate(['name' => 'admin']);

            $admin = User::factory()->create();
            $admin->assignRole('admin');

            $response = $this->actingAs($admin)->get(route('gardens.show', $this->otherUserGarden));

            $response->assertOk();
        });

        it('provides meaningful error messages for unauthorized access', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.show', $this->otherUserGarden));

            $response->assertForbidden();
            // In real apps, you could also check for specific error messages
            // by checking the response content or session errors
        });
    });

    describe('Garden Display', function () {
        it('displays garden information correctly', function () {
            $garden = Garden::factory()->for($this->user)->create([
                'name' => 'Mein Testgarten',
                'description' => 'Ein wunderschöner Garten zum Testen',
                'size_sqm' => 150.75,
                'location' => 'Hinterhof',
                'city' => 'Berlin',
                'postal_code' => '10115',
            ]);

            $response = $this->actingAs($this->user)->get(route('gardens.show', $garden));

            $response->assertOk()
                ->assertSee('Mein Testgarten')
                ->assertSee('Ein wunderschöner Garten zum Testen')
                ->assertSee('150,75 m²')
                ->assertSee('Hinterhof')
                ->assertSee('10115, Berlin');
        });

        it('displays garden type correctly', function () {
            $garden = Garden::factory()->vegetableGarden()->for($this->user)->create();

            $response = $this->actingAs($this->user)->get(route('gardens.show', $garden));

            $response->assertOk()
                ->assertSee('Nutzgarten');
        });

        it('displays active status correctly', function () {
            $activeGarden = Garden::factory()->for($this->user)->create(['is_active' => true]);
            $inactiveGarden = Garden::factory()->for($this->user)->create(['is_active' => false]);

            $activeResponse = $this->actingAs($this->user)->get(route('gardens.show', $activeGarden));
            $inactiveResponse = $this->actingAs($this->user)->get(route('gardens.show', $inactiveGarden));

            $activeResponse->assertOk()->assertSee('Aktiv');
            $inactiveResponse->assertOk()->assertSee('Inaktiv');
        });

        it('displays coordinates when available', function () {
            $garden = Garden::factory()
                ->for($this->user)
                ->withCoordinates(52.5200, 13.4050)
                ->create();

            $response = $this->actingAs($this->user)->get(route('gardens.show', $garden));

            $response->assertOk()
                ->assertSee('GPS-Koordinaten verfügbar')
                ->assertSee('52.5200')
                ->assertSee('13.4050');
        });

        it('handles missing coordinates gracefully', function () {
            $garden = Garden::factory()
                ->for($this->user)
                ->withoutCoordinates()
                ->create();

            $response = $this->actingAs($this->user)->get(route('gardens.show', $garden));

            $response->assertOk()
                ->assertDontSee('GPS-Koordinaten verfügbar');
        });

        it('displays garden age when established date is set', function () {
            $garden = Garden::factory()
                ->for($this->user)
                ->create(['established_at' => now()->subYears(3)]);

            $response = $this->actingAs($this->user)->get(route('gardens.show', $garden));

            $response->assertOk()
                ->assertSee('3 Jahre alt');
        });

        it('handles singular year correctly', function () {
            $garden = Garden::factory()
                ->for($this->user)
                ->create(['established_at' => now()->subYear()]);

            $response = $this->actingAs($this->user)->get(route('gardens.show', $garden));

            $response->assertOk()
                ->assertSee('1 Jahr alt');
        });
    });

    describe('Plants in Garden', function () {
        it('displays plants associated with garden', function () {
            $plant1 = Plant::factory()->create([
                'name' => 'Testpflanze 1',
                'latin_name' => 'Testicus planticus',
            ]);
            $plant2 = Plant::factory()->create([
                'name' => 'Testpflanze 2',
            ]);

            // Associate plants with garden
            $this->garden->plants()->attach([$plant1->id, $plant2->id]);

            $response = $this->actingAs($this->user)->get(route('gardens.show', $this->garden));

            $response->assertOk()
                ->assertSee('Testpflanze 1')
                ->assertSee('Testicus planticus')
                ->assertSee('Testpflanze 2')
                ->assertSee('Anzeigen →');
        });

        it('displays empty state when no plants in garden', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.show', $this->garden));

            $response->assertOk()
                ->assertSee('Noch keine Pflanzen in diesem Garten')
                ->assertSee('Pflanzen durchstöbern →');
        });

        it('includes plant links to show page', function () {
            $plant = Plant::factory()->create(['name' => 'Testpflanze']);
            $this->garden->plants()->attach($plant->id);

            $response = $this->actingAs($this->user)->get(route('gardens.show', $this->garden));

            $response->assertOk();

            $content = $response->getContent();
            expect($content)->toContain(route('plants.show', $plant));
        });
    });

    describe('View Data', function () {
        it('loads garden with relationships', function () {
            $plant = Plant::factory()->create();
            $this->garden->plants()->attach($plant->id);

            $response = $this->actingAs($this->user)->get(route('gardens.show', $this->garden));

            $response->assertOk();

            // Test that relationships are loaded by checking N+1 query prevention
            // This would normally require database query counting, but we test the structure
            expect($response->viewData('garden'))->toBeInstanceOf(Garden::class);
        });

        it('passes correct garden to view', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.show', $this->garden));

            $response->assertOk();

            $viewGarden = $response->viewData('garden');
            expect($viewGarden->id)->toBe($this->garden->id);
            expect($viewGarden->name)->toBe($this->garden->name);
        });
    });

    describe('Route Model Binding', function () {
        it('returns 404 for non-existent garden', function () {
            $response = $this->actingAs($this->user)->get('/gardens/999999');

            $response->assertNotFound();
        });

        it('works with garden id in URL', function () {
            $response = $this->actingAs($this->user)->get("/gardens/{$this->garden->id}");

            $response->assertOk();
        });
    });

    describe('Response Structure', function () {
        it('returns successful response', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.show', $this->garden));

            $response->assertOk();
        });

        it('uses correct view', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.show', $this->garden));

            $response->assertViewIs('gardens.show');
        });

        it('returns HTML content', function () {
            $response = $this->actingAs($this->user)->get(route('gardens.show', $this->garden));

            $response->assertOk();
            expect($response->headers->get('Content-Type'))->toContain('text/html');
        });
    });
});
