<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AboutUsController;
use Illuminate\Support\Facades\Http;
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

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Resource routes for tasks and projects
Route::resource('tasks', TaskController::class);

Route::resource('projects', ProjectController::class);

Route::resource('budgets', BudgetController::class);

// Route::resource('logs', LogController::class)->middleware('role:Project Manager');
Route::resource('logs', LogController::class);

Route::get('about-us', [AboutUsController::class,'index'])->name('about-us');

Route::post('/about-us/members', [AboutUsController::class,'getMembersViaWebService'])->name('about-us-post');
