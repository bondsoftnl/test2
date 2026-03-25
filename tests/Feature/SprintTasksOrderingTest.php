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

        $orderColumn = collect(['task_order', 'order', 'position'])
            ->first(static fn (string $column): bool => Schema::hasColumn('sprint_tasks', $column)) ?? 'id';

        DB::table('sprint_tasks')->insert([
            $this->makeTaskRow(101, 11, $orderColumn, 3),
            $this->makeTaskRow(102, 11, $orderColumn, 1),
            $this->makeTaskRow(103, 11, $orderColumn, 2),
        ]);

        $firstResponse = $this->getJson('/api/sprints/11/tasks')
            ->assertOk()
            ->json('data');

        $secondResponse = $this->getJson('/api/sprints/11/tasks')
            ->assertOk()
            ->json('data');

        $this->assertSame([1, 2, 3], array_column($firstResponse, $orderColumn));
        $this->assertSame($firstResponse, $secondResponse);
    }

    private function makeTaskRow(int $id, int $sprintId, string $orderColumn, int $order): array
    {
        $columns = array_flip(Schema::getColumnListing('sprint_tasks'));
        $now = Carbon::now();

        $row = [];

        if (isset($columns['id'])) {
            $row['id'] = $id;
        }

        if (isset($columns['sprint_id'])) {
            $row['sprint_id'] = $sprintId;
        }

        if (isset($columns[$orderColumn])) {
            $row[$orderColumn] = $order;
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
