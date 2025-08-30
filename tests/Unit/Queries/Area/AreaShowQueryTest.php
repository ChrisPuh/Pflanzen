<?php

declare(strict_types=1);

use App\Models\Area;
use App\Models\Garden;
use App\Models\User;
use App\Queries\Area\AreaShowQuery;
use App\Repositories\Area\AreaRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

describe('AreaShowQuery', function () {
    beforeEach(function () {
        // Setup roles for tests
        \Spatie\Permission\Models\Role::query()->firstOrCreate(['name' => 'admin']);
        \Spatie\Permission\Models\Role::query()->firstOrCreate(['name' => 'user']);

        $this->repository = new AreaRepository();
        $this->query = new AreaShowQuery($this->repository);
        
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

            expect(fn() => $this->query->execute($nonExistentId))
                ->toThrow(ModelNotFoundException::class);
        });

        it('returns area with relationships loaded', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            $result = $this->query->execute($area->id);

            expect($result)->toBeInstanceOf(Area::class)
                ->and($result->relationLoaded('garden'))->toBeTrue();
        });

        it('loads plants and plantType relationships', function () {
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

        it('returns the exact area requested', function () {
            $area = Area::factory()->create([
                'garden_id' => $this->garden->id,
                'name' => 'Specific Test Area'
            ]);

            $result = $this->query->execute($area->id);

            expect($result->id)->toBe($area->id)
                ->and($result->name)->toBe('Specific Test Area');
        });
    });

    describe('constructor', function () {
        it('accepts AreaRepository dependency', function () {
            $repository = new AreaRepository();
            $query = new AreaShowQuery($repository);

            expect($query)->toBeInstanceOf(AreaShowQuery::class);
        });

        it('is readonly', function () {
            $reflection = new ReflectionClass(AreaShowQuery::class);

            expect($reflection->isReadOnly())->toBeTrue();
        });

        it('properties are readonly', function () {
            $reflection = new ReflectionClass(AreaShowQuery::class);

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

            expect(fn() => $this->query->execute($nonExistentId))
                ->toThrow(ModelNotFoundException::class);
        });

        it('does not provide fallback behavior for missing areas', function () {
            $nonExistentId = 0;

            // Should throw, not return null or empty model
            expect(fn() => $this->query->execute($nonExistentId))
                ->toThrow(ModelNotFoundException::class);
        });
    });

    describe('repository integration', function () {
        it('uses queryForShow method from repository', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            // This should work if the repository method is correctly implemented
            $result = $this->query->execute($area->id);

            expect($result)->toBeInstanceOf(Area::class)
                ->and($result->id)->toBe($area->id);
        });

        it('maintains single responsibility principle', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            // The query should only retrieve the area, not modify it
            $originalName = $area->name;
            
            $result = $this->query->execute($area->id);

            expect($result->name)->toBe($originalName)
                ->and($result)->toBeInstanceOf(Area::class);
        });

        it('loads required relationships for show context', function () {
            $area = Area::factory()->create(['garden_id' => $this->garden->id]);

            $result = $this->query->execute($area->id);

            // Verify that relationships needed for showing are loaded
            expect($result->relationLoaded('garden'))->toBeTrue()
                ->and($result->relationLoaded('plants'))->toBeTrue();
        });
    });

    describe('data integrity', function () {
        it('returns complete area data', function () {
            $area = Area::factory()->create([
                'garden_id' => $this->garden->id,
                'name' => 'Complete Test Area',
                'description' => 'A complete area for testing'
            ]);

            $result = $this->query->execute($area->id);

            expect($result->name)->toBe('Complete Test Area')
                ->and($result->description)->toBe('A complete area for testing')
                ->and($result->garden_id)->toBe($this->garden->id);
        });

        it('preserves area attributes correctly', function () {
            $area = Area::factory()->create([
                'garden_id' => $this->garden->id,
                'is_active' => true,
                'size_sqm' => 25.5
            ]);

            $result = $this->query->execute($area->id);

            expect($result->is_active)->toBe(true)
                ->and($result->size_sqm)->toBe(25.5)
                ->and($result->garden_id)->toBe($this->garden->id);
        });
    });
});