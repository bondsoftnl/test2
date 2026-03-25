<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Middleware\EnsureUserHasRole;
use App\Support\Roles;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', EnsureUserHasRole::class.':'.Roles::STUDENT])->group(function () {
    Route::get('students/dashboard', static fn (): JsonResponse => response()->json([
        'message' => 'Student area.',
    ]));

    Route::get('sprints/{sprint}/tasks', static function (int $sprint): JsonResponse {
        $orderColumn = collect(['task_order', 'order', 'position'])
            ->first(static fn (string $column): bool => Schema::hasColumn('sprint_tasks', $column)) ?? 'id';

        $tasks = DB::table('sprint_tasks')
            ->where('sprint_id', $sprint)
            ->orderBy($orderColumn)
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => $tasks,
        ]);
    });
});

Route::middleware(['auth:sanctum', EnsureUserHasRole::class.':'.Roles::MENTOR])->group(function () {
    Route::get('mentors/dashboard', static fn (): JsonResponse => response()->json([
        'message' => 'Mentor area.',
    ]));
});

Route::middleware(['auth:sanctum', EnsureUserHasRole::class.':'.Roles::ADMIN])->group(function () {
    Route::get('admins/dashboard', static fn (): JsonResponse => response()->json([
        'message' => 'Admin area.',
    ]));
});
