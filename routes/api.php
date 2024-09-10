<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::get('/projects', [ProjectController::class, 'index']);         // Get all projects (XML)
    Route::get('/projects/{id}', [ProjectController::class, 'show']);     // Get project details (XML)
    Route::post('/projects', [ProjectController::class, 'store']);        // Create a new project (All User)
    Route::put('/projects/{id}', [ProjectController::class, 'update']);   // Update project
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);// Delete project
    Route::post('/projects/{id}/assign', [ProjectController::class, 'assign']);  // Assign project (Manager & Admin)
    Route::put('/   projects/{id}/complete', [ProjectController::class, 'complete']); // Complete a project (Manager & Admin)
});