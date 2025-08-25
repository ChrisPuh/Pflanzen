<?php

declare(strict_types=1);

use App\Actions\Area\AreaDeleteAction;
use App\DTOs\Area\AreaDeleteDTO;
use App\Models\Area;
use App\Repositories\Area\Contracts\AreaRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

describe('AreaDeleteAction', function () {
    beforeEach(function () {
        $this->repository = Mockery::mock(AreaRepositoryInterface::class);
        $this->action = new AreaDeleteAction($this->repository);
        $this->area = Area::factory()->make(['id' => 1]);
        $this->dto = AreaDeleteDTO::fromValidatedRequest(['is_active' => false]);
    });

    it('deletes area successfully', function () {
        DB::shouldReceive('transaction')
            ->once()
            ->with(Closure::class)
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with($this->area, $this->dto)
            ->andReturn(true);

        Log::shouldReceive('info')
            ->once()
            ->with('Deleting area', ['area_id' => 1]);

        Log::shouldReceive('info')
            ->once()
            ->with('Area deleted successfully', ['area_id' => 1]);

        $result = $this->action->execute($this->area, $this->dto);

        expect($result)->toBeTrue();
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
            ->shouldReceive('delete')
            ->once()
            ->with($this->area, $this->dto)
            ->andThrow($exception);

        Log::shouldReceive('info')
            ->once()
            ->with('Deleting area', ['area_id' => 1]);

        Log::shouldReceive('error')
            ->once()
            ->with('Error deleting area', ['error' => 'Database error', 'area_id' => 1]);

        $result = $this->action->execute($this->area, $this->dto);

        expect($result)->toBeFalse();
    });

    it('handles transaction rollback on failure', function () {
        $exception = new RuntimeException('Transaction failed');

        DB::shouldReceive('transaction')
            ->once()
            ->with(Closure::class)
            ->andThrow($exception);

        Log::shouldReceive('info')
            ->once()
            ->with('Deleting area', ['area_id' => 1]);

        Log::shouldReceive('error')
            ->once()
            ->with('Error deleting area', ['error' => 'Transaction failed', 'area_id' => 1]);

        $result = $this->action->execute($this->area, $this->dto);

        expect($result)->toBeFalse();
    });

    it('returns false when delete fails', function () {
        DB::shouldReceive('transaction')
            ->once()
            ->with(Closure::class)
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with($this->area, $this->dto)
            ->andReturn(false);

        Log::shouldReceive('info')
            ->once()
            ->with('Deleting area', ['area_id' => 1]);

        Log::shouldReceive('info')
            ->once()
            ->with('Area deleted successfully', ['area_id' => 1]);

        $result = $this->action->execute($this->area, $this->dto);

        expect($result)->toBeFalse();
    });

    afterEach(function () {
        Mockery::close();
    });
});
