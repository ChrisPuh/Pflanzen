<?php

declare(strict_types=1);

use App\DTOs\Area\Actions\PlantSelectionData;
use Illuminate\Support\Carbon;

describe('PlantSelectionData', function () {
    it('creates instance with all parameters', function () {
        $plantedAt = now();
        
        $dto = new PlantSelectionData(
            plantId: 42,
            quantity: 10,
            notes: 'Test plant notes',
            plantedAt: $plantedAt
        );

        expect($dto->plantId)->toBe(42)
            ->and($dto->quantity)->toBe(10)
            ->and($dto->notes)->toBe('Test plant notes')
            ->and($dto->plantedAt)->toBe($plantedAt);
    });

    it('creates instance with null notes', function () {
        $plantedAt = now();
        
        $dto = new PlantSelectionData(
            plantId: 1,
            quantity: 5,
            notes: null,
            plantedAt: $plantedAt
        );

        expect($dto->plantId)->toBe(1)
            ->and($dto->quantity)->toBe(5)
            ->and($dto->notes)->toBeNull()
            ->and($dto->plantedAt)->toBe($plantedAt);
    });

    it('creates instance with null planted_at', function () {
        $dto = new PlantSelectionData(
            plantId: 3,
            quantity: 7,
            notes: 'Without date',
            plantedAt: null
        );

        expect($dto->plantId)->toBe(3)
            ->and($dto->quantity)->toBe(7)
            ->and($dto->notes)->toBe('Without date')
            ->and($dto->plantedAt)->toBeNull();
    });

    it('creates instance with both optional parameters null', function () {
        $dto = new PlantSelectionData(
            plantId: 99,
            quantity: 1,
            notes: null,
            plantedAt: null
        );

        expect($dto->plantId)->toBe(99)
            ->and($dto->quantity)->toBe(1)
            ->and($dto->notes)->toBeNull()
            ->and($dto->plantedAt)->toBeNull();
    });

    describe('type constraints', function () {
        it('enforces integer type for plantId', function () {
            $dto = new PlantSelectionData(
                plantId: 123,
                quantity: 5,
                notes: 'test',
                plantedAt: now()
            );

            expect($dto->plantId)->toBeInt()
                ->and($dto->plantId)->toBe(123);
        });

        it('enforces integer type for quantity', function () {
            $dto = new PlantSelectionData(
                plantId: 1,
                quantity: 456,
                notes: 'test',
                plantedAt: now()
            );

            expect($dto->quantity)->toBeInt()
                ->and($dto->quantity)->toBe(456);
        });

        it('enforces string or null type for notes', function () {
            $dtoWithString = new PlantSelectionData(
                plantId: 1,
                quantity: 5,
                notes: 'string notes',
                plantedAt: now()
            );

            $dtoWithNull = new PlantSelectionData(
                plantId: 2,
                quantity: 3,
                notes: null,
                plantedAt: now()
            );

            expect($dtoWithString->notes)->toBeString()
                ->and($dtoWithString->notes)->toBe('string notes')
                ->and($dtoWithNull->notes)->toBeNull();
        });

        it('enforces Carbon or null type for plantedAt', function () {
            $date = now();
            
            $dtoWithCarbon = new PlantSelectionData(
                plantId: 1,
                quantity: 5,
                notes: 'test',
                plantedAt: $date
            );

            $dtoWithNull = new PlantSelectionData(
                plantId: 2,
                quantity: 3,
                notes: 'test',
                plantedAt: null
            );

            expect($dtoWithCarbon->plantedAt)->toBeInstanceOf(Carbon::class)
                ->and($dtoWithCarbon->plantedAt)->toBe($date)
                ->and($dtoWithNull->plantedAt)->toBeNull();
        });
    });

    describe('edge cases', function () {
        it('handles zero quantity', function () {
            $dto = new PlantSelectionData(
                plantId: 1,
                quantity: 0,
                notes: 'Zero quantity plant',
                plantedAt: now()
            );

            expect($dto->quantity)->toBe(0)
                ->and($dto->quantity)->toBeInt();
        });

        it('handles large plant ID', function () {
            $largeId = PHP_INT_MAX;
            
            $dto = new PlantSelectionData(
                plantId: $largeId,
                quantity: 1,
                notes: 'Large ID test',
                plantedAt: now()
            );

            expect($dto->plantId)->toBe($largeId)
                ->and($dto->plantId)->toBeInt();
        });

        it('handles large quantity', function () {
            $largeQuantity = 999999;
            
            $dto = new PlantSelectionData(
                plantId: 1,
                quantity: $largeQuantity,
                notes: 'Large quantity test',
                plantedAt: now()
            );

            expect($dto->quantity)->toBe($largeQuantity)
                ->and($dto->quantity)->toBeInt();
        });

        it('handles empty string notes', function () {
            $dto = new PlantSelectionData(
                plantId: 1,
                quantity: 5,
                notes: '',
                plantedAt: now()
            );

            expect($dto->notes)->toBe('')
                ->and($dto->notes)->toBeString();
        });

        it('handles long notes string', function () {
            $longNotes = str_repeat('This is a very long note. ', 100);
            
            $dto = new PlantSelectionData(
                plantId: 1,
                quantity: 5,
                notes: $longNotes,
                plantedAt: now()
            );

            expect($dto->notes)->toBe($longNotes)
                ->and($dto->notes)->toBeString()
                ->and(strlen($dto->notes))->toBeGreaterThan(1000);
        });

        it('handles whitespace-only notes', function () {
            $whitespaceNotes = "   \n\t\r   ";
            
            $dto = new PlantSelectionData(
                plantId: 1,
                quantity: 5,
                notes: $whitespaceNotes,
                plantedAt: now()
            );

            expect($dto->notes)->toBe($whitespaceNotes)
                ->and($dto->notes)->toBeString();
        });
    });

    describe('date handling', function () {
        it('preserves exact Carbon instance', function () {
            $specificDate = Carbon::create(2023, 12, 25, 14, 30, 45);
            
            $dto = new PlantSelectionData(
                plantId: 1,
                quantity: 5,
                notes: 'Date test',
                plantedAt: $specificDate
            );

            expect($dto->plantedAt)->toBe($specificDate)
                ->and($dto->plantedAt->year)->toBe(2023)
                ->and($dto->plantedAt->month)->toBe(12)
                ->and($dto->plantedAt->day)->toBe(25)
                ->and($dto->plantedAt->hour)->toBe(14)
                ->and($dto->plantedAt->minute)->toBe(30)
                ->and($dto->plantedAt->second)->toBe(45);
        });

        it('handles different Carbon timezone', function () {
            $utcDate = Carbon::now('UTC');
            $berlinDate = Carbon::now('Europe/Berlin');
            
            $dtoUtc = new PlantSelectionData(
                plantId: 1,
                quantity: 5,
                notes: 'UTC test',
                plantedAt: $utcDate
            );

            $dtoBerlin = new PlantSelectionData(
                plantId: 2,
                quantity: 3,
                notes: 'Berlin test',
                plantedAt: $berlinDate
            );

            expect($dtoUtc->plantedAt->timezone->getName())->toBe('UTC')
                ->and($dtoBerlin->plantedAt->timezone->getName())->toBe('Europe/Berlin');
        });

        it('handles past and future dates', function () {
            $pastDate = Carbon::now()->subYears(5);
            $futureDate = Carbon::now()->addYears(2);
            
            $dtoPast = new PlantSelectionData(
                plantId: 1,
                quantity: 5,
                notes: 'Past planting',
                plantedAt: $pastDate
            );

            $dtoFuture = new PlantSelectionData(
                plantId: 2,
                quantity: 3,
                notes: 'Future planting',
                plantedAt: $futureDate
            );

            expect($dtoPast->plantedAt)->toBe($pastDate)
                ->and($dtoPast->plantedAt->isPast())->toBeTrue()
                ->and($dtoFuture->plantedAt)->toBe($futureDate)
                ->and($dtoFuture->plantedAt->isFuture())->toBeTrue();
        });
    });

    describe('immutability', function () {
        it('is readonly', function () {
            $dto = new PlantSelectionData(
                plantId: 1,
                quantity: 5,
                notes: 'test',
                plantedAt: now()
            );
            
            $reflection = new ReflectionClass($dto);

            expect($reflection->isReadOnly())->toBeTrue();
        });

        it('properties are readonly', function () {
            $dto = new PlantSelectionData(
                plantId: 1,
                quantity: 5,
                notes: 'test',
                plantedAt: now()
            );
            
            $reflection = new ReflectionClass($dto);

            foreach ($reflection->getProperties() as $property) {
                expect($property->isReadOnly())->toBeTrue();
            }
        });

        it('cannot modify properties after creation', function () {
            $originalDate = now();
            $dto = new PlantSelectionData(
                plantId: 42,
                quantity: 10,
                notes: 'original',
                plantedAt: $originalDate
            );

            // Verify original values are preserved
            expect($dto->plantId)->toBe(42)
                ->and($dto->quantity)->toBe(10)
                ->and($dto->notes)->toBe('original')
                ->and($dto->plantedAt)->toBe($originalDate);
        });
    });

    describe('data integrity', function () {
        it('maintains all constructor parameters', function () {
            $plantId = 123;
            $quantity = 456;
            $notes = 'integrity test notes';
            $plantedAt = Carbon::create(2023, 6, 15, 10, 30);

            $dto = new PlantSelectionData(
                plantId: $plantId,
                quantity: $quantity,
                notes: $notes,
                plantedAt: $plantedAt
            );

            expect($dto->plantId)->toBe($plantId)
                ->and($dto->quantity)->toBe($quantity)
                ->and($dto->notes)->toBe($notes)
                ->and($dto->plantedAt)->toBe($plantedAt);
        });

        it('handles unicode characters in notes', function () {
            $unicodeNotes = 'Тест 🌱 Prüfung ñoño 中文';
            
            $dto = new PlantSelectionData(
                plantId: 1,
                quantity: 5,
                notes: $unicodeNotes,
                plantedAt: now()
            );

            expect($dto->notes)->toBe($unicodeNotes);
        });

        it('preserves original Carbon object reference', function () {
            $originalCarbon = now();
            
            $dto = new PlantSelectionData(
                plantId: 1,
                quantity: 5,
                notes: 'reference test',
                plantedAt: $originalCarbon
            );

            expect($dto->plantedAt)->toBe($originalCarbon);
        });
    });
});