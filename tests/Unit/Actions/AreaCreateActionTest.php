<?php

declare(strict_types=1);

use App\Actions\AreaStoreAction;
use App\DTOs\Area\AreaStoreDTO;
use App\Enums\Area\AreaTypeEnum;
use App\Models\Area;
use App\Repositories\Contracts\AreaRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

describe('AreaCreateAction', function () {
    beforeEach(function () {
        $this->repository = Mockery::mock(AreaRepositoryInterface::class);
        $this->action = new AreaStoreAction($this->repository);
        $this->dto = new AreaStoreDTO(
            name: 'Test Area',
            gardenId: 1,
            type: AreaTypeEnum::VegetableBed,
            isActive: true,
            description: 'Test description'
        );
    });

    it('creates area successfully', function () {
        $area = Area::factory()->create(
            [
                'name' => 'Test Area',
            ]
        );

        DB::shouldReceive('transaction')
            ->once()
            ->with(Closure::class)
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with($this->dto)
            ->andReturn($area);

        Log::shouldReceive('info')
            ->once()
            ->with('Creating new area', ['name' => 'Test Area']);

        Log::shouldReceive('info')
            ->once()
            ->with('Area created successfully', ['area_id' => 1, 'name' => 'Test Area']);

        $result = $this->action->execute($this->dto);

        expect($result)->toBe($area);
    });

    it('handles repository exceptions and logs errors', function () {
        $exception = new Exception('Database error');

        DB::shouldReceive('transaction')
            ->once()
            ->with(Closure::class)
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with($this->dto)
            ->andThrow($exception);

        Log::shouldReceive('info')
            ->once()
            ->with('Creating new area', ['name' => 'Test Area']);

        Log::shouldReceive('error')
            ->once()
            ->with('Error creating area', ['error' => 'Database error', 'name' => 'Test Area']);

        expect(fn () => $this->action->execute($this->dto))
            ->toThrow(Exception::class, 'Database error');
    });

    it('handles transaction rollback on failure', function () {
        $exception = new RuntimeException('Transaction failed');

        DB::shouldReceive('transaction')
            ->once()
            ->with(Closure::class)
            ->andThrow($exception);

        Log::shouldReceive('info')
            ->once()
            ->with('Creating new area', ['name' => 'Test Area']);

        Log::shouldReceive('error')
            ->once()
            ->with('Error creating area', ['error' => 'Transaction failed', 'name' => 'Test Area']);

        expect(fn () => $this->action->execute($this->dto))
            ->toThrow(RuntimeException::class, 'Transaction failed');
    });

    afterEach(function () {
        Mockery::close();
    });
});
