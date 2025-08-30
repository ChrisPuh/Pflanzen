<?php

declare(strict_types=1);

use App\DTOs\Area\AreaDeleteDTO;
use App\DTOs\Area\AreaStoreDTO;
use App\DTOs\Area\AreaUpdateDTO;
use App\Enums\Area\AreaTypeEnum;
use App\Models\Area;
use App\Models\Garden;
use App\Models\Plant;
use App\Models\User;
use App\Repositories\Area\AreaRepository;

describe('AreaRepository', function () {

    beforeEach(function () {
        $this->repository = new AreaRepository();
        $this->garden = Garden::factory()->create();
    });

    // Diese Tests sollten in Ihren bestehenden AreaRepositoryTest.php eingefügt werden

    describe('queryForUser', function () {

        beforeEach(function () {
            // Zusätzliche Setup-Daten für queryForUser Tests
            $this->user = User::factory()->user()->create();
            $this->adminUser = User::factory()->admin()->create();
            $this->otherUser = User::factory()->user()->create();

            // Gärten für verschiedene Benutzer erstellen
            $this->userGarden = Garden::factory()->create(['user_id' => $this->user->id]);
            $this->otherUserGarden = Garden::factory()->create(['user_id' => $this->otherUser->id]);

            // Areas für verschiedene Gärten erstellen
            $this->userArea = Area::factory()->create([
                'garden_id' => $this->userGarden->id,
                'name' => 'User Area',
            ]);

            $this->otherUserArea = Area::factory()->create([
                'garden_id' => $this->otherUserGarden->id,
                'name' => 'Other User Area',
            ]);

            // Plants für Relationship-Tests - Many-to-Many über Pivot
            if (method_exists(Area::class, 'plants')) {
                $this->userPlant = Plant::factory()->create([
                    'name' => 'User Plant',
                ]);

                // Attach plant to area via pivot table
                $this->userArea->plants()->attach($this->userPlant->id, [
                    'planted_at' => now(),
                    'notes' => 'Test plant',
                    'quantity' => 5,
                ]);
            }
        });

        it('returns query with proper eager loading', function () {
            $query = $this->repository->queryForUser($this->user->id, false);

            // Prüfen ob die richtigen Relationships geladen werden
            $eagerLoads = $query->getEagerLoads();

            expect($eagerLoads)->toHaveKey('garden')
                ->and($eagerLoads)->toHaveKey('plants');

            // Prüfen der spezifischen Select-Felder für garden
            expect($eagerLoads['garden'])->toBeCallable();
        });

        it('returns only user areas for regular user', function () {
            $query = $this->repository->queryForUser($this->user->id, false);
            $areas = $query->get();

            expect($areas)->toHaveCount(1)
                ->and($areas->first()->id)->toBe($this->userArea->id)
                ->and($areas->first()->name)->toBe('User Area');

            // Sicherstellen, dass andere Benutzer-Areas nicht enthalten sind
            $areaIds = $areas->pluck('id')->toArray();
            expect($areaIds)->not->toContain($this->otherUserArea->id);
        });

        it('returns all areas for admin user', function () {
            $query = $this->repository->queryForUser($this->adminUser->id, true);
            $areas = $query->get();

            expect($areas)->toHaveCount(2);

            $areaIds = $areas->pluck('id')->toArray();
            expect($areaIds)->toContain($this->userArea->id)
                ->and($areaIds)->toContain($this->otherUserArea->id);
        });

        it('loads garden relationship with correct select fields', function () {
            $query = $this->repository->queryForUser($this->user->id, false);
            $area = $query->first();

            expect($area->garden)->not->toBeNull()
                ->and($area->garden->id)->toBe($this->userGarden->id)
                ->and($area->garden->name)->toBe($this->userGarden->name)
                ->and($area->garden->type)->toBe($this->userGarden->type);

            // Prüfen, dass nur die spezifizierten Felder geladen wurden
            expect($area->garden->getAttributes())->toHaveKeys(['id', 'name', 'type']);
        });

        it('loads plants relationship correctly', function () {
            // Nur ausführen wenn Plants-Relationship existiert
            if (! method_exists(Area::class, 'plants')) {
                $this->markTestSkipped('Plants relationship not available');
            }

            $query = $this->repository->queryForUser($this->user->id, false);
            $area = $query->first();

            expect($area->plants)->not->toBeNull();

            if ($area->plants->isNotEmpty()) {
                $plant = $area->plants->first();
                expect($plant->getAttributes())->toHaveKeys(['id', 'name']);
            }
        });

        it('applies user filter correctly with whereHas', function () {
            // Einen weiteren Garten für denselben Benutzer erstellen
            $anotherUserGarden = Garden::factory()->forUser($this->user)->create();
            $anotherUserArea = Area::factory()
                ->forGarden($anotherUserGarden)
                ->create(['name' => 'Another User Area']);

            $query = $this->repository->queryForUser($this->user->id, false);
            $areas = $query->get();

            expect($areas)->toHaveCount(2);

            // Da user_id nicht im Select ist, müssen wir die Beziehung neu laden
            foreach ($areas as $area) {
                $fullGarden = $area->garden()->first();
                expect($fullGarden->user_id)->toBe($this->user->id);
            }
        });

        it('returns empty collection when user has no gardens', function () {
            $userWithoutGardens = User::factory()->create();

            $query = $this->repository->queryForUser($userWithoutGardens->id, false);
            $areas = $query->get();

            expect($areas)->toHaveCount(0);
        });

        it('preserves query builder functionality', function () {
            $query = $this->repository->queryForUser($this->user->id, false);

            // Zusätzliche Where-Klauseln hinzufügen
            $filteredQuery = $query->where('name', 'User Area');
            $areas = $filteredQuery->get();

            expect($areas)->toHaveCount(1)
                ->and($areas->first()->name)->toBe('User Area');
        });

        it('handles soft deleted areas correctly', function () {
            // Area soft delete
            $this->userArea->delete();

            $query = $this->repository->queryForUser($this->user->id, false);
            $areas = $query->get();

            // Soft deleted Areas sollten nicht enthalten sein
            expect($areas)->toHaveCount(0);

            // Mit withTrashed() sollten sie sichtbar sein
            $queryWithTrashed = $this->repository->queryForUser($this->user->id, false)->withTrashed();
            $areasWithTrashed = $queryWithTrashed->get();

            expect($areasWithTrashed)->toHaveCount(1);
        });

        it('returns Builder instance', function () {
            $query = $this->repository->queryForUser($this->user->id, false);

            expect($query)->toBeInstanceOf(Illuminate\Database\Eloquent\Builder::class);
        });

        it('admin flag overrides user restriction', function () {
            // Sicherstellen, dass Admin-Flag die Benutzerfilterung überschreibt
            $query1 = $this->repository->queryForUser($this->user->id, true); // Als Admin
            $query2 = $this->repository->queryForUser($this->user->id, false); // Als regulärer Benutzer

            $adminAreas = $query1->get();
            $userAreas = $query2->get();

            expect($adminAreas->count())->toBeGreaterThanOrEqual($userAreas->count());
        });
    });
    describe('store', function () {
        it('creates area with all data', function () {
            $dto = new AreaStoreDTO(
                name: 'Test Vegetable Patch',
                gardenId: $this->garden->id,
                type: AreaTypeEnum::VegetableBed,
                isActive: true,
                description: 'Growing tomatoes and peppers',
                sizeSqm: 25.5,
                coordinates: ['x' => 10.5, 'y' => 20.3],
                color: '#00FF00'
            );

            $area = $this->repository->store($dto);

            expect($area)
                ->toBeInstanceOf(Area::class)
                ->id->not->toBeNull()
                ->name->toBe('Test Vegetable Patch')
                ->garden_id->toBe($this->garden->id)
                ->type->toBe(AreaTypeEnum::VegetableBed)
                ->is_active->toBeTrue()
                ->description->toBe('Growing tomatoes and peppers')
                ->size_sqm->toBe(25.5)
                ->coordinates->toBe(['x' => 10.5, 'y' => 20.3])
                ->color->toBe('#00FF00');

            $this->assertDatabaseHas('areas', [
                'name' => 'Test Vegetable Patch',
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::VegetableBed->value,
                'is_active' => true,
                'description' => 'Growing tomatoes and peppers',
                'size_sqm' => 25.5,
                'color' => '#00FF00',
            ]);
        });

        it('creates area with minimal required data', function () {
            $dto = new AreaStoreDTO(
                name: 'Minimal Area',
                gardenId: $this->garden->id,
                type: AreaTypeEnum::HerbBed,
                isActive: false
            );

            $area = $this->repository->store($dto);

            expect($area)
                ->toBeInstanceOf(Area::class)
                ->name->toBe('Minimal Area')
                ->garden_id->toBe($this->garden->id)
                ->type->toBe(AreaTypeEnum::HerbBed)
                ->is_active->toBeFalse()
                ->description->toBeNull()
                ->size_sqm->toBeNull()
                ->coordinates->toBeNull()
                ->color->toBeNull();
        });

        it('handles json coordinates correctly', function () {
            $coordinates = ['x' => 15.75, 'y' => 30.25];
            $dto = new AreaStoreDTO(
                name: 'Coordinates Test',
                gardenId: $this->garden->id,
                type: AreaTypeEnum::FlowerBed,
                coordinates: $coordinates
            );

            $area = $this->repository->store($dto);

            expect($area->coordinates)->toBe($coordinates);

            // Verify it's stored as JSON in database
            $this->assertDatabaseHas('areas', [
                'name' => 'Coordinates Test',
                'coordinates' => json_encode($coordinates),
            ]);
        });

        it('sets timestamps automatically', function () {
            $dto = new AreaStoreDTO(
                name: 'Timestamp Test',
                gardenId: $this->garden->id,
                type: AreaTypeEnum::VegetableBed
            );

            $area = $this->repository->store($dto);

            expect($area->created_at)->not->toBeNull()
                ->and($area->updated_at)->not->toBeNull()
                ->and($area->created_at)->toEqual($area->updated_at);
        });
    });

    describe('update', function () {
        it('updates area successfully', function () {
            $area = Area::factory()->create([
                'name' => 'Old Name',
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::HerbBed,
                'is_active' => true,
            ]);

            $dto = AreaUpdateDTO::fromValidated([
                'name' => 'Updated Name',
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::FlowerBed->value,
                'is_active' => false,
                'description' => 'Updated description',
                'size_sqm' => 30.0,
                'coordinates_x' => 5.1,
                'coordinates_y' => 10.1,
                'color' => '#0000FF',
            ]);

            $updatedArea = $this->repository->update($area, $dto);

            expect($updatedArea->id)->toBe($area->id)
                ->and($updatedArea->name)->toBe('Updated Name')
                ->and($updatedArea->garden_id)->toBe($this->garden->id)
                ->and($updatedArea->type)->toBe(AreaTypeEnum::FlowerBed)
                ->and($updatedArea->is_active)->toBeFalse()
                ->and($updatedArea->description)->toBe('Updated description')
                ->and($updatedArea->size_sqm)->toBe(30.0)
                ->and($updatedArea->coordinates)->toBe(['x' => 5.1, 'y' => 10.1])
                ->and($updatedArea->color)->toBe('#0000FF');

            $this->assertDatabaseHas('areas', [
                'id' => $area->id,
                'name' => 'Updated Name',
                'type' => AreaTypeEnum::FlowerBed->value,
                'is_active' => false,
                'description' => 'Updated description',
                'size_sqm' => 30.0,
                'color' => '#0000FF',
            ]);
        });

        it('sets timestamps on update', function () {
            $area = Area::factory()->create([
                'name' => 'Timestamp Area',
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::HerbBed,
                'is_active' => true,
            ]);

            $originalUpdatedAt = $area->updated_at;

            sleep(1); // Ensure timestamp difference

            $dto = AreaUpdateDTO::fromValidated([
                'name' => 'Timestamp Area Updated',
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::HerbBed->value,
                'is_active' => true,
            ]);

            $updatedArea = $this->repository->update($area, $dto);

            expect($updatedArea->updated_at)->not->toBe($originalUpdatedAt)
                ->and($updatedArea->updated_at)->toBeGreaterThan($originalUpdatedAt);
        });
    });

    describe('delete', function () {
        it('soft deletes area successfully', function () {
            $area = Area::factory()->create([
                'name' => 'Area to Delete',
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::VegetableBed,
                'is_active' => true,

            ]);

            $dto = new AreaDeleteDTO(areaId: $area->id, name: $area->name, isActive: false);

            $result = $this->repository->delete($dto);

            expect($result)->toBeTrue();

            // Verify area was soft deleted
            $this->assertSoftDeleted('areas', [
                'id' => $area->id,
                'name' => 'Area to Delete',
            ]);

            // Verify is_active was updated before deletion
            $this->assertDatabaseHas('areas', [
                'id' => $area->id,
                'is_active' => false,
            ]);
        });

        it('updates is_active before soft deletion', function () {
            $area = Area::factory()->create([
                'name' => 'Active Area',
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::HerbBed,
                'is_active' => true,
            ]);

            $dto = new AreaDeleteDTO(areaId: $area->id, name: $area->name, isActive: false);

            $result = $this->repository->delete($dto);

            expect($result)->toBeTrue();

            // Check that area was deactivated before soft deletion
            $deletedArea = Area::withTrashed()->find($area->id);
            expect($deletedArea->is_active)->toBeFalse()
                ->and($deletedArea->deleted_at)->not->toBeNull();
        });

        it('handles already inactive area deletion', function () {
            $area = Area::factory()->create([
                'name' => 'Inactive Area',
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::FlowerBed,
                'is_active' => false, // Already inactive
            ]);

            $dto = new AreaDeleteDTO(areaId: $area->id, name: $area->name, isActive: false);

            $result = $this->repository->delete($dto);

            expect($result)->toBeTrue();

            $this->assertSoftDeleted('areas', [
                'id' => $area->id,
                'is_active' => false,
            ]);
        });

        it('sets deleted_at timestamp correctly', function () {
            $area = Area::factory()->create([
                'name' => 'Timestamp Delete Test',
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::VegetableBed,
                'is_active' => true,
            ]);

            expect($area->deleted_at)->toBeNull();

            $dto = new AreaDeleteDTO(areaId: $area->id, name: $area->name, isActive: false);
            $result = $this->repository->delete($dto);

            expect($result)->toBeTrue();

            $deletedArea = Area::withTrashed()->find($area->id);
            expect($deletedArea->deleted_at)->not->toBeNull()
                ->and($deletedArea->deleted_at)->toBeInstanceOf(Carbon\Carbon::class);
        });

        it('can activate area during deletion process', function () {
            // Edge case: someone passes isActive: true to delete method
            $area = Area::factory()->create([
                'name' => 'Edge Case Area',
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::HerbBed,
                'is_active' => false,
            ]);

            $dto = new AreaDeleteDTO(areaId: $area->id, name: $area->name, isActive: true); // Activate before delete

            $result = $this->repository->delete($dto);

            expect($result)->toBeTrue();

            $deletedArea = Area::withTrashed()->find($area->id);
            expect($deletedArea->is_active)->toBeTrue() // Was activated
                ->and($deletedArea->deleted_at)->not->toBeNull(); // But then deleted
        });
    });

});
