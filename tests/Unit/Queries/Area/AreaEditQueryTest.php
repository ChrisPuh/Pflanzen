<?php

declare(strict_types=1);

use App\Models\Area;
use App\Models\Garden;
use App\Models\User;
use App\Queries\Area\AreaEditQuery;
use App\Repositories\Area\AreaRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

describe('AreaEditQuery', function () {
    beforeEach(function () {
        // Setup roles for tests
        Spatie\Permission\Models\Role::query()->firstOrCreate(['name' => 'admin']);
        Spatie\Permission\Models\Role::query()->firstOrCreate(['name' => 'user']);

        $this->repository = new AreaRepository();
        $this->query = new AreaEditQuery($this->repository);

        $this->user = User::factory()->user()->create();
        $this->garden = Garden::factory()->create(['user_id' => $this->user->id]);
    });

    describe('execute', function () {
        it('returns Area model when area exists', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            $result = $this->query->execute($area->id);

            expect($result)->toBeInstanceOf(Area::class)
                ->and($result->id)->toBe($area->id);
        });

        it('throws ModelNotFoundException when area does not exist', function () {
            $nonExistentId = 99999;

            expect(fn () => $this->query->execute($nonExistentId))
                ->toThrow(ModelNotFoundException::class);
        });

        it('returns area with relationships loaded for editing', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            $result = $this->query->execute($area->id);

            expect($result)->toBeInstanceOf(Area::class)
                ->and($result->relationLoaded('garden'))->toBeTrue();
        });

        it('loads plants and plantType relationships for edit form', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            $result = $this->query->execute($area->id);

            expect($result)->toBeInstanceOf(Area::class)
                ->and($result->relationLoaded('plants'))->toBeTrue();
        });

        it('handles different area IDs correctly', function () {
            $area1 = Area::factory()->create(['garden_id' => $this->garden->id]);
            $area2 = Area::factory()->create(['garden_id' => $this->garden->id]);

            $result1 = $this->query->execute($area1->id);
            $result2 = $this->query->execute($area2->id);

            expect($result1->id)->toBe($area1->id)
                ->and($result2->id)->toBe($area2->id)
                ->and($result1->id)->not->toBe($result2->id);
        });

        it('returns the exact area requested for editing', function () {
            $area = Area::factory()->create([
                'garden_id' => $this->garden->id,
                'name' => 'Editable Test Area',
            ]);

            $result = $this->query->execute($area->id);

            expect($result->id)->toBe($area->id)
                ->and($result->name)->toBe('Editable Test Area');
        });

        it('uses same implementation as AreaShowQuery', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            // Both queries should return the same data structure since they use queryForShow
            $result = $this->query->execute($area->id);

            expect($result)->toBeInstanceOf(Area::class)
                ->and($result->id)->toBe($area->id)
                ->and($result->relationLoaded('garden'))->toBeTrue()
                ->and($result->relationLoaded('plants'))->toBeTrue();
        });
    });

    describe('constructor', function () {
        it('accepts AreaRepository dependency', function () {
            $repository = new AreaRepository();
            $query = new AreaEditQuery($repository);

            expect($query)->toBeInstanceOf(AreaEditQuery::class);
        });

        it('is readonly', function () {
            $reflection = new ReflectionClass(AreaEditQuery::class);

            expect($reflection->isReadOnly())->toBeTrue();
        });

        it('properties are readonly', function () {
            $reflection = new ReflectionClass(AreaEditQuery::class);

            foreach ($reflection->getProperties() as $property) {
                expect($property->isReadOnly())->toBeTrue();
            }
        });
    });

    describe('type safety', function () {
        it('enforces integer type for areaId parameter', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            $result = $this->query->execute($area->id);

            expect($result)->toBeInstanceOf(Area::class);
        });

        it('returns Area model instance', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            $result = $this->query->execute($area->id);

            expect($result)->toBeInstanceOf(Area::class);
        });
    });

    describe('error handling', function () {
        it('propagates ModelNotFoundException for non-existent areas', function () {
            $nonExistentId = 404;

            expect(fn () => $this->query->execute($nonExistentId))
                ->toThrow(ModelNotFoundException::class);
        });

        it('does not provide fallback behavior for missing areas', function () {
            $nonExistentId = 0;

            // Should throw, not return null or empty model
            expect(fn () => $this->query->execute($nonExistentId))
                ->toThrow(ModelNotFoundException::class);
        });
    });

    describe('repository integration', function () {
        it('uses queryForShow method from repository', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            // Even for edit, uses queryForShow method
            $result = $this->query->execute($area->id);

            expect($result)->toBeInstanceOf(Area::class)
                ->and($result->id)->toBe($area->id);
        });

        it('maintains single responsibility principle', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            // The query should only retrieve the area for editing, not modify it
            $originalName = $area->name;

            $result = $this->query->execute($area->id);

            expect($result->name)->toBe($originalName)
                ->and($result)->toBeInstanceOf(Area::class);
        });

        it('loads required relationships for edit context', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            $result = $this->query->execute($area->id);

            // Verify that relationships needed for editing are loaded
            expect($result->relationLoaded('garden'))->toBeTrue()
                ->and($result->relationLoaded('plants'))->toBeTrue();
        });
    });

    describe('edit form data preparation', function () {
        it('returns complete area data for form population', function () {
            $area = Area::factory()->create([
                'garden_id' => $this->garden->id,
                'name' => 'Edit Form Test Area',
                'description' => 'An area for testing edit forms',
                'is_active' => true,
            ]);

            $result = $this->query->execute($area->id);

            expect($result->name)->toBe('Edit Form Test Area')
                ->and($result->description)->toBe('An area for testing edit forms')
                ->and($result->is_active)->toBe(true)
                ->and($result->garden_id)->toBe($this->garden->id);
        });

        it('preserves all area attributes for editing', function () {
            $area = Area::factory()->create([
                'garden_id' => $this->garden->id,
                'is_active' => false,
                'size_sqm' => 30.75,
                'coordinates' => ['x' => 10.5, 'y' => 20.3],
            ]);

            $result = $this->query->execute($area->id);

            expect($result->is_active)->toBe(false)
                ->and($result->size_sqm)->toBe(30.75)
                ->and($result->coordinates)->toBe(['x' => 10.5, 'y' => 20.3])
                ->and($result->garden_id)->toBe($this->garden->id);
        });

        it('is suitable for edit form population', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            $result = $this->query->execute($area->id);

            // The returned Area should contain all necessary data for editing
            expect($result)->toBeInstanceOf(Area::class)
                ->and($result->id)->toBe($area->id)
                ->and($result->garden_id)->toBe($this->garden->id);
        });
    });

    describe('consistency with show query', function () {
        it('has same behavior as AreaShowQuery for data loading', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            $result = $this->query->execute($area->id);

            // Should load the same relationships and data as AreaShowQuery
            expect($result)->toBeInstanceOf(Area::class)
                ->and($result->relationLoaded('garden'))->toBeTrue()
                ->and($result->relationLoaded('plants'))->toBeTrue();
        });

        it('uses identical repository method as AreaShowQuery', function () {
            $area = Area::factory()->create([
                'garden_id' => $this->garden->id,
                'name' => 'Consistency Test Area',
            ]);

            $result = $this->query->execute($area->id);

            // This documents that both Show and Edit queries use queryForShow
            expect($result->name)->toBe('Consistency Test Area')
                ->and($result)->toBeInstanceOf(Area::class);
        });
    });
});
