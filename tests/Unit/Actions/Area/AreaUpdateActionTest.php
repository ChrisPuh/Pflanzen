<?php

declare(strict_types=1);

use App\Actions\Area\AreaUpdateAction;
use App\DTOs\Area\AreaUpdateDTO;
use App\Enums\Area\AreaTypeEnum;
use App\Models\Area;
use App\Models\Garden;
use Illuminate\Support\Facades\Log;

describe('AreaUpdateAction', function () {

    beforeEach(function () {
        $this->action = new AreaUpdateAction(
            app(App\Repositories\Area\Contracts\AreaRepositoryInterface::class)
        );
        $this->garden = Garden::factory()->create();
        $this->area = Area::factory()->create(['garden_id' => $this->garden->id]);
        $this->dto = new AreaUpdateDTO(
            name: 'Updated Area',
            gardenId: $this->garden->id,
            type: AreaTypeEnum::VegetableBed,
            isActive: true,
            description: 'Updated description'
        );
    });

    it('updates area successfully', function () {
        Log::shouldReceive('info')
            ->once()
            ->with('Updating area', ['area_id' => $this->area->id]);

        Log::shouldReceive('info')
            ->once()
            ->with('Area updated successfully', ['area_id' => $this->area->id]);

        $result = $this->action->execute($this->area, $this->dto);

        expect($result->name)->toBe('Updated Area')
            ->and($result->description)->toBe('Updated description');
    });

    it('handles exceptions and logs errors', function () {
        $exception = new Exception('Database error');

        Log::shouldReceive('info')
            ->once()
            ->with('Updating area', ['area_id' => $this->area->id]);

        Log::shouldReceive('error')
            ->once()
            ->with('Error updating area', ['error' => $exception->getMessage(), 'area_id' => $this->area->id]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Database error');

        // Mock the repository to throw an exception
        $mockRepo = Mockery::mock(App\Repositories\Area\Contracts\AreaRepositoryInterface::class);
        $mockRepo->shouldReceive('update')
            ->andThrow($exception);

        $actionWithMock = new AreaUpdateAction($mockRepo);
        $actionWithMock->execute($this->area, $this->dto);
    });
});
