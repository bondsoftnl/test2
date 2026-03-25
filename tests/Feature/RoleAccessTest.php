<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_endpoints_reject_unauthenticated_users(): void
    {
        $this->getJson('/api/students/dashboard')->assertUnauthorized();
        $this->getJson('/api/mentors/dashboard')->assertUnauthorized();
        $this->getJson('/api/admins/dashboard')->assertUnauthorized();
    }

    public function test_student_can_access_student_route_and_cannot_access_mentor_route(): void
    {
        Sanctum::actingAs($this->makeUserWithRole(Roles::STUDENT));

        $this->getJson('/api/students/dashboard')->assertOk();
        $this->getJson('/api/mentors/dashboard')->assertForbidden();
    }

    public function test_mentor_can_access_mentor_route_and_cannot_access_admin_route(): void
    {
        Sanctum::actingAs($this->makeUserWithRole(Roles::MENTOR));

        $this->getJson('/api/mentors/dashboard')->assertOk();
        $this->getJson('/api/admins/dashboard')->assertForbidden();
    }

    public function test_admin_can_access_admin_route_and_cannot_access_student_route(): void
    {
        Sanctum::actingAs($this->makeUserWithRole(Roles::ADMIN));

        $this->getJson('/api/admins/dashboard')->assertOk();
        $this->getJson('/api/students/dashboard')->assertForbidden();
    }

    private function makeUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }
}
