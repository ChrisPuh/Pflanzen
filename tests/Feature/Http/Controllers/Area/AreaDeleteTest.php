<?php

declare(strict_types=1);

use App\Enums\Area\AreaTypeEnum;
use App\Models\Area;
use App\Models\Garden;
use App\Models\User;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->garden = Garden::factory()->create(['user_id' => $this->user->id]);
    $this->area = Area::factory()->create([
        'garden_id' => $this->garden->id,
        'name' => 'Test Area',
        'type' => AreaTypeEnum::FlowerBed,
        'description' => 'Test description',
        'is_active' => true,
    ]);
});

describe('DELETE /areas/{area}', function () {
    it('can soft delete an area', function (): void {
        $response = $this->actingAs($this->user)->delete(route('areas.destroy', $this->area));

        $response->assertRedirect(route('areas.index'))
            ->assertSessionHas('success', "Bereich 'Test Area' wurde erfolgreich gelöscht.");

        // Area should be soft deleted
        $this->assertSoftDeleted('areas', [
            'id' => $this->area->id,
        ]);
    });

    it('stores the area name before deletion for success message', function (): void {
        $areaName = $this->area->name;

        $response = $this->actingAs($this->user)->delete(route('areas.destroy', $this->area));

        $response->assertSessionHas('success', "Bereich '{$areaName}' wurde erfolgreich gelöscht.");
    });

    it('denies access for non-owners', function (): void {
        $otherUser = User::factory()->create();
        $otherGarden = Garden::factory()->create(['user_id' => $otherUser->id]);
        $otherArea = Area::factory()->create(['garden_id' => $otherGarden->id]);

        $response = $this->actingAs($this->user)->delete(route('areas.destroy', $otherArea));

        $response->assertForbidden();

        // Area should not be deleted
        $this->assertDatabaseHas('areas', [
            'id' => $otherArea->id,
            'deleted_at' => null,
        ]);
    });

    it('requires authentication', function (): void {
        $response = $this->delete(route('areas.destroy', $this->area));

        $response->assertRedirect(route('login'));

        // Area should not be deleted
        $this->assertDatabaseHas('areas', [
            'id' => $this->area->id,
            'deleted_at' => null,
        ]);
    });

    it('can delete already inactive areas', function (): void {
        $this->area->update(['is_active' => false]);

        $response = $this->actingAs($this->user)->delete(route('areas.destroy', $this->area));

        $response->assertRedirect(route('areas.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('areas', [
            'id' => $this->area->id,
        ]);
    });
});

describe('POST /areas/{areaId}/restore', function () {
    beforeEach(function (): void {
        $this->area->delete(); // Soft delete the area
    });

    it('can restore a soft deleted area', function (): void {
        $response = $this->actingAs($this->user)->post(route('areas.restore', $this->area->id));

        $response->assertRedirect(route('areas.show', $this->area))
            ->assertSessionHas('success', "Bereich 'Test Area' wurde erfolgreich wiederhergestellt.");

        // Area should be restored
        $this->assertDatabaseHas('areas', [
            'id' => $this->area->id,
            'deleted_at' => null,
        ]);
    });

    it('denies access for non-owners', function (): void {
        $otherUser = User::factory()->create();
        $otherGarden = Garden::factory()->create(['user_id' => $otherUser->id]);
        $otherArea = Area::factory()->create(['garden_id' => $otherGarden->id]);
        $otherArea->delete();

        $response = $this->actingAs($this->user)->post(route('areas.restore', $otherArea->id));

        $response->assertForbidden();

        // Area should remain deleted
        $this->assertSoftDeleted('areas', [
            'id' => $otherArea->id,
        ]);
    });

    it('requires authentication', function (): void {
        $response = $this->post(route('areas.restore', $this->area->id));

        $response->assertRedirect(route('login'));

        // Area should remain deleted
        $this->assertSoftDeleted('areas', [
            'id' => $this->area->id,
        ]);
    });

    it('fails when trying to restore non-existent area', function (): void {
        $response = $this->actingAs($this->user)->post(route('areas.restore', 99999));

        $response->assertNotFound();
    });

    it('can attempt to restore already active area', function (): void {
        $this->area->restore(); // Restore the area first

        $response = $this->actingAs($this->user)->post(route('areas.restore', $this->area->id));

        // The controller doesn't prevent restoring already active areas
        $response->assertRedirect(route('areas.show', $this->area));
    });
});

describe('DELETE /areas/{areaId}/force', function () {
    beforeEach(function (): void {
        $this->area->delete(); // Soft delete the area first
    });

    it('can permanently delete a soft deleted area', function (): void {
        $areaId = $this->area->id;
        $areaName = $this->area->name;

        $response = $this->actingAs($this->user)->delete(route('areas.force-delete', $areaId));

        $response->assertRedirect(route('areas.index'))
            ->assertSessionHas('success', "Bereich '{$areaName}' wurde permanent gelöscht.");

        // Area should be completely removed from database
        $this->assertDatabaseMissing('areas', [
            'id' => $areaId,
        ]);
    });

    it('stores the area name before permanent deletion for success message', function (): void {
        $areaName = $this->area->name;

        $response = $this->actingAs($this->user)->delete(route('areas.force-delete', $this->area->id));

        $response->assertSessionHas('success', "Bereich '{$areaName}' wurde permanent gelöscht.");
    });

    it('denies access for non-owners', function (): void {
        $otherUser = User::factory()->create();
        $otherGarden = Garden::factory()->create(['user_id' => $otherUser->id]);
        $otherArea = Area::factory()->create(['garden_id' => $otherGarden->id]);
        $otherArea->delete();

        $response = $this->actingAs($this->user)->delete(route('areas.force-delete', $otherArea->id));

        $response->assertForbidden();

        // Area should still exist (soft deleted)
        $this->assertSoftDeleted('areas', [
            'id' => $otherArea->id,
        ]);
    });

    it('requires authentication', function (): void {
        $response = $this->delete(route('areas.force-delete', $this->area->id));

        $response->assertRedirect(route('login'));

        // Area should still exist (soft deleted)
        $this->assertSoftDeleted('areas', [
            'id' => $this->area->id,
        ]);
    });

    it('fails when trying to force delete non-existent area', function (): void {
        $response = $this->actingAs($this->user)->delete(route('areas.force-delete', 99999));

        $response->assertNotFound();
    });

    it('can attempt to force delete non-soft-deleted area', function (): void {
        $activeArea = Area::factory()->create(['garden_id' => $this->garden->id]);

        $response = $this->actingAs($this->user)->delete(route('areas.force-delete', $activeArea->id));

        // The controller doesn't prevent force deleting active areas
        $response->assertRedirect(route('areas.index'));

        // Area should be completely removed
        $this->assertDatabaseMissing('areas', ['id' => $activeArea->id]);
    });
});

describe('Policy integration', function () {
    it('uses AreaPolicy for all delete operations', function (): void {
        $otherUser = User::factory()->create();
        $otherGarden = Garden::factory()->create(['user_id' => $otherUser->id]);
        $otherArea = Area::factory()->create(['garden_id' => $otherGarden->id]);

        // Test destroy policy
        $response = $this->actingAs($this->user)->delete(route('areas.destroy', $otherArea));
        $response->assertForbidden();

        // Test restore policy
        $otherArea->delete();
        $response = $this->actingAs($this->user)->post(route('areas.restore', $otherArea->id));
        $response->assertForbidden();

        // Test force delete policy
        $response = $this->actingAs($this->user)->delete(route('areas.force-delete', $otherArea->id));
        $response->assertForbidden();
    });
});

describe('Edge cases and error handling', function () {
    it('handles areas with special characters in name', function (): void {
        $specialArea = Area::factory()->create([
            'garden_id' => $this->garden->id,
            'name' => 'Ärger & Füße "Test" \'Area\'',
        ]);

        $response = $this->actingAs($this->user)->delete(route('areas.destroy', $specialArea));

        $response->assertRedirect(route('areas.index'))
            ->assertSessionHas('success', "Bereich 'Ärger & Füße \"Test\" 'Area'' wurde erfolgreich gelöscht.");

        $this->assertSoftDeleted('areas', [
            'id' => $specialArea->id,
        ]);
    });

    it('handles areas with very long names', function (): void {
        $longName = str_repeat('Very Long Area Name ', 10);
        $longArea = Area::factory()->create([
            'garden_id' => $this->garden->id,
            'name' => $longName,
        ]);

        $response = $this->actingAs($this->user)->delete(route('areas.destroy', $longArea));

        $response->assertRedirect(route('areas.index'))
            ->assertSessionHas('success', "Bereich '{$longName}' wurde erfolgreich gelöscht.");

        $this->assertSoftDeleted('areas', [
            'id' => $longArea->id,
        ]);
    });

    it('maintains referential integrity when soft deleting', function (): void {
        // Create plants associated with the area
        $plant = App\Models\Plant::factory()->create();
        $this->area->plants()->attach($plant->id);

        $response = $this->actingAs($this->user)->delete(route('areas.destroy', $this->area));

        $response->assertRedirect(route('areas.index'));

        // Area is soft deleted but relationships remain intact
        $this->assertSoftDeleted('areas', ['id' => $this->area->id]);

        // Plant-area relationship should still exist in pivot table
        $this->assertDatabaseHas('area_plant', [
            'area_id' => $this->area->id,
            'plant_id' => $plant->id,
        ]);
    });
});
