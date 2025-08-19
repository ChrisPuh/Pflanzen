<?php

declare(strict_types=1);

use App\Models\Garden;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('DELETE /gardens/{garden}', function () {
    it('requires authentication', function () {
        $garden = Garden::factory()->for($this->user)->create();

        $this->delete(route('gardens.destroy', $garden))
            ->assertRedirect(route('login'));
    });

    it('archives garden successfully for owner', function () {
        $garden = Garden::factory()->for($this->user)->create([
            'name' => 'Test Garden',
            'is_active' => true,
        ]);

        $this->actingAs($this->user)
            ->delete(route('gardens.destroy', $garden))
            ->assertRedirect(route('gardens.index'))
            ->assertSessionHas('status', 'Garten "Test Garden" wurde archiviert.');

        // Garden should be soft deleted
        expect(Garden::find($garden->id))->toBeNull();

        // But should exist in database with deleted_at
        $trashedGarden = Garden::withTrashed()->find($garden->id);
        expect($trashedGarden)->not->toBeNull();
        expect($trashedGarden->trashed())->toBeTrue();
        expect($trashedGarden->is_active)->toBeFalse();
    });

    it('denies access for non-owners', function () {
        $garden = Garden::factory()->for($this->otherUser)->create();

        $this->actingAs($this->user)
            ->delete(route('gardens.destroy', $garden))
            ->assertForbidden();

        // Garden should not be deleted
        expect($garden->fresh())->not->toBeNull();
    });

    it('sets garden as inactive before archiving', function () {
        $garden = Garden::factory()->for($this->user)->create([
            'is_active' => true,
        ]);

        $this->actingAs($this->user)
            ->delete(route('gardens.destroy', $garden));

        $trashedGarden = Garden::withTrashed()->find($garden->id);
        expect($trashedGarden->is_active)->toBeFalse();
    });
});

describe('POST /gardens/{garden}/restore', function () {
    it('requires authentication', function () {
        $garden = Garden::factory()->for($this->user)->create();
        $garden->delete();

        $this->post(route('gardens.restore', $garden->id))
            ->assertRedirect(route('login'));
    });

    it('restores archived garden successfully for owner', function () {
        $garden = Garden::factory()->for($this->user)->create([
            'name' => 'Test Garden',
            'is_active' => false,
        ]);
        $garden->delete();

        $this->actingAs($this->user)
            ->post(route('gardens.restore', $garden->id))
            ->assertRedirect(route('gardens.show', $garden->id))
            ->assertSessionHas('status', 'Garten "Test Garden" wurde wiederhergestellt.');

        // Garden should be restored
        $restoredGarden = Garden::find($garden->id);
        expect($restoredGarden)->not->toBeNull();
        expect($restoredGarden->trashed())->toBeFalse();
        expect($restoredGarden->is_active)->toBeTrue();
    });

    it('denies access for non-owners', function () {
        $garden = Garden::factory()->for($this->otherUser)->create();
        $garden->delete();

        $this->actingAs($this->user)
            ->post(route('gardens.restore', $garden->id))
            ->assertForbidden();

        // Garden should remain deleted
        $trashedGarden = Garden::withTrashed()->find($garden->id);
        expect($trashedGarden->trashed())->toBeTrue();
    });

    it('returns 404 for non-existent garden', function () {
        $this->actingAs($this->user)
            ->post(route('gardens.restore', 999))
            ->assertNotFound();
    });

    it('returns 404 for non-trashed garden', function () {
        $garden = Garden::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->post(route('gardens.restore', $garden->id))
            ->assertNotFound();
    });

    it('reactivates garden when restoring', function () {
        $garden = Garden::factory()->for($this->user)->create([
            'is_active' => false,
        ]);
        $garden->delete();

        $this->actingAs($this->user)
            ->post(route('gardens.restore', $garden->id));

        $restoredGarden = Garden::find($garden->id);
        expect($restoredGarden->is_active)->toBeTrue();
    });
});
