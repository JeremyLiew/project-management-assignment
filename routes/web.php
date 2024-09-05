<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginRegisterController;
use App\Http\Controllers\ForgotPasswordController;

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

Route::get('/projects/{project}/users', [TaskController::class, 'getProjectUsers']);

Route::resource('budgets', BudgetController::class);

// Route::resource('logs', LogController::class)->middleware('role:Project Manager');
Route::resource('logs', LogController::class);

Route::get('about-us', [AboutUsController::class, 'index'])->name('about-us');

Route::post('/about-us/members', [AboutUsController::class, 'getMembersViaWebService'])->name('about-us-post');

Route::controller(LoginRegisterController::class)->group(function () {
    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/create-users', 'createUsers')->name('createUsers');
});

Route::controller(ProfileController::class)->group(function () {
    Route::get('/profile', 'show')->name('profile.show');
    Route::post('/profile/update', 'update')->name('profile.update');
});

Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('forget-password', 'showForgetPasswordForm')->name('forget.password.get');
    Route::post('forget-password', 'submitForgetPasswordForm')->name('forget.password.post');
    Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset.password.get');
    Route::post('reset-password', 'submitResetPasswordForm')->name('reset.password.post');
});

Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware('isAdmin');
