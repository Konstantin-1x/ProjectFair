<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Главная страница
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Аутентификация (используем встроенные маршруты Laravel)
Auth::routes();

// Публичные маршруты
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

// Защищенные маршруты (требуют аутентификации)
Route::middleware('auth')->group(function () {
    // Проекты
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Задачи
    Route::resource('tasks', TaskController::class);

    // Команды
    Route::resource('teams', TeamController::class);
    Route::post('/teams/{team}/join', [TeamController::class, 'join'])->name('teams.join');
    Route::post('/teams/{team}/leave', [TeamController::class, 'leave'])->name('teams.leave');

    // Профиль пользователя
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/projects', [App\Http\Controllers\ProfileController::class, 'projects'])->name('profile.projects');
    Route::get('/profile/teams', [App\Http\Controllers\ProfileController::class, 'teams'])->name('profile.teams');
});

// Публичные профили пользователей (вне группы middleware)
Route::get('/users/{user}', [App\Http\Controllers\ProfileController::class, 'show'])->name('users.show');
Route::get('/users/{user}/projects', [App\Http\Controllers\ProfileController::class, 'projects'])->name('users.projects');
Route::get('/users/{user}/teams', [App\Http\Controllers\ProfileController::class, 'teams'])->name('users.teams');

// Админские маршруты
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::get('/projects', [AdminController::class, 'projects'])->name('projects');
    Route::get('/teams', [AdminController::class, 'teams'])->name('teams');
    Route::get('/tasks', [AdminController::class, 'tasks'])->name('tasks');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Test routes
Route::get('/test', [App\Http\Controllers\TestController::class, 'test'])->name('test');
Route::get('/test-projects-create', [App\Http\Controllers\ProjectController::class, 'create'])->name('test.projects.create');
Route::get('/test-auth', function() {
    return response()->json([
        'authenticated' => auth()->check(),
        'user_id' => auth()->id(),
        'user_name' => auth()->user() ? auth()->user()->name : null,
    ]);
})->name('test.auth');

// Test route without auth middleware
Route::get('/test-projects-create-no-auth', function() {
    return response()->json([
        'message' => 'This route works without auth middleware',
        'authenticated' => auth()->check(),
        'user_id' => auth()->id(),
    ]);
})->name('test.projects.create.no.auth');

// НОВЫЙ маршрут для создания проекта (вне группы middleware)
Route::get('/create-project', [App\Http\Controllers\ProjectController::class, 'create'])->name('create.project');

// НОВЫЙ маршрут с middleware (отдельно)
Route::get('/projects-new/create', [App\Http\Controllers\ProjectController::class, 'create'])
    ->middleware('auth')
    ->name('projects.new.create');
