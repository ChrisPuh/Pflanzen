<?php

declare(strict_types=1);

use App\Models\Garden;
use App\Models\Plant;
use App\Models\User;
use App\Services\GardenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

describe('GardenService', function () {
    beforeEach(function () {
        $this->service = app(GardenService::class);
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->admin->assignRole($this->adminRole);
    });

    describe('getGardensForUser', function () {
        it('returns paginated gardens for regular user', function () {
            $userGardens = Garden::factory()->for($this->user)->count(3)->create();
            $otherGardens = Garden::factory()->count(2)->create();

            $result = $this->service->getGardensForUser($this->user, false, 10);

            expect($result->total())->toBe(3);
            expect($result->items())->toHaveCount(3);
            
            $resultIds = $result->pluck('id')->toArray();
            foreach ($userGardens as $garden) {
                expect($resultIds)->toContain($garden->id);
            }
        });

        it('returns all gardens for admin user', function () {
            $userGardens = Garden::factory()->for($this->user)->count(2)->create();
            $otherGardens = Garden::factory()->count(3)->create();

            $result = $this->service->getGardensForUser($this->admin, true, 10);

            expect($result->total())->toBe(5);
        });

        it('loads required relationships', function () {
            Garden::factory()->for($this->user)->create();
            
            $result = $this->service->getGardensForUser($this->user, false, 10);
            $garden = $result->items()[0];

            expect($garden->relationLoaded('user'))->toBeTrue();
            expect($garden->relationLoaded('plants'))->toBeTrue();
        });

        it('orders gardens by latest', function () {
            $olderGarden = Garden::factory()->for($this->user)->create([
                'created_at' => now()->subDays(2),
            ]);
            $newerGarden = Garden::factory()->for($this->user)->create([
                'created_at' => now()->subDay(),
            ]);

            $result = $this->service->getGardensForUser($this->user, false, 10);
            $gardens = $result->items();

            expect($gardens[0]->id)->toBe($newerGarden->id);
            expect($gardens[1]->id)->toBe($olderGarden->id);
        });

        it('respects per page parameter', function () {
            Garden::factory()->for($this->user)->count(5)->create();

            $result = $this->service->getGardensForUser($this->user, false, 3);

            expect($result->perPage())->toBe(3);
            expect($result->items())->toHaveCount(3);
            expect($result->hasMorePages())->toBeTrue();
        });
    });

    describe('getGardenForDisplay', function () {
        it('loads garden with relationships', function () {
            $garden = Garden::factory()->for($this->user)->create();
            $plant = Plant::factory()->create();
            $garden->plants()->attach($plant);

            $result = $this->service->getGardenForDisplay($garden);

            expect($result->relationLoaded('user'))->toBeTrue();
            expect($result->relationLoaded('plants'))->toBeTrue();
            expect($result->plants)->toHaveCount(1);
            expect($result->user->id)->toBe($this->user->id);
        });
    });

    describe('getGardenStatistics', function () {
        it('calculates correct statistics for regular user', function () {
            // Create gardens for the user
            Garden::factory()->for($this->user)->count(2)->create(['is_active' => true]);
            Garden::factory()->for($this->user)->count(1)->create(['is_active' => false]);
            
            // Create plants and attach to gardens
            $garden1 = Garden::factory()->for($this->user)->create();
            $plant1 = Plant::factory()->create();
            $plant2 = Plant::factory()->create();
            $garden1->plants()->attach([$plant1->id, $plant2->id]);

            // Create other user's gardens (should not be counted)
            Garden::factory()->count(2)->create();

            $stats = $this->service->getGardenStatistics($this->user, false);

            expect($stats['total'])->toBe(4); // 2 active + 1 inactive + 1 garden1 = 4
            expect($stats['active'])->toBe(3); // 2 + 1 (garden1 is active by default) = 3
            expect($stats['total_plants'])->toBe(2); // 2 plants in garden1
            expect($stats['by_type'])->toBeArray();
        });

        it('calculates correct statistics for admin user', function () {
            // Create gardens for different users
            Garden::factory()->for($this->user)->count(2)->create(['is_active' => true]);
            Garden::factory()->count(3)->create(['is_active' => true]);
            Garden::factory()->count(1)->create(['is_active' => false]);

            $stats = $this->service->getGardenStatistics($this->admin, true);

            expect($stats['total'])->toBe(6);
            expect($stats['active'])->toBe(5);
            expect($stats['by_type'])->toBeArray();
        });

        it('groups gardens by type correctly', function () {
            Garden::factory()->for($this->user)->count(2)->create(['type' => 'vegetable_garden']);

            $stats = $this->service->getGardenStatistics($this->user, false);

            expect($stats['by_type'])->toBeArray();
            expect($stats['by_type'])->toHaveKey('Nutzgarten');
            expect($stats['by_type']['Nutzgarten'])->toBe(2);
        });
    });

    describe('getAvailableGardenTypes', function () {
        it('returns all garden type enum cases', function () {
            $types = $this->service->getAvailableGardenTypes();

            expect($types)->toBeArray();
            expect($types)->not->toBeEmpty();
            
            foreach ($types as $type) {
                expect($type)->toBeInstanceOf(\App\Enums\Garden\GardenTypeEnum::class);
            }
        });
    });

    describe('getRecentlyActiveGardens', function () {
        it('returns recently active gardens for user', function () {
            // Create active gardens with different update times
            $recentGarden = Garden::factory()->for($this->user)->create([
                'is_active' => true,
                'updated_at' => now()->subHour(),
            ]);
            $olderGarden = Garden::factory()->for($this->user)->create([
                'is_active' => true,
                'updated_at' => now()->subDays(2),
            ]);
            
            // Create inactive garden (should not be returned)
            Garden::factory()->for($this->user)->create(['is_active' => false]);
            
            // Create other user's gardens (should not be returned)
            Garden::factory()->create(['is_active' => true]);

            $result = $this->service->getRecentlyActiveGardens($this->user, 5);

            expect($result)->toHaveCount(2);
            expect($result->first()->id)->toBe($recentGarden->id);
            expect($result->last()->id)->toBe($olderGarden->id);
        });

        it('respects the limit parameter', function () {
            Garden::factory()->for($this->user)->count(10)->create(['is_active' => true]);

            $result = $this->service->getRecentlyActiveGardens($this->user, 3);

            expect($result)->toHaveCount(3);
        });

        it('loads plants relationship', function () {
            $garden = Garden::factory()->for($this->user)->create(['is_active' => true]);
            $plant = Plant::factory()->create();
            $garden->plants()->attach($plant);

            $result = $this->service->getRecentlyActiveGardens($this->user, 5);

            expect($result->first()->relationLoaded('plants'))->toBeTrue();
        });
    });

    describe('getGardensByLocation', function () {
        it('returns gardens filtered by city', function () {
            $berlinGarden = Garden::factory()->for($this->user)->create(['city' => 'Berlin']);
            $hamburgGarden = Garden::factory()->for($this->user)->create(['city' => 'Hamburg']);
            $munichGarden = Garden::factory()->for($this->user)->create(['city' => 'Munich']);

            $result = $this->service->getGardensByLocation($this->user, 'Berlin');

            expect($result)->toHaveCount(1);
            expect($result->first()->id)->toBe($berlinGarden->id);
        });

        it('returns empty collection when no gardens in city', function () {
            Garden::factory()->for($this->user)->create(['city' => 'Berlin']);

            $result = $this->service->getGardensByLocation($this->user, 'Hamburg');

            expect($result)->toHaveCount(0);
        });

        it('only returns user\'s own gardens', function () {
            $userGarden = Garden::factory()->for($this->user)->create(['city' => 'Berlin']);
            $otherGarden = Garden::factory()->create(['city' => 'Berlin']);

            $result = $this->service->getGardensByLocation($this->user, 'Berlin');

            expect($result)->toHaveCount(1);
            expect($result->first()->id)->toBe($userGarden->id);
        });
    });

    describe('getDashboardSummary', function () {
        it('returns correct dashboard summary', function () {
            // Create gardens with different states
            Garden::factory()->for($this->user)->count(2)->create(['is_active' => true, 'size_sqm' => 0]);
            Garden::factory()->for($this->user)->create(['is_active' => false, 'size_sqm' => 0]);
            
            // Create garden with size for largest garden test
            $largestGarden = Garden::factory()->for($this->user)->create(['size_sqm' => 100.5]);
            
            // Add plants to garden
            $plant1 = Plant::factory()->create();
            $plant2 = Plant::factory()->create();
            $largestGarden->plants()->attach([$plant1->id, $plant2->id]);

            $summary = $this->service->getDashboardSummary($this->user);

            expect($summary['gardens_count'])->toBe(4); // 2 active + 1 inactive + 1 largest = 4
            expect($summary['active_gardens'])->toBe(3); // 2 + 1 largest (active by default) = 3
            expect($summary['total_plants'])->toBe(2);
            expect($summary['largest_garden']->id)->toBe($largestGarden->id);
        });

        it('handles user with no gardens', function () {
            $summary = $this->service->getDashboardSummary($this->user);

            expect($summary['gardens_count'])->toBe(0);
            expect($summary['active_gardens'])->toBe(0);
            expect($summary['total_plants'])->toBe(0);
            expect($summary['largest_garden'])->toBeNull();
        });
    });

    describe('searchGardens', function () {
        it('searches gardens by name', function () {
            $matchingGarden = Garden::factory()->for($this->user)->create(['name' => 'Rose Garden']);
            $nonMatchingGarden = Garden::factory()->for($this->user)->create(['name' => 'Vegetable Patch']);

            $result = $this->service->searchGardens($this->user, 'Rose');

            expect($result)->toHaveCount(1);
            expect($result->first()->id)->toBe($matchingGarden->id);
        });

        it('searches gardens by description', function () {
            $matchingGarden = Garden::factory()->for($this->user)->create([
                'description' => 'A beautiful rose garden',
            ]);
            $nonMatchingGarden = Garden::factory()->for($this->user)->create([
                'description' => 'Vegetable growing space',
            ]);

            $result = $this->service->searchGardens($this->user, 'beautiful');

            expect($result)->toHaveCount(1);
            expect($result->first()->id)->toBe($matchingGarden->id);
        });

        it('searches gardens by location and city', function () {
            $locationMatch = Garden::factory()->for($this->user)->create(['location' => 'Backyard']);
            $cityMatch = Garden::factory()->for($this->user)->create(['city' => 'Berlin']);
            $noMatch = Garden::factory()->for($this->user)->create([
                'location' => 'Front yard',
                'city' => 'Hamburg',
            ]);

            $locationResult = $this->service->searchGardens($this->user, 'Backyard');
            $cityResult = $this->service->searchGardens($this->user, 'Berlin');

            expect($locationResult)->toHaveCount(1);
            expect($locationResult->first()->id)->toBe($locationMatch->id);
            
            expect($cityResult)->toHaveCount(1);
            expect($cityResult->first()->id)->toBe($cityMatch->id);
        });

        it('returns all matching gardens for admin', function () {
            $userGarden = Garden::factory()->for($this->user)->create(['name' => 'Rose Garden']);
            $otherGarden = Garden::factory()->create(['name' => 'Rose Paradise']);

            $userResult = $this->service->searchGardens($this->user, 'Rose', false);
            $adminResult = $this->service->searchGardens($this->admin, 'Rose', true);

            expect($userResult)->toHaveCount(1);
            expect($adminResult)->toHaveCount(2);
        });

        it('loads relationships', function () {
            $garden = Garden::factory()->for($this->user)->create(['name' => 'Test Garden']);
            $plant = Plant::factory()->create();
            $garden->plants()->attach($plant);

            $result = $this->service->searchGardens($this->user, 'Test');

            expect($result->first()->relationLoaded('user'))->toBeTrue();
            expect($result->first()->relationLoaded('plants'))->toBeTrue();
        });

        it('orders results by latest', function () {
            $olderGarden = Garden::factory()->for($this->user)->create([
                'name' => 'Test Garden 1',
                'created_at' => now()->subDays(2),
            ]);
            $newerGarden = Garden::factory()->for($this->user)->create([
                'name' => 'Test Garden 2',
                'created_at' => now()->subDay(),
            ]);

            $result = $this->service->searchGardens($this->user, 'Test');

            expect($result->first()->id)->toBe($newerGarden->id);
            expect($result->last()->id)->toBe($olderGarden->id);
        });
    });
});