<?php

declare(strict_types=1);

use App\Models\Garden;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('GET /gardens/archived', function () {
    it('requires authentication', function () {
        $this->get(route('gardens.archived'))
            ->assertRedirect(route('login'));
    });

    it('shows archived gardens page for authenticated user', function () {
        $this->actingAs($this->user)
            ->get(route('gardens.archived'))
            ->assertOk()
            ->assertViewIs('gardens.archived')
            ->assertViewHas('gardens')
            ->assertViewHas('isAdmin', false)
            ->assertSee('Archivierte Gärten');
    });

    it('displays user\'s archived gardens', function () {
        // Create active garden (should not appear)
        $activeGarden = Garden::factory()->for($this->user)->create([
            'name' => 'Active Garden',
        ]);

        // Create archived garden (should appear)
        $archivedGarden = Garden::factory()->for($this->user)->create([
            'name' => 'Archived Garden',
        ]);
        $archivedGarden->delete();

        // Create other user's archived garden (should not appear)
        $otherArchivedGarden = Garden::factory()->for($this->otherUser)->create([
            'name' => 'Other User Garden',
        ]);
        $otherArchivedGarden->delete();

        $response = $this->actingAs($this->user)
            ->get(route('gardens.archived'));

        $response->assertSee('Archived Garden')
            ->assertDontSee('Active Garden')
            ->assertDontSee('Other User Garden');

        $gardens = $response->viewData('gardens');
        expect($gardens)->toHaveCount(1);
        expect($gardens->first()->name)->toBe('Archived Garden');
    });

    it('shows empty state when no archived gardens exist', function () {
        $this->actingAs($this->user)
            ->get(route('gardens.archived'))
            ->assertSee('Keine archivierten Gärten')
            ->assertSee('Du hast derzeit keine archivierten Gärten');
    });

    it('displays restore buttons for user\'s own gardens', function () {
        $archivedGarden = Garden::factory()->for($this->user)->create([
            'name' => 'Test Garden',
        ]);
        $archivedGarden->delete();

        $this->actingAs($this->user)
            ->get(route('gardens.archived'))
            ->assertSee('Garten wiederherstellen')
            ->assertSee(route('gardens.restore', $archivedGarden->id));
    });

    it('shows garden details correctly', function () {
        $archivedGarden = Garden::factory()->for($this->user)->create([
            'name' => 'Test Garden',
            'description' => 'Test Description',
            'size_sqm' => 25.5,
            'location' => 'Test Location',
            'city' => 'Test City',
        ]);
        $archivedGarden->delete();

        $response = $this->actingAs($this->user)
            ->get(route('gardens.archived'));

        $response->assertSee('Test Garden')
            ->assertSee('25,50 m²')
            ->assertSee('Test Location')
            ->assertSee('Test City')
            ->assertSee('Archiviert am')
            ->assertSee('Inaktiv');
    });

    it('shows garden type label', function () {
        $archivedGarden = Garden::factory()->for($this->user)->create();
        $archivedGarden->delete();

        $this->actingAs($this->user)
            ->get(route('gardens.archived'))
            ->assertSee($archivedGarden->type->getLabel());
    });

    it('shows archived badge for all gardens', function () {
        $archivedGarden = Garden::factory()->for($this->user)->create([
            'name' => 'Test Garden',
        ]);
        $archivedGarden->delete();

        $this->actingAs($this->user)
            ->get(route('gardens.archived'))
            ->assertSee('Archiviert');
    });

    it('shows admin view indicator for admin users', function () {
        $adminUser = User::factory()->create();

        // Create the admin role first
        $adminRole = Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $adminUser->assignRole($adminRole);

        $this->actingAs($adminUser)
            ->get(route('gardens.archived'))
            ->assertSee('(Admin-Ansicht)');
    });

    it('shows all archived gardens for admin users', function () {
        $adminUser = User::factory()->create();

        // Create the admin role first
        $adminRole = Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $adminUser->assignRole($adminRole);

        // Create archived gardens for different users
        $userGarden = Garden::factory()->for($this->user)->create([
            'name' => 'User Garden',
        ]);
        $userGarden->delete();

        $otherUserGarden = Garden::factory()->for($this->otherUser)->create([
            'name' => 'Other User Garden',
        ]);
        $otherUserGarden->delete();

        $response = $this->actingAs($adminUser)
            ->get(route('gardens.archived'));

        $response->assertSee('User Garden')
            ->assertSee('Other User Garden');

        $gardens = $response->viewData('gardens');
        expect($gardens)->toHaveCount(2);
    });

    it('shows user names for admin when viewing other users gardens', function () {
        $adminUser = User::factory()->create();

        // Create the admin role first
        $adminRole = Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $adminUser->assignRole($adminRole);

        $userGarden = Garden::factory()->for($this->user)->create([
            'name' => 'User Garden',
        ]);
        $userGarden->delete();

        $this->actingAs($adminUser)
            ->get(route('gardens.archived'))
            ->assertSee($this->user->name);
    });

    it('shows navigation back to active gardens', function () {
        $this->actingAs($this->user)
            ->get(route('gardens.archived'))
            ->assertSee('Zurück zu aktiven Gärten')
            ->assertSee(route('gardens.index'));
    });

    it('displays plant count for archived gardens', function () {
        $archivedGarden = Garden::factory()->for($this->user)->create();
        $archivedGarden->delete();

        $this->actingAs($this->user)
            ->get(route('gardens.archived'))
            ->assertSee('Pflanzen');
    });
});
