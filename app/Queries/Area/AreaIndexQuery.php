<?php

declare(strict_types=1);

namespace App\Queries\Area;

use App\DTOs\Area\AreaIndexFilterDTO;
use App\Repositories\Area\Contracts\AreaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class AreaIndexQuery
{
    public function __construct(private AreaRepositoryInterface $repository)
    {
    }

    public function execute(int $user_id, AreaIndexFilterDTO $filter, bool $isAdmin): LengthAwarePaginator
    {
        $query = $this->repository
            ->queryForUser(user_id: $user_id, isAdmin: $isAdmin)
            ->latest()
            ->forGarden($filter->garden_id)
            ->byType($filter->type)
            ->byCategory($filter->category)
            ->search($filter->search);

        return $query->paginate(12)->withQueryString();
    }
}
