<?php

declare(strict_types=1);

use App\Models\Garden;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('GET /gardens', function () {
    it('shows archived gardens link when archived gardens exist', function () {
        // Create an archived garden
        $archivedGarden = Garden::factory()->for($this->user)->create([
            'name' => 'Archived Garden',
        ]);
        $archivedGarden->delete();

        $this->actingAs($this->user)
            ->get(route('gardens.index'))
            ->assertOk()
            ->assertSee('Archivierte Gärten')
            ->assertSee(route('gardens.archived'));
    });

    it('hides archived gardens link when no archived gardens exist', function () {
        // Create only active gardens
        Garden::factory()->for($this->user)->create([
            'name' => 'Active Garden',
        ]);

        $this->actingAs($this->user)
            ->get(route('gardens.index'))
            ->assertOk()
            ->assertDontSee('Archivierte Gärten')
            ->assertDontSee(route('gardens.archived'));
    });

    it('shows archived gardens link for admin when any user has archived gardens', function () {
        $adminUser = User::factory()->create();
        $otherUser = User::factory()->create();

        // Create the admin role first
        $adminRole = Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $adminUser->assignRole($adminRole);

        // Create an archived garden for another user
        $archivedGarden = Garden::factory()->for($otherUser)->create([
            'name' => 'Other User Archived Garden',
        ]);
        $archivedGarden->delete();

        $this->actingAs($adminUser)
            ->get(route('gardens.index'))
            ->assertOk()
            ->assertSee('Archivierte Gärten');
    });

    it('hides archived gardens link for admin when no archived gardens exist anywhere', function () {
        $adminUser = User::factory()->create();

        // Create the admin role first
        $adminRole = Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $adminUser->assignRole($adminRole);

        // Create only active gardens
        Garden::factory()->for($this->user)->create([
            'name' => 'Active Garden',
        ]);

        $this->actingAs($adminUser)
            ->get(route('gardens.index'))
            ->assertOk()
            ->assertDontSee('Archivierte Gärten');
    });

    it('always shows create garden button', function () {
        $this->actingAs($this->user)
            ->get(route('gardens.index'))
            ->assertOk()
            ->assertSee('Neuen Garten erstellen')
            ->assertSee(route('gardens.create'));
    });

    it('passes hasArchivedGardens variable to view', function () {
        $response = $this->actingAs($this->user)
            ->get(route('gardens.index'));

        $response->assertViewHas('hasArchivedGardens', false);

        // Create an archived garden
        $archivedGarden = Garden::factory()->for($this->user)->create();
        $archivedGarden->delete();

        $response = $this->actingAs($this->user)
            ->get(route('gardens.index'));

        $response->assertViewHas('hasArchivedGardens', true);
    });
});
