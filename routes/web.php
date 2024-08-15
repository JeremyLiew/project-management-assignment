<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// can be edited to start the application with your page - Jeremy
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Resource routes for tasks and projects
Route::resource('tasks', TaskController::class);

Route::resource('projects', ProjectController::class);

Route::resource('budgets', BudgetController::class);

Route::resource('logs', LogController::class)->middleware('role:Project Manager');



