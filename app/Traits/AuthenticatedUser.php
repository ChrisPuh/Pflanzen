<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;

trait AuthenticatedUser
{
    /**
     * Get the authenticated user and admin status.
     *
     * @return array{user: User, isAdmin: bool}
     */
    protected function getUserAndAdminStatus(): array
    {
        $user = $this->getUser();
        $isAdmin = $user->hasRole('admin');

        return [
            'user' => $user,
            'isAdmin' => $isAdmin,
        ];
    }

    /**
     * Get just the authenticated user.
     */
    protected function getUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }

    /**
     * Check if the authenticated user is an admin.
     */
    protected function isAdmin(): bool
    {
        return $this->getUser()->hasRole('admin');
    }
}
