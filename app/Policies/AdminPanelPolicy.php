<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Filament\Panel;

final class AdminPanelPolicy
{
    public function access(User $user, Panel $panel): bool
    {
        return $user->hasRole('admin');
    }
}
