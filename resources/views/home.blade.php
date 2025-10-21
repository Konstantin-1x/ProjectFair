@extends('layouts.app')

@section('title', 'Личный кабинет - ' . Auth::user()->name)

@section('content')
@php
    $user = Auth::user();
    $userProjects = \App\Models\Project::where('created_by', $user->id)->count();
    $userTeams = $user->teams()->count();
    $ledTeams = $user->ledTeams()->count();
    $recentProjects = \App\Models\Project::where('created_by', $user->id)->latest()->take(3)->get();
    $recentTeams = $user->teams()->latest()->take(3)->get();
@endphp

<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-2">
                            <i class="fas fa-user-circle me-2 text-primary"></i>
                            Добро пожаловать, {{ $user->name }}!
                        </h2>
                        <p class="text-muted mb-0">
                            @if($user->institute && $user->course)
                                {{ $user->institute }}, {{ $user->course }} курс
                                @if($user->group)
                                    , группа {{ $user->group }}
                                @endif
                            @else
                                Участник платформы СевГУ
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Редактировать профиль
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="text-primary mb-2">
                    <i class="fas fa-project-diagram fa-2x"></i>
                </div>
                <h3 class="card-title text-primary">{{ $userProjects }}</h3>
                <p class="card-text text-muted">Мои проекты</p>
                <a href="{{ route('profile.projects') }}" class="btn btn-sm btn-outline-primary">Посмотреть</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="text-success mb-2">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h3 class="card-title text-success">{{ $userTeams }}</h3>
                <p class="card-text text-muted">Участвую в командах</p>
                <a href="{{ route('profile.teams') }}" class="btn btn-sm btn-outline-success">Посмотреть</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="text-warning mb-2">
                    <i class="fas fa-crown fa-2x"></i>
                </div>
                <h3 class="card-title text-warning">{{ $ledTeams }}</h3>
                <p class="card-text text-muted">Руковожу командами</p>
                <a href="{{ route('profile.teams') }}" class="btn btn-sm btn-outline-warning">Посмотреть</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="text-info mb-2">
                    <i class="fas fa-tasks fa-2x"></i>
                </div>
                <h3 class="card-title text-info">{{ \App\Models\Task::count() }}</h3>
                <p class="card-text text-muted">Доступных задач</p>
                <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-info">Посмотреть</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Быстрые действия
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('projects.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Создать проект
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teams.create') }}" class="btn btn-success w-100">
                            <i class="fas fa-users me-2"></i>Создать команду
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('tasks.create') }}" class="btn btn-warning w-100">
                            <i class="fas fa-tasks me-2"></i>Создать задачу
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('projects.index') }}" class="btn btn-info w-100">
                            <i class="fas fa-search me-2"></i>Найти проекты
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <!-- Recent Projects -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-project-diagram me-2"></i>Последние проекты
                </h5>
            </div>
            <div class="card-body">
                @if($recentProjects->count() > 0)
                    @foreach($recentProjects as $project)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            @if($project->image)
                                <img src="{{ Storage::url($project->image) }}" alt="{{ $project->title }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">{{ $project->title }}</h6>
                            <small class="text-muted">{{ $project->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">Открыть</a>
                        </div>
                    </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('profile.projects') }}" class="btn btn-sm btn-primary">Все проекты</a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                        <p class="text-muted">У вас пока нет проектов</p>
                        <a href="{{ route('projects.create') }}" class="btn btn-primary">Создать первый проект</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Teams -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Мои команды
                </h5>
            </div>
            <div class="card-body">
                @if($recentTeams->count() > 0)
                    @foreach($recentTeams as $team)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">
                                {{ $team->name }}
                                @if($team->leader_id === $user->id)
                                    <i class="fas fa-crown text-warning ms-1"></i>
                                @endif
                            </h6>
                            <small class="text-muted">{{ $team->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-{{ $team->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($team->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('profile.teams') }}" class="btn btn-sm btn-primary">Все команды</a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Вы пока не участвуете в командах</p>
                        <a href="{{ route('teams.index') }}" class="btn btn-primary">Найти команды</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($user->isAdmin())
<!-- Admin Quick Access -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-cog me-2"></i>Административная панель
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-warning w-100">
                            <i class="fas fa-tachometer-alt me-2"></i>Дашборд
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.analytics') }}" class="btn btn-warning w-100">
                            <i class="fas fa-chart-bar me-2"></i>Аналитика
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.users') }}" class="btn btn-warning w-100">
                            <i class="fas fa-users me-2"></i>Пользователи
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.projects') }}" class="btn btn-warning w-100">
                            <i class="fas fa-project-diagram me-2"></i>Проекты
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
