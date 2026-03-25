<?php

namespace App\Services\Auth;

use App\Models\User;

class TokenService
{
    public function create(User $user, string $deviceName = 'spa'): string
    {
        return $user->createToken($deviceName)->plainTextToken;
    }
}
