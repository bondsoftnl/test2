<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SprintTaskService
{
    public function getOrderedTasksForSprint(int $sprintId): Collection
    {
        return DB::table('sprint_tasks')
            ->where('sprint_id', $sprintId)
            ->orderBy('task_order')
            ->orderBy('id')
            ->get();
    }
}
