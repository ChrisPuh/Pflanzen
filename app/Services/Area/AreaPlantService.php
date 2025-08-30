<?php

declare(strict_types=1);

namespace App\Services\Area;

use App\Actions\Area\Actions\AttachPlantsToAreaAction;
use App\Actions\Area\Actions\DetachPlantFromAreaAction;
use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\DTOs\Area\Actions\DetachPlantFromAreaDTO;
use App\Models\Area;
use App\Services\Area\Contracts\AreaPlantServiceInterface;
use Throwable;

final readonly class AreaPlantService implements AreaPlantServiceInterface
{
    public function __construct(
        private AttachPlantsToAreaAction $attachAction,
        private DetachPlantFromAreaAction $detachAction,
    ) {}

    /**
     * @throws Throwable
     */
    public function attachPlantsToArea(Area $area, AttachPlantToAreaDTO $data): void
    {
        // handle cache invalidation if needed
        // handle exceptions if needed
        // handle the action result if needed
        $this->attachAction->execute($area, $data);
    }

    /**
     * @throws Throwable
     */
    public function detachPlantFromArea(Area $area, DetachPlantFromAreaDTO $data): void
    {
        // handle cache invalidation if needed
        // handle exceptions if needed
        // handle the action result if needed
        $this->detachAction->execute($area, $data);
    }
}
