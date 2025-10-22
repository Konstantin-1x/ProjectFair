<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\ProjectTeamController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamApplicationController;
use App\Http\Controllers\TeamProjectApplicationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

// Главная страница
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Переключение языка
Route::get('/locale/{lang}', function (string $lang) {
    if (!in_array($lang, ['ru', 'en'], true)) {
        $lang = config('app.locale');
    }
    session(['locale' => $lang]);
    return Redirect::back();
})->name('locale.switch');

// Аутентификация (используем встроенные маршруты Laravel)
Auth::routes();

// Публичные маршруты
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

// Защищенные маршруты (требуют аутентификации)
Route::middleware('auth')->group(function () {
    // Проекты
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/projects/{project}/complete', [ProjectController::class, 'complete'])->name('projects.complete');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    // Задачи проекта
    Route::prefix('projects/{project}/tasks')->name('projects.tasks.')->group(function () {
        Route::get('/', [ProjectTaskController::class, 'index'])->name('index');
        Route::get('/create', [ProjectTaskController::class, 'create'])->name('create');
        Route::post('/', [ProjectTaskController::class, 'store'])->name('store');
        Route::get('/{task}', [ProjectTaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [ProjectTaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [ProjectTaskController::class, 'update'])->name('update');
        Route::post('/{task}/complete', [ProjectTaskController::class, 'complete'])->name('complete');
        Route::post('/{task}/reject', [ProjectTaskController::class, 'reject'])->name('reject');
        Route::post('/{task}/approve', [ProjectTaskController::class, 'approve'])->name('approve');
        Route::post('/{task}/comment', [ProjectTaskController::class, 'addComment'])->name('comment');
        Route::delete('/{task}', [ProjectTaskController::class, 'destroy'])->name('destroy');
    });

    // Команды проекта
    Route::prefix('projects/{project}/teams')->name('projects.teams.')->group(function () {
        Route::get('/', [ProjectTeamController::class, 'index'])->name('index');
        Route::get('/create', [ProjectTeamController::class, 'create'])->name('create');
        Route::post('/', [ProjectTeamController::class, 'store'])->name('store');
        Route::delete('/{team}', [ProjectTeamController::class, 'destroy'])->name('destroy');
        Route::patch('/{team}/status', [ProjectTeamController::class, 'updateStatus'])->name('update_status');
    });

    // Заявки команд на проекты
    Route::prefix('projects/{project}/team-applications')->name('projects.team-applications.')->group(function () {
        Route::get('/', [TeamProjectApplicationController::class, 'index'])->name('index');
        Route::get('/apply', [TeamProjectApplicationController::class, 'create'])->name('create');
        Route::post('/apply', [TeamProjectApplicationController::class, 'store'])->name('store');
        Route::post('/{application}/approve', [TeamProjectApplicationController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [TeamProjectApplicationController::class, 'reject'])->name('reject');
        Route::post('/{application}/withdraw', [TeamProjectApplicationController::class, 'withdraw'])->name('withdraw');
    });

    // Мои заявки команд на проекты
    Route::get('/my-team-applications', [TeamProjectApplicationController::class, 'myApplications'])->name('my-team-applications');

    // Задачи
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

    // Команды
    Route::resource('teams', TeamController::class);
    Route::post('/teams/{team}/join', [TeamController::class, 'join'])->name('teams.join');
    Route::post('/teams/{team}/leave', [TeamController::class, 'leave'])->name('teams.leave');
    Route::delete('/teams/{team}/members/{user}', [TeamController::class, 'removeMember'])->name('teams.members.remove');
    Route::post('/teams/{team}/members/{user}/exclude', [TeamController::class, 'excludeMember'])->name('teams.members.exclude');
    
    // Заявки на вступление в команду
    Route::get('/teams/{team}/apply', [TeamApplicationController::class, 'create'])->name('teams.apply');
    Route::post('/teams/{team}/apply', [TeamApplicationController::class, 'store'])->name('teams.apply.store');
    Route::get('/teams/{team}/applications', [TeamApplicationController::class, 'index'])->name('teams.applications');
    Route::post('/teams/{team}/applications/{application}/approve', [TeamApplicationController::class, 'approve'])->name('teams.applications.approve');
    Route::post('/teams/{team}/applications/{application}/reject', [TeamApplicationController::class, 'reject'])->name('teams.applications.reject');
    Route::post('/teams/{team}/applications/{application}/withdraw', [TeamApplicationController::class, 'withdraw'])->name('teams.applications.withdraw');
    Route::get('/my-applications', [TeamApplicationController::class, 'myApplications'])->name('teams.my-applications');

    // Профиль пользователя
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/projects', [ProfileController::class, 'projects'])->name('profile.projects');
    Route::get('/profile/teams', [ProfileController::class, 'teams'])->name('profile.teams');
});

// Публичные профили пользователей (вне группы middleware)
Route::get('/users/{user}', [ProfileController::class, 'show'])->name('users.show');
Route::get('/users/{user}/projects', [ProfileController::class, 'projects'])->name('users.projects');
Route::get('/users/{user}/teams', [ProfileController::class, 'teams'])->name('users.teams');

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

Route::get('/home', [HomeController::class, 'index'])->name('home.dashboard');

