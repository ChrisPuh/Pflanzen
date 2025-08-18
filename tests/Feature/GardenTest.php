<?php

declare(strict_types=1);

use App\Enums\Garden\GardenTypeEnum;
use App\Models\Garden;
use App\Models\User;

describe('Garden Model', function () {
    describe('Factory', function () {
        it('creates a garden with valid data', function () {
            $garden = Garden::factory()->create();

            expect($garden)
                ->toBeInstanceOf(Garden::class)
                ->and($garden->name)->toBeString()
                ->and($garden->type)->toBeInstanceOf(GardenTypeEnum::class)
                ->and($garden->user_id)->toBeInt()
                ->and($garden->country)->toBe('DE')
                ->and($garden->is_active)->toBeBool();
        });

        it('can create specific garden types', function () {
            $vegetableGarden = Garden::factory()->vegetableGarden()->create();
            $flowerGarden = Garden::factory()->flowerGarden()->create();
            $herbGarden = Garden::factory()->herbGarden()->create();
            $balconyGarden = Garden::factory()->balconyGarden()->create();

            expect($vegetableGarden->type)->toBe(GardenTypeEnum::VegetableGarden)
                ->and($flowerGarden->type)->toBe(GardenTypeEnum::FlowerGarden)
                ->and($herbGarden->type)->toBe(GardenTypeEnum::HerbGarden)
                ->and($balconyGarden->type)->toBe(GardenTypeEnum::BalconyGarden);
        });

        it('can create gardens with specific states', function () {
            $largeGarden = Garden::factory()->large()->create();
            $smallGarden = Garden::factory()->small()->create();
            $inactiveGarden = Garden::factory()->inactive()->create();

            expect($largeGarden->size_sqm)->toBeGreaterThan(200)
                ->and($smallGarden->size_sqm)->toBeLessThan(10)
                ->and($inactiveGarden->is_active)->toBeFalse();
        });
    });

    describe('Relationships', function () {
        it('belongs to a user', function () {
            $user = User::factory()->create();
            $garden = Garden::factory()->for($user)->create();

            expect($garden->user)->toBeInstanceOf(User::class)
                ->and($garden->user->id)->toBe($user->id);
        });

        it('can have many plants', function () {
            $garden = Garden::factory()->create();

            expect($garden->plants())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
        });
    });

    describe('Scopes', function () {
        beforeEach(function () {
            $this->user = User::factory()->create();
            $this->activeGarden = Garden::factory()->for($this->user)->create(['is_active' => true]);
            $this->inactiveGarden = Garden::factory()->for($this->user)->create(['is_active' => false]);
            $this->otherUserGarden = Garden::factory()->create(['is_active' => true]);
        });

        it('filters active gardens', function () {
            $activeGardens = Garden::active()->get();

            expect($activeGardens)->toHaveCount(2)
                ->and($activeGardens->pluck('id'))->toContain($this->activeGarden->id)
                ->and($activeGardens->pluck('id'))->toContain($this->otherUserGarden->id)
                ->and($activeGardens->pluck('id'))->not->toContain($this->inactiveGarden->id);
        });

        it('filters gardens for specific user', function () {
            $userGardens = Garden::forUser($this->user)->get();

            expect($userGardens)->toHaveCount(2)
                ->and($userGardens->pluck('id'))->toContain($this->activeGarden->id)
                ->and($userGardens->pluck('id'))->toContain($this->inactiveGarden->id)
                ->and($userGardens->pluck('id'))->not->toContain($this->otherUserGarden->id);
        });

        it('filters gardens by type', function () {
            $vegetableGarden = Garden::factory()->vegetableGarden()->create();
            $flowerGarden = Garden::factory()->flowerGarden()->create();

            $vegetableGardens = Garden::byType(GardenTypeEnum::VegetableGarden)->get();

            expect($vegetableGardens)->toHaveCount(1)
                ->and($vegetableGardens->first()->id)->toBe($vegetableGarden->id);
        });

        it('filters gardens by location', function () {
            $berlinGarden = Garden::factory()->create([
                'city' => 'Berlin',
                'location' => 'Balkon',
                'postal_code' => '10115',
            ]);

            $municGarden = Garden::factory()->create([
                'city' => 'München',
                'location' => 'Garten',
                'postal_code' => '80331',
            ]);

            $berlinResults = Garden::byLocation('Berlin')->get();
            $balkonResults = Garden::byLocation('Balkon')->get();
            $postalResults = Garden::byLocation('10115')->get();

            expect($berlinResults->pluck('id'))->toContain($berlinGarden->id)
                ->and($balkonResults->pluck('id'))->toContain($berlinGarden->id)
                ->and($postalResults->pluck('id'))->toContain($berlinGarden->id)
                ->and($berlinResults->pluck('id'))->not->toContain($municGarden->id);
        });
    });

    describe('Attributes and Methods', function () {
        it('formats size correctly', function () {
            $gardenWithSize = Garden::factory()->create(['size_sqm' => 123.45]);
            $gardenWithoutSize = Garden::factory()->create(['size_sqm' => null]);

            expect($gardenWithSize->formatted_size)->toBe('123,45 m²')
                ->and($gardenWithoutSize->formatted_size)->toBe('Größe nicht angegeben');
        });

        it('calculates age in years', function () {
            $gardenWithAge = Garden::factory()->create(['established_at' => now()->subYears(3)]);
            $gardenWithoutAge = Garden::factory()->create(['established_at' => null]);

            expect($gardenWithAge->age_in_years)->toBe(3)
                ->and($gardenWithoutAge->age_in_years)->toBeNull();
        });

        it('handles coordinates correctly', function () {
            $gardenWithCoords = Garden::factory()->withCoordinates(52.5200, 13.4050)->create();
            $gardenWithoutCoords = Garden::factory()->withoutCoordinates()->create();

            expect($gardenWithCoords->hasCoordinates())->toBeTrue()
                ->and($gardenWithCoords->getLatitude())->toBe(52.5200)
                ->and($gardenWithCoords->getLongitude())->toBe(13.4050)
                ->and($gardenWithoutCoords->hasCoordinates())->toBeFalse()
                ->and($gardenWithoutCoords->getLatitude())->toBeNull()
                ->and($gardenWithoutCoords->getLongitude())->toBeNull();
        });

        it('sets coordinates correctly', function () {
            $garden = Garden::factory()->create();
            $garden->setCoordinates(48.1351, 11.5820);

            expect($garden->hasCoordinates())->toBeTrue()
                ->and($garden->getLatitude())->toBe(48.1351)
                ->and($garden->getLongitude())->toBe(11.5820);
        });

        it('formats full location correctly', function () {
            $fullLocationGarden = Garden::factory()->create([
                'location' => 'Balkon',
                'postal_code' => '10115',
                'city' => 'Berlin',
            ]);

            $partialLocationGarden = Garden::factory()->create([
                'location' => null,
                'postal_code' => '80331',
                'city' => 'München',
            ]);

            $noLocationGarden = Garden::factory()->create([
                'location' => null,
                'postal_code' => null,
                'city' => null,
            ]);

            expect($fullLocationGarden->full_location)->toBe('Balkon, 10115, Berlin')
                ->and($partialLocationGarden->full_location)->toBe('80331, München')
                ->and($noLocationGarden->full_location)->toBe('Standort nicht angegeben');
        });
    });

    describe('Validation and Casting', function () {
        it('casts type to enum', function () {
            $garden = Garden::factory()->create(['type' => GardenTypeEnum::VegetableGarden]);

            expect($garden->type)->toBeInstanceOf(GardenTypeEnum::class)
                ->and($garden->type)->toBe(GardenTypeEnum::VegetableGarden);
        });

        it('casts coordinates to array', function () {
            $garden = Garden::factory()->withCoordinates(52.5200, 13.4050)->create();

            expect($garden->coordinates)->toBeArray()
                ->and($garden->coordinates)->toHaveKey('lat')
                ->and($garden->coordinates)->toHaveKey('lng');
        });

        it('casts size_sqm to decimal', function () {
            $garden = Garden::factory()->create(['size_sqm' => 123.456]);

            expect($garden->size_sqm)->toBeFloat();
        });

        it('casts is_active to boolean', function () {
            $garden = Garden::factory()->create(['is_active' => 1]);

            expect($garden->is_active)->toBeBool()
                ->and($garden->is_active)->toBeTrue();
        });

        it('casts established_at to date', function () {
            $garden = Garden::factory()->create(['established_at' => '2020-01-01']);

            expect($garden->established_at)->toBeInstanceOf(Carbon\Carbon::class);
        });
    });
});

describe('GardenType Enum', function () {
    it('has correct values', function () {
        $expectedValues = [
            'vegetable_garden',
            'flower_garden',
            'herb_garden',
            'fruit_garden',
            'mixed_garden',
            'greenhouse_garden',
            'container_garden',
            'rooftop_garden',
            'balcony_garden',
            'indoor_garden',
        ];

        $actualValues = GardenTypeEnum::values();

        expect($actualValues)->toBe($expectedValues);
    });

    it('provides correct labels', function () {
        expect(GardenTypeEnum::VegetableGarden->getLabel())->toBe('Nutzgarten')
            ->and(GardenTypeEnum::FlowerGarden->getLabel())->toBe('Blumengarten')
            ->and(GardenTypeEnum::HerbGarden->getLabel())->toBe('Kräutergarten');
    });

    it('provides descriptions', function () {
        expect(GardenTypeEnum::VegetableGarden->description())
            ->toContain('Gemüse')
            ->and(GardenTypeEnum::FlowerGarden->description())
            ->toContain('Blumen');
    });

    it('provides options collection', function () {
        $options = GardenTypeEnum::options();

        expect($options)->toBeInstanceOf(Illuminate\Support\Collection::class)
            ->and($options->first())->toHaveKeys(['value', 'label', 'description']);
    });
});
