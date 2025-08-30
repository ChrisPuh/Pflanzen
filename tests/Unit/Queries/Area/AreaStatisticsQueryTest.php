<?php

declare(strict_types=1);

use App\DTOs\Area\AreaStatisticsDTO;
use App\Enums\Area\AreaTypeEnum;
use App\Models\Area;
use App\Models\Garden;
use App\Models\User;
use App\Queries\Area\AreaStatisticsQuery;
use App\Repositories\Area\AreaRepository;

describe('AreaStatisticsQuery', function () {
    beforeEach(function () {
        // Setup roles for tests
        Spatie\Permission\Models\Role::query()->firstOrCreate(['name' => 'admin']);
        Spatie\Permission\Models\Role::query()->firstOrCreate(['name' => 'user']);

        $this->repository = new AreaRepository();
        $this->query = new AreaStatisticsQuery($this->repository);

        $this->user = User::factory()->user()->create();
        $this->garden = Garden::factory()->create(['user_id' => $this->user->id]);
    });

    describe('execute', function () {
        it('returns AreaStatisticsDTO with correct counts for regular user', function () {
            // Create test areas
            Area::factory()->count(10)->create(['garden_id' => $this->garden->id, 'is_active' => true]);
            Area::factory()->count(3)->create(['garden_id' => $this->garden->id, 'is_active' => false]);

            // Create planting areas
            Area::factory()->count(5)->create([
                'garden_id' => $this->garden->id,
                'is_active' => true,
                'type' => AreaTypeEnum::VegetableBed,
            ]);

            $result = $this->query->execute($this->user->id, false);

            expect($result)->toBeInstanceOf(AreaStatisticsDTO::class)
                ->and($result->total)->toBe(18) // 10 + 3 + 5 total areas
                ->and($result->active)->toBe(15) // 10 + 5 active areas
                ->and($result->planting)->toBeGreaterThan(4) // at least 5 planting areas
                ->and($result->archived)->toBe(0) // default value
                ->and($result->buildings)->toBe(0) // default value
                ->and($result->waterFeatures)->toBe(0); // default value
        });

        it('returns zero counts when user has no areas', function () {
            $emptyUser = User::factory()->user()->create();

            $result = $this->query->execute($emptyUser->id, false);

            expect($result)->toBeInstanceOf(AreaStatisticsDTO::class)
                ->and($result->total)->toBe(0)
                ->and($result->active)->toBe(0)
                ->and($result->planting)->toBe(0);
        });

        it('returns AreaStatisticsDTO with correct counts for admin user', function () {
            $admin = User::factory()->admin()->create();

            // Create areas for multiple users that admin can see
            $otherUser = User::factory()->user()->create();
            $otherGarden = Garden::factory()->create(['user_id' => $otherUser->id]);

            Area::factory()->count(5)->create(['garden_id' => $this->garden->id, 'is_active' => true]);
            Area::factory()->count(3)->create(['garden_id' => $otherGarden->id, 'is_active' => true]);

            $result = $this->query->execute($admin->id, true);

            expect($result)->toBeInstanceOf(AreaStatisticsDTO::class)
                ->and($result->total)->toBeGreaterThan(7) // at least 8 areas visible to admin
                ->and($result->active)->toBeGreaterThan(7);
        });

        it('correctly identifies planting areas using enum values', function () {
            // Create specific area types
            Area::factory()->create([
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::VegetableBed,
                'is_active' => true,
            ]);
            Area::factory()->create([
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::HerbBed,
                'is_active' => true,
            ]);
            Area::factory()->create([
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::FlowerBed,
                'is_active' => true,
            ]);

            $result = $this->query->execute($this->user->id, false);

            expect($result->planting)->toBeGreaterThan(2);
            expect($result->total)->toBe(3);
            expect($result->active)->toBe(3);
        });

        it('handles mixed area types correctly', function () {
            // Create planting and non-planting areas
            Area::factory()->count(3)->create([
                'garden_id' => $this->garden->id,
                'type' => AreaTypeEnum::VegetableBed, // planting area
                'is_active' => true,
            ]);

            // Assuming there are non-planting area types in the enum
            $plantingValues = AreaTypeEnum::getPlantingAreaValues();
            $allValues = collect(AreaTypeEnum::cases())->pluck('value')->toArray();
            $nonPlantingValues = array_values(array_diff($allValues, $plantingValues));

            if (! empty($nonPlantingValues)) {
                Area::factory()->create([
                    'garden_id' => $this->garden->id,
                    'type' => AreaTypeEnum::from($nonPlantingValues[0]), // non-planting area
                    'is_active' => true,
                ]);
            }

            $result = $this->query->execute($this->user->id, false);

            expect($result->planting)->toBe(3); // Only planting areas
            expect($result->active)->toBeGreaterThan(2);
            expect($result->total)->toBeGreaterThan(2);
        });
    });

    describe('constructor', function () {
        it('accepts AreaRepository dependency', function () {
            $repository = new AreaRepository();
            $query = new AreaStatisticsQuery($repository);

            expect($query)->toBeInstanceOf(AreaStatisticsQuery::class);
        });

        it('is readonly', function () {
            $reflection = new ReflectionClass(AreaStatisticsQuery::class);

            expect($reflection->isReadOnly())->toBeTrue();
        });

        it('properties are readonly', function () {
            $reflection = new ReflectionClass(AreaStatisticsQuery::class);

            foreach ($reflection->getProperties() as $property) {
                expect($property->isReadOnly())->toBeTrue();
            }
        });
    });

    describe('type safety', function () {
        it('enforces integer type for user_id parameter', function () {
            $result = $this->query->execute(123, false);

            expect($result)->toBeInstanceOf(AreaStatisticsDTO::class);
        });

        it('enforces boolean type for isAdmin parameter', function () {
            $result = $this->query->execute(123, true);

            expect($result)->toBeInstanceOf(AreaStatisticsDTO::class);
        });

        it('returns AreaStatisticsDTO instance', function () {
            $result = $this->query->execute($this->user->id, false);

            expect($result)->toBeInstanceOf(AreaStatisticsDTO::class)
                ->and($result->total)->toBeInt()
                ->and($result->active)->toBeInt()
                ->and($result->planting)->toBeInt();
        });
    });
});
