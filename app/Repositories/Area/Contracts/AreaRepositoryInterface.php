<?php

declare(strict_types=1);

namespace App\Repositories\Area\Contracts;

use App\DTOs\Shared\Contracts\WritableDTOInterface;
use App\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

interface AreaRepositoryInterface
{
    public function queryForUser(User $user, bool $isAdmin): Builder;

    public function store(WritableDTOInterface $data): Area;

    public function update(Area $area, WritableDTOInterface $data): Area;

    public function delete(Area $area, WritableDTOInterface $data): bool;
}
