<?php

declare(strict_types=1);

namespace App\Services\Area;

use App\Actions\Area\Actions\AttachPlantsToAreaAction;
use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\Models\Area;
use App\Services\Area\Contracts\AreaPlantServiceInterface;
use Throwable;

final readonly class AreaPlantService implements AreaPlantServiceInterface
{
    public function __construct(private AttachPlantsToAreaAction $action) {}

    /**
     * @throws Throwable
     */
    public function attachPlantsToArea(Area $area, AttachPlantToAreaDTO $data): void
    {
        // handle cache invalidation if needed
        // handle exceptions if needed
        // handle the action result if needed
        $this->action->execute($area, $data);
    }
}
