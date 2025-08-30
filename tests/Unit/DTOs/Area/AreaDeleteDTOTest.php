<?php

declare(strict_types=1);

use App\DTOs\Area\AreaDeleteDTO;
use App\DTOs\Shared\Contracts\WritableDTOInterface;

describe('AreaDeleteDTO', function () {
    it('creates instance with default values', function () {
        $dto = new AreaDeleteDTO(areaId: 1, name: 'test area');

        expect($dto->isActive)->toBeFalse();
    });

    it('creates instance with custom values', function () {
        $dto = new AreaDeleteDTO(areaId: 1, name: 'test area', isActive: true);

        expect($dto->isActive)->toBeTrue();
    });

    it('creates instance from validated request with is_active false', function () {
        $validated = [
            'is_active' => false,
            'id' => 1,
            'name' => 'test area',
        ];

        $dto = AreaDeleteDTO::fromValidatedRequest($validated);

        expect($dto->isActive)->toBeFalse();
    });

    it('creates instance from validated request with is_active true', function () {
        $validated = [
            'is_active' => true,
            'id' => 1,
            'name' => 'test area',
        ];

        $dto = AreaDeleteDTO::fromValidatedRequest($validated);

        expect($dto->isActive)->toBeTrue();
    });

    it('handles type casting from validated request', function () {
        $validated = [
            'is_active' => 1, // Will be cast to bool
            'id' => 1,
            'name' => 'test area',
        ];

        $dto = AreaDeleteDTO::fromValidatedRequest($validated);

        expect($dto->isActive)->toBeTrue()
            ->and($dto->isActive)->toBeBool();
    });

    it('handles string values from validated request', function () {
        $validated = [
            'is_active' => 'false', // String that evaluates to true in PHP
            'id' => 1,
            'name' => 'test area',
        ];

        $dto = AreaDeleteDTO::fromValidatedRequest($validated);

        // Note: 'false' string is truthy in PHP
        expect($dto->isActive)->toBeTrue();
    });

    describe('toModelData', function () {
        it('converts to model data with is_active false', function () {
            $dto = new AreaDeleteDTO(areaId: 1, name: 'test area', isActive: false);

            $modelData = $dto->toModelData();

            expect($modelData)->toBe([
                'id' => 1,
                'is_active' => false,
            ]);
        });

        it('converts to model data with is_active true', function () {
            $dto = new AreaDeleteDTO(areaId: 1, name: 'test area', isActive: true);

            $modelData = $dto->toModelData();

            expect($modelData)->toBe([
                'id' => 1,
                'is_active' => true,
            ]);
        });

        it('maintains boolean type in model data', function () {
            $dto = new AreaDeleteDTO(areaId: 1, name: 'test area', isActive: false);

            $modelData = $dto->toModelData();

            expect($modelData['is_active'])->toBeBool()
                ->and($modelData['is_active'])->toBeFalse();
        });
    });

    it('implements WritableDTOInterface', function () {
        $dto = new AreaDeleteDTO(areaId: 1, name: 'test area');

        expect($dto)->toBeInstanceOf(WritableDTOInterface::class);
    });

    it('has correct default behavior for soft delete', function () {
        // Default constructor should create DTO for soft delete (deactivation)
        $dto = new AreaDeleteDTO(areaId: 1, name: 'test area');

        expect($dto->isActive)->toBeFalse()
            ->and($dto->toModelData())->toBe([
                'id' => 1,
                'is_active' => false,
            ]);
    });
});
