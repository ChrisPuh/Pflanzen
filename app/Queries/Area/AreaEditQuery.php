<?php

declare(strict_types=1);

namespace App\Queries\Area;

use App\Models\Area;
use App\Models\User;
use App\Repositories\Area\Contracts\AreaRepositoryInterface;

final readonly class AreaEditQuery
{
    public function __construct(private AreaRepositoryInterface $repository)
    {
    }

    public function execute(User $user, int $areaId, bool $isAdmin): array
    {
        // Get area via repository
        $area = $this->repository->queryForShow($areaId)->firstOrFail();
        
        // Get user gardens
        $userGardens = $this->getUserGardens($user, $isAdmin);

        return [
            'area' => $area,
            'userGardens' => $userGardens,
            'areaTypes' => $this->getAvailableAreaTypes(),
            'isAdmin' => $isAdmin,
        ];
    }

    private function getUserGardens(User $user, bool $isAdmin): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Garden::query()
            ->when(!$isAdmin, function (\Illuminate\Database\Eloquent\Builder $query) use ($user): void {
                $query->where('user_id', $user->id);
            })
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();
    }

    private function getAvailableAreaTypes(): array
    {
        return \App\Enums\Area\AreaTypeEnum::options()->toArray();
    }
}
