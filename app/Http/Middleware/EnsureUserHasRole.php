<?php

namespace App\Http\Middleware;

use App\Policies\RoleAccessPolicy;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnsureUserHasRole
{
    public function __construct(private readonly RoleAccessPolicy $policy)
    {
    }

    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        if (! $user || ! $this->policy->canAccess($user, $roles)) {
            return new JsonResponse([
                'message' => 'Forbidden.',
            ], 403);
        }

        return $next($request);
    }
}
