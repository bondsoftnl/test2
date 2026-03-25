<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sprint\SprintTasksIndexRequest;
use App\Services\SprintTaskService;
use Illuminate\Http\JsonResponse;

class SprintTaskController extends Controller
{
    public function __construct(private readonly SprintTaskService $sprintTaskService)
    {
    }

    public function index(SprintTasksIndexRequest $request): JsonResponse
    {
        $tasks = $this->sprintTaskService->getOrderedTasksForSprint((int) $request->validated('sprint'));

        return response()->json([
            'data' => $tasks,
        ]);
    }
}
