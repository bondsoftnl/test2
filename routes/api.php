<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Middleware\EnsureUserHasRole;
use App\Support\Roles;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', EnsureUserHasRole::class.':'.Roles::STUDENT])->group(function () {
    Route::get('students/dashboard', static fn (): JsonResponse => response()->json([
        'message' => 'Student area.',
    ]));
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
