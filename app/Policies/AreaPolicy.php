<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Area;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class AreaPolicy
{
    /**
     * Perform pre-authorization checks (Laravel 12 modern approach).
     */
    public function before(User $user): ?Response
    {
        // Admin users can perform all actions
        if ($user->hasRole('admin')) {
            return Response::allow();
        }

        return null; // Continue with regular policy methods
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): Response
    {
        // Only authenticated users can view areas list
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Area $area): Response
    {
        return $user->id === $area->garden->user_id
            ? Response::allow()
            : Response::deny('Du kannst nur Bereiche deiner eigenen Gärten ansehen.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Area $area): Response
    {
        return $user->id === $area->garden->user_id
            ? Response::allow()
            : Response::deny('Du kannst nur Bereiche deiner eigenen Gärten bearbeiten.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Area $area): Response
    {
        return $user->id === $area->garden->user_id
            ? Response::allow()
            : Response::deny('Du kannst nur Bereiche deiner eigenen Gärten löschen.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Area $area): Response
    {
        return $user->id === $area->garden->user_id
            ? Response::allow()
            : Response::deny('Du kannst nur Bereiche deiner eigenen Gärten wiederherstellen.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Area $area): Response
    {
        return $user->id === $area->garden->user_id
            ? Response::allow()
            : Response::deny('Du kannst nur Bereiche deiner eigenen Gärten permanent löschen.');
    }
}
