<?php

namespace App\Policies;

use App\Models\User;

class RoleAccessPolicy
{
    /**
     * @param array<int, string> $allowedRoles
     */
    public function canAccess(User $user, array $allowedRoles): bool
    {
        return in_array($user->role, $allowedRoles, true);
    }
}
