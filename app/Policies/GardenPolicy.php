<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Garden;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class GardenPolicy
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
        // Only authenticated users can view gardens list
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Garden $garden): Response
    {
        return $user->id === $garden->user_id
            ? Response::allow()
            : Response::deny('Du kannst nur deine eigenen Gärten ansehen.');
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
    public function update(User $user, Garden $garden): Response
    {
        return $user->id === $garden->user_id
            ? Response::allow()
            : Response::deny('Du kannst nur deine eigenen Gärten bearbeiten.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Garden $garden): Response
    {
        return $user->id === $garden->user_id
            ? Response::allow()
            : Response::deny('Du kannst nur deine eigenen Gärten löschen.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Garden $garden): Response
    {
        return $user->id === $garden->user_id
            ? Response::allow()
            : Response::deny('Du kannst nur deine eigenen Gärten wiederherstellen.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Garden $garden): Response
    {
        return $user->id === $garden->user_id
            ? Response::allow()
            : Response::deny('Du kannst nur deine eigenen Gärten permanent löschen.');
    }
}
