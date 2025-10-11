@extends('layouts.app')

@section('title', 'Витрина студенческих проектов СевГУ')

@section('content')
<!-- Hero Section -->
<div class="hero-section bg-gradient-to-r from-blue-600 to-blue-800 text-white py-5 mb-5 rounded-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3 text-dark">
                    <i class="fas fa-graduation-cap me-3"></i>
                    Витрина студенческих проектов
                </h1>
                <p class="lead mb-4 text-dark">
                    Платформа для публикации, поиска и совместной работы над студенческими проектами 
                    Севастопольского государственного университета
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ route('projects.index') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-eye me-2"></i>Смотреть проекты
                    </a>
                    @auth
                        <a href="{{ route('projects.new.create') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-plus me-2"></i>Создать проект
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Присоединиться
                        </a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <i class="fas fa-laptop-code" style="font-size: 8rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="row mb-5">
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="text-primary mb-3">
                    <i class="fas fa-project-diagram fa-3x"></i>
                </div>
                <h5 class="card-title">Публикация проектов</h5>
                <p class="card-text">
                    Студенческие команды могут публиковать свои проекты с подробной информацией, 
                    технологиями и тегами для удобного поиска.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="text-primary mb-3">
                    <i class="fas fa-tasks fa-3x"></i>
                </div>
                <h5 class="card-title">Банк задач</h5>
                <p class="card-text">
                    Публикация задач для решения студенческими командами с указанием сложности, 
                    требований и сроков выполнения.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="text-primary mb-3">
                    <i class="fas fa-users fa-3x"></i>
                </div>
                <h5 class="card-title">Командообразование</h5>
                <p class="card-text">
                    Создание студенческих команд, открытие вакансий и поиск участников 
                    для совместной работы над проектами.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="row mb-5">
    <div class="col-12">
        <h3 class="text-center mb-4">Статистика платформы</h3>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h2 class="text-primary">{{ \App\Models\Project::count() }}</h2>
                <p class="card-text">Проектов</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h2 class="text-primary">{{ \App\Models\Task::count() }}</h2>
                <p class="card-text">Задач</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h2 class="text-primary">{{ \App\Models\Team::count() }}</h2>
                <p class="card-text">Команд</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h2 class="text-primary">{{ \App\Models\User::count() }}</h2>
                <p class="card-text">Пользователей</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Projects -->
@if(\App\Models\Project::count() > 0)
<div class="row">
    <div class="col-12">
        <h3 class="mb-4">Последние проекты</h3>
    </div>
    @foreach(\App\Models\Project::with(['creator', 'tags', 'technologies'])->latest()->take(6)->get() as $project)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            @if($project->image)
                <img src="{{ Storage::url($project->image) }}" class="card-img-top" alt="{{ $project->title }}" style="height: 200px; object-fit: cover;">
            @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="fas fa-image fa-3x text-muted"></i>
                </div>
            @endif
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{ $project->title }}</h5>
                <p class="card-text flex-grow-1">
                    {{ Str::limit($project->short_description ?? $project->description, 100) }}
                </p>
                <div class="mb-3">
                    @foreach($project->tags->take(3) as $tag)
                        <span class="tag">{{ $tag->name }}</span>
                    @endforeach
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-user me-1"></i>{{ $project->creator->name }}
                    </small>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-primary btn-sm">
                        Подробнее
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
        </div>

<div class="text-center mt-4">
    <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
        Смотреть все проекты
    </a>
</div>
        @endif

@endsection

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, var(--sevgu-blue) 0%, var(--sevgu-light-blue) 100%);
    }
</style>
@endpush