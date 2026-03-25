<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SprintTasksOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_sprint_tasks_are_stably_sorted_across_repeated_requests(): void
    {
        Sanctum::actingAs($this->makeStudent());

        DB::table('sprints')->insert($this->makeSprintRow(11));

        DB::table('sprint_tasks')->insert([
            $this->makeTaskRow(101, 11, 3),
            $this->makeTaskRow(102, 11, 1),
            $this->makeTaskRow(103, 11, 2),
        ]);

        $firstResponse = $this->getJson('/api/sprints/11/tasks')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->json('data');

        $secondResponse = $this->getJson('/api/sprints/11/tasks')
            ->assertOk()
            ->json('data');

        $this->assertSame([1, 2, 3], array_column($firstResponse, 'task_order'));
        $this->assertSame($firstResponse, $secondResponse);
    }

    public function test_returns_empty_array_when_sprint_has_no_tasks(): void
    {
        Sanctum::actingAs($this->makeStudent());

        DB::table('sprints')->insert($this->makeSprintRow(20));

        $this->getJson('/api/sprints/20/tasks')
            ->assertOk()
            ->assertExactJson(['data' => []]);
    }

    public function test_returns_validation_error_when_sprint_is_not_numeric(): void
    {
        Sanctum::actingAs($this->makeStudent());

        $this->getJson('/api/sprints/not-a-number/tasks')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sprint']);
    }

    public function test_returns_validation_error_when_sprint_does_not_exist(): void
    {
        Sanctum::actingAs($this->makeStudent());

        $this->getJson('/api/sprints/999999/tasks')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sprint']);
    }

    private function makeSprintRow(int $id): array
    {
        $columns = array_flip(Schema::getColumnListing('sprints'));
        $now = Carbon::now();
        $row = [];

        if (isset($columns['id'])) {
            $row['id'] = $id;
        }

        if (isset($columns['name'])) {
            $row['name'] = 'Sprint '.$id;
        }

        if (isset($columns['title'])) {
            $row['title'] = 'Sprint '.$id;
        }

        if (isset($columns['created_at'])) {
            $row['created_at'] = $now;
        }

        if (isset($columns['updated_at'])) {
            $row['updated_at'] = $now;
        }

        return $row;
    }

    private function makeTaskRow(int $id, int $sprintId, int $taskOrder): array
    {
        $columns = array_flip(Schema::getColumnListing('sprint_tasks'));
        $now = Carbon::now();

        $row = [
            'id' => $id,
            'sprint_id' => $sprintId,
            'task_order' => $taskOrder,
        ];

        if (! isset($columns['id'])) {
            unset($row['id']);
        }

        if (! isset($columns['sprint_id'])) {
            unset($row['sprint_id']);
        }

        if (! isset($columns['task_order'])) {
            unset($row['task_order']);
        }

        if (isset($columns['title'])) {
            $row['title'] = 'Task '.$id;
        }

        if (isset($columns['name'])) {
            $row['name'] = 'Task '.$id;
        }

        if (isset($columns['created_at'])) {
            $row['created_at'] = $now;
        }

        if (isset($columns['updated_at'])) {
            $row['updated_at'] = $now;
        }

        return $row;
    }

    private function makeStudent(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => Roles::STUDENT])->save();

        return $user;
    }
}
