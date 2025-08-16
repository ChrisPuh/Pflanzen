<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class AdminPanelPolicy
{
    public function access(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
