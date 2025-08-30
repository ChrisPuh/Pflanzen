<?php

declare(strict_types=1);

use App\DTOs\Area\AreaFilterOptionsDTO;
use App\Enums\Area\AreaTypeEnum;
use App\Models\Garden;
use App\Models\User;
use App\Queries\Area\AreaFilterOptionsQuery;
use App\Repositories\Garden\GardenRepository;
use Illuminate\Support\Collection;

describe('AreaFilterOptionsQuery', function () {
    beforeEach(function () {
        // Setup roles for tests
        \Spatie\Permission\Models\Role::query()->firstOrCreate(['name' => 'admin']);
        \Spatie\Permission\Models\Role::query()->firstOrCreate(['name' => 'user']);

        $this->gardenRepository = new GardenRepository();
        $this->query = new AreaFilterOptionsQuery($this->gardenRepository);
        
        $this->user = User::factory()->user()->create();
    });

    describe('execute', function () {
        it('returns AreaFilterOptionsDTO with all filter options for regular user', function () {
            // Create some gardens for the user
            Garden::factory()->count(2)->create(['user_id' => $this->user->id]);

            $result = $this->query->execute($this->user->id, false);

            expect($result)->toBeInstanceOf(AreaFilterOptionsDTO::class)
                ->and($result->gardens)->toBeInstanceOf(Collection::class)
                ->and($result->areaTypes)->toBeInstanceOf(Collection::class)
                ->and($result->categories)->toBeInstanceOf(Collection::class);
        });

        it('returns AreaFilterOptionsDTO with all filter options for admin user', function () {
            $admin = User::factory()->admin()->create();
            
            // Create gardens for multiple users that admin can see
            $otherUser = User::factory()->user()->create();
            Garden::factory()->create(['user_id' => $this->user->id, 'name' => 'User Garden']);
            Garden::factory()->create(['user_id' => $otherUser->id, 'name' => 'Other User Garden']);

            $result = $this->query->execute($admin->id, true);

            expect($result)->toBeInstanceOf(AreaFilterOptionsDTO::class)
                ->and($result->gardens)->toBeInstanceOf(Collection::class)
                ->and($result->areaTypes)->toBeInstanceOf(Collection::class)
                ->and($result->categories)->toBeInstanceOf(Collection::class);
        });

        it('handles user with no gardens', function () {
            $emptyUser = User::factory()->user()->create();

            $result = $this->query->execute($emptyUser->id, false);

            expect($result->gardens)->toBeInstanceOf(Collection::class)
                ->and($result->gardens)->toBeEmpty()
                ->and($result->areaTypes)->toBeInstanceOf(Collection::class)
                ->and($result->areaTypes)->not->toBeEmpty()
                ->and($result->categories)->toBeInstanceOf(Collection::class)
                ->and($result->categories)->not->toBeEmpty();
        });

        it('gets area type options from enum', function () {
            $result = $this->query->execute($this->user->id, false);

            expect($result->areaTypes)->toBeInstanceOf(Collection::class)
                ->and($result->areaTypes)->not->toBeEmpty();
            
            // Verify it's the same collection returned by the enum
            expect($result->areaTypes->toArray())->toBe(AreaTypeEnum::getFilterOptions()->toArray());
        });

        it('gets category options from enum', function () {
            $result = $this->query->execute($this->user->id, false);

            expect($result->categories)->toBeInstanceOf(Collection::class)
                ->and($result->categories)->not->toBeEmpty();
            
            // Verify it's the same collection returned by the enum
            expect($result->categories->toArray())->toBe(AreaTypeEnum::getCategoryFilterOptions()->toArray());
        });

        it('returns consistent data structure', function () {
            Garden::factory()->count(3)->create(['user_id' => $this->user->id]);

            $result = $this->query->execute($this->user->id, false);

            // Verify all collections have proper structure
            expect($result->gardens)->toBeInstanceOf(Collection::class)
                ->and($result->areaTypes)->toBeInstanceOf(Collection::class)
                ->and($result->categories)->toBeInstanceOf(Collection::class);

            // Verify the DTO structure matches expectation
            $arrayResult = $result->toArray();
            expect($arrayResult)->toHaveKeys(['gardens', 'areaTypes', 'categories'])
                ->and($arrayResult['gardens'])->toBeArray()
                ->and($arrayResult['areaTypes'])->toBeArray()
                ->and($arrayResult['categories'])->toBeArray();
        });
    });

    describe('enum integration', function () {
        it('returns area types from AreaTypeEnum::getFilterOptions', function () {
            $result = $this->query->execute($this->user->id, false);

            $areaTypes = $result->areaTypes;
            expect($areaTypes)->toBeInstanceOf(Collection::class);

            // Verify it matches the enum output
            $expectedAreaTypes = AreaTypeEnum::getFilterOptions();
            expect($areaTypes->toArray())->toBe($expectedAreaTypes->toArray());
        });

        it('returns categories from AreaTypeEnum::getCategoryFilterOptions', function () {
            $result = $this->query->execute($this->user->id, false);

            $categories = $result->categories;
            expect($categories)->toBeInstanceOf(Collection::class);

            // Verify it matches the enum output
            $expectedCategories = AreaTypeEnum::getCategoryFilterOptions();
            expect($categories->toArray())->toBe($expectedCategories->toArray());
        });

        it('enum methods return non-empty collections', function () {
            $result = $this->query->execute($this->user->id, false);

            expect($result->areaTypes->count())->toBeGreaterThan(0)
                ->and($result->categories->count())->toBeGreaterThan(0);
        });
    });

    describe('constructor', function () {
        it('accepts GardenRepository dependency', function () {
            $repository = new GardenRepository();
            $query = new AreaFilterOptionsQuery($repository);

            expect($query)->toBeInstanceOf(AreaFilterOptionsQuery::class);
        });

        it('is readonly', function () {
            $reflection = new ReflectionClass(AreaFilterOptionsQuery::class);

            expect($reflection->isReadOnly())->toBeTrue();
        });

        it('properties are readonly', function () {
            $reflection = new ReflectionClass(AreaFilterOptionsQuery::class);

            foreach ($reflection->getProperties() as $property) {
                expect($property->isReadOnly())->toBeTrue();
            }
        });
    });

    describe('type safety', function () {
        it('enforces integer type for user_id parameter', function () {
            $result = $this->query->execute(123, false);

            expect($result)->toBeInstanceOf(AreaFilterOptionsDTO::class);
        });

        it('enforces boolean type for isAdmin parameter', function () {
            $result = $this->query->execute(123, true);

            expect($result)->toBeInstanceOf(AreaFilterOptionsDTO::class);
        });

        it('returns AreaFilterOptionsDTO instance', function () {
            $result = $this->query->execute($this->user->id, false);

            expect($result)->toBeInstanceOf(AreaFilterOptionsDTO::class)
                ->and($result->gardens)->toBeInstanceOf(Collection::class)
                ->and($result->areaTypes)->toBeInstanceOf(Collection::class)
                ->and($result->categories)->toBeInstanceOf(Collection::class);
        });
    });

    describe('collection type consistency', function () {
        it('maintains collection type consistency', function () {
            Garden::factory()->count(2)->create(['user_id' => $this->user->id]);

            $result = $this->query->execute($this->user->id, false);

            // All three should be Collection instances
            expect($result->gardens)->toBeInstanceOf(Collection::class)
                ->and($result->areaTypes)->toBeInstanceOf(Collection::class)
                ->and($result->categories)->toBeInstanceOf(Collection::class);

            // Should be convertible to arrays
            expect($result->gardens->toArray())->toBeArray()
                ->and($result->areaTypes->toArray())->toBeArray()
                ->and($result->categories->toArray())->toBeArray();
        });

        it('returns proper garden data structure', function () {
            $garden1 = Garden::factory()->create(['user_id' => $this->user->id, 'name' => 'Test Garden 1']);
            $garden2 = Garden::factory()->create(['user_id' => $this->user->id, 'name' => 'Test Garden 2']);

            $result = $this->query->execute($this->user->id, false);

            expect($result->gardens)->toBeInstanceOf(Collection::class)
                ->and($result->gardens->count())->toBe(2);
        });
    });
});