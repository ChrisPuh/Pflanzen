<?php

declare(strict_types=1);

use App\DTOs\Area\AreaStatisticsDTO;

describe('AreaStatisticsDTO', function () {
    it('creates instance with all required parameters', function () {
        $dto = new AreaStatisticsDTO(
            total: 100,
            active: 85,
            planting: 70
        );

        expect($dto->total)->toBe(100)
            ->and($dto->active)->toBe(85)
            ->and($dto->planting)->toBe(70)
            ->and($dto->archived)->toBe(0)
            ->and($dto->buildings)->toBe(0)
            ->and($dto->waterFeatures)->toBe(0);
    });

    it('creates instance with all parameters', function () {
        $dto = new AreaStatisticsDTO(
            total: 150,
            active: 120,
            planting: 80,
            archived: 15,
            buildings: 10,
            waterFeatures: 5
        );

        expect($dto->total)->toBe(150)
            ->and($dto->active)->toBe(120)
            ->and($dto->planting)->toBe(80)
            ->and($dto->archived)->toBe(15)
            ->and($dto->buildings)->toBe(10)
            ->and($dto->waterFeatures)->toBe(5);
    });

    describe('fromArray', function () {
        it('creates instance from array with all data', function () {
            $data = [
                'total' => 200,
                'active' => 180,
                'planting' => 150,
                'archived' => 20,
                'buildings' => 15,
                'waterFeatures' => 10,
            ];

            $dto = AreaStatisticsDTO::fromArray($data);

            expect($dto->total)->toBe(200)
                ->and($dto->active)->toBe(180)
                ->and($dto->planting)->toBe(150)
                ->and($dto->archived)->toBe(20)
                ->and($dto->buildings)->toBe(15)
                ->and($dto->waterFeatures)->toBe(10);
        });

        it('creates instance from array with minimal required data', function () {
            $data = [
                'total' => 50,
                'active' => 40,
                'planting' => 30,
            ];

            $dto = AreaStatisticsDTO::fromArray($data);

            expect($dto->total)->toBe(50)
                ->and($dto->active)->toBe(40)
                ->and($dto->planting)->toBe(30)
                ->and($dto->archived)->toBe(0)
                ->and($dto->buildings)->toBe(0)
                ->and($dto->waterFeatures)->toBe(0);
        });

        it('creates instance from array with partial optional data', function () {
            $data = [
                'total' => 75,
                'active' => 60,
                'planting' => 45,
                'archived' => 10,
                'buildings' => 5,
            ];

            $dto = AreaStatisticsDTO::fromArray($data);

            expect($dto->total)->toBe(75)
                ->and($dto->active)->toBe(60)
                ->and($dto->planting)->toBe(45)
                ->and($dto->archived)->toBe(10)
                ->and($dto->buildings)->toBe(5)
                ->and($dto->waterFeatures)->toBe(0);
        });
    });

    describe('toArray', function () {
        it('converts to array with all properties', function () {
            $dto = new AreaStatisticsDTO(
                total: 300,
                active: 250,
                planting: 200,
                archived: 25,
                buildings: 20,
                waterFeatures: 15
            );

            $array = $dto->toArray();

            expect($array)->toBe([
                'total' => 300,
                'active' => 250,
                'planting' => 200,
                'archived' => 25,
                'buildings' => 20,
                'waterFeatures' => 15,
            ]);
        });

        it('converts to array with default optional values', function () {
            $dto = new AreaStatisticsDTO(
                total: 80,
                active: 65,
                planting: 50
            );

            $array = $dto->toArray();

            expect($array)->toBe([
                'total' => 80,
                'active' => 65,
                'planting' => 50,
                'archived' => 0,
                'buildings' => 0,
                'waterFeatures' => 0,
            ]);
        });

        it('maintains integer types in array', function () {
            $dto = new AreaStatisticsDTO(
                total: 100,
                active: 90,
                planting: 80
            );

            $array = $dto->toArray();

            expect($array['total'])->toBeInt()
                ->and($array['active'])->toBeInt()
                ->and($array['planting'])->toBeInt()
                ->and($array['archived'])->toBeInt()
                ->and($array['buildings'])->toBeInt()
                ->and($array['waterFeatures'])->toBeInt();
        });
    });

    describe('data consistency', function () {
        it('maintains data integrity through fromArray and toArray', function () {
            $originalData = [
                'total' => 500,
                'active' => 450,
                'planting' => 400,
                'archived' => 30,
                'buildings' => 25,
                'waterFeatures' => 20,
            ];

            $dto = AreaStatisticsDTO::fromArray($originalData);
            $resultData = $dto->toArray();

            expect($resultData)->toBe($originalData);
        });

        it('handles zero values correctly', function () {
            $data = [
                'total' => 0,
                'active' => 0,
                'planting' => 0,
                'archived' => 0,
                'buildings' => 0,
                'waterFeatures' => 0,
            ];

            $dto = AreaStatisticsDTO::fromArray($data);

            expect($dto->total)->toBe(0)
                ->and($dto->active)->toBe(0)
                ->and($dto->planting)->toBe(0)
                ->and($dto->archived)->toBe(0)
                ->and($dto->buildings)->toBe(0)
                ->and($dto->waterFeatures)->toBe(0);

            expect($dto->toArray())->toBe($data);
        });
    });

    it('is readonly', function () {
        $dto = new AreaStatisticsDTO(total: 1, active: 1, planting: 1);
        $reflection = new ReflectionClass($dto);

        expect($reflection->isReadOnly())->toBeTrue();
    });

    it('properties are readonly', function () {
        $dto = new AreaStatisticsDTO(total: 1, active: 1, planting: 1);
        $reflection = new ReflectionClass($dto);

        foreach ($reflection->getProperties() as $property) {
            expect($property->isReadOnly())->toBeTrue();
        }
    });
});