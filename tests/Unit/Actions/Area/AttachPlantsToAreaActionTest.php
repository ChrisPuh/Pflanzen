<?php

declare(strict_types=1);

use App\Actions\Area\Actions\AttachPlantsToAreaAction;
use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\Models\Area;
use App\Repositories\Area\Contracts\AreaPlantRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

describe('AttachPlantsToAreaAction', function () {
    beforeEach(function () {
        $this->area = Area::factory()->create();
        $this->repository = Mockery::mock(AreaPlantRepositoryInterface::class);
        $this->action = new AttachPlantsToAreaAction($this->repository);

        $this->plantData = [
            1 => [
                'quantity' => 5,
                'notes' => 'Test notes',
                'planted_at' => now(),
                'plant_id' => 1,
            ],
            2 => [
                'quantity' => 3,
                'notes' => 'Another plant',
                'planted_at' => now(),
                'plant_id' => 2,
            ],
        ];

        $this->dto = AttachPlantToAreaDTO::fromValidatedRequest($this->plantData);
    });

    describe('execute', function () {
        it('successfully attaches plants to area', function () {
            // Arrange
            $expectedAttached = ['plant_1', 'plant_2'];

            $this->repository
                ->shouldReceive('attachPlantsToArea')
                ->once()
                ->with($this->area, $this->dto)
                ->andReturn($expectedAttached);

            Log::shouldReceive('info')
                ->twice()
                ->with(
                    Mockery::anyOf(
                        'Attaching plants to area',
                        'Plants attached to area successfully'
                    ),
                    Mockery::type('array')
                );

            // Act
            $result = $this->action->execute($this->area, $this->dto);

            // Assert
            expect($result)->toBe($expectedAttached);
        });

        it('logs start of operation with correct data', function () {
            // Arrange
            $this->repository
                ->shouldReceive('attachPlantsToArea')
                ->once()
                ->andReturn([]);

            Log::shouldReceive('info')
                ->once()
                ->with('Attaching plants to area', ['area_id' => $this->area->id])
                ->ordered();

            Log::shouldReceive('info')
                ->once()
                ->with('Plants attached to area successfully', Mockery::type('array'))
                ->ordered();

            // Act
            $this->action->execute($this->area, $this->dto);
        });

        it('logs success with attached plants data', function () {
            // Arrange
            $attachedPlants = ['plant_1', 'plant_2', 'plant_3'];

            $this->repository
                ->shouldReceive('attachPlantsToArea')
                ->once()
                ->andReturn($attachedPlants);

            Log::shouldReceive('info')
                ->once()
                ->with('Attaching plants to area', Mockery::type('array'));

            Log::shouldReceive('info')
                ->once()
                ->with(
                    'Plants attached to area successfully',
                    [
                        'area_id' => $this->area->id,
                        'attached_plants' => $attachedPlants,
                    ]
                );

            // Act
            $result = $this->action->execute($this->area, $this->dto);

            // Assert
            expect($result)->toBe($attachedPlants);
        });

        it('wraps repository call in database transaction', function () {
            // Arrange
            $this->repository
                ->shouldReceive('attachPlantsToArea')
                ->once()
                ->andReturn([]);

            Log::shouldReceive('info')->twice();

            DB::shouldReceive('transaction')
                ->once()
                ->with(Mockery::type('callable'))
                ->andReturnUsing(function ($callback) {
                    return $callback();
                });

            // Act
            $this->action->execute($this->area, $this->dto);
        });

        it('logs error and rethrows exception when repository fails', function () {
            // Arrange
            $exception = new RuntimeException('Database connection failed');

            $this->repository
                ->shouldReceive('attachPlantsToArea')
                ->once()
                ->andThrow($exception);

            Log::shouldReceive('info')
                ->once()
                ->with('Attaching plants to area', ['area_id' => $this->area->id]);

            Log::shouldReceive('error')
                ->once()
                ->with(
                    'Failed to attach plants to area',
                    [
                        'area_id' => $this->area->id,
                        'error' => 'Database connection failed',
                    ]
                );

            // Act & Assert
            expect(fn () => $this->action->execute($this->area, $this->dto))
                ->toThrow(RuntimeException::class, 'Database connection failed');
        });

        it('logs error and rethrows exception when transaction fails', function () {
            // Arrange
            $exception = new InvalidArgumentException('Invalid plant data');

            DB::shouldReceive('transaction')
                ->once()
                ->andThrow($exception);

            Log::shouldReceive('info')
                ->once()
                ->with('Attaching plants to area', ['area_id' => $this->area->id]);

            Log::shouldReceive('error')
                ->once()
                ->with(
                    'Failed to attach plants to area',
                    [
                        'area_id' => $this->area->id,
                        'error' => 'Invalid plant data',
                    ]
                );

            // Act & Assert
            expect(fn () => $this->action->execute($this->area, $this->dto))
                ->toThrow(InvalidArgumentException::class, 'Invalid plant data');
        });

        it('handles different types of throwable exceptions', function () {
            // Arrange
            $error = new Error('Fatal error occurred');

            $this->repository
                ->shouldReceive('attachPlantsToArea')
                ->once()
                ->andThrow($error);

            Log::shouldReceive('info')->once();
            Log::shouldReceive('error')
                ->once()
                ->with(
                    'Failed to attach plants to area',
                    [
                        'area_id' => $this->area->id,
                        'error' => 'Fatal error occurred',
                    ]
                );

            // Act & Assert
            expect(fn () => $this->action->execute($this->area, $this->dto))
                ->toThrow(Error::class, 'Fatal error occurred');
        });

        it('passes correct parameters to repository', function () {
            // Arrange
            $this->repository
                ->shouldReceive('attachPlantsToArea')
                ->once()
                ->with(
                    Mockery::on(fn ($area) => $area->id === $this->area->id),
                    Mockery::on(fn ($dto) => $dto instanceof AttachPlantToAreaDTO)
                )
                ->andReturn([]);

            Log::shouldReceive('info')->twice();

            // Act
            $this->action->execute($this->area, $this->dto);
        });

        it('returns empty array when no plants attached', function () {
            // Arrange
            $this->repository
                ->shouldReceive('attachPlantsToArea')
                ->once()
                ->andReturn([]);

            Log::shouldReceive('info')->twice();

            // Act
            $result = $this->action->execute($this->area, $this->dto);

            // Assert
            expect($result)->toBe([]);
        });

        it('maintains transaction integrity on success', function () {
            // Arrange
            $attachedPlants = ['plant_1'];
            $transactionExecuted = false;

            $this->repository
                ->shouldReceive('attachPlantsToArea')
                ->once()
                ->andReturn($attachedPlants);

            DB::shouldReceive('transaction')
                ->once()
                ->andReturnUsing(function ($callback) use (&$transactionExecuted) {
                    $transactionExecuted = true;

                    return $callback();
                });

            Log::shouldReceive('info')->twice();

            // Act
            $result = $this->action->execute($this->area, $this->dto);

            // Assert
            expect($transactionExecuted)->toBeTrue();
            expect($result)->toBe($attachedPlants);
        });

        it('does not log success when exception occurs', function () {
            // Arrange
            $exception = new Exception('Something went wrong');

            $this->repository
                ->shouldReceive('attachPlantsToArea')
                ->once()
                ->andThrow($exception);

            Log::shouldReceive('info')
                ->once()
                ->with('Attaching plants to area', ['area_id' => $this->area->id]);

            Log::shouldReceive('error')->once();

            // Success log should NOT be called
            Log::shouldNotReceive('info')
                ->with('Plants attached to area successfully', Mockery::any());

            // Act & Assert
            expect(fn () => $this->action->execute($this->area, $this->dto))
                ->toThrow(Exception::class);
        });
    });
});
