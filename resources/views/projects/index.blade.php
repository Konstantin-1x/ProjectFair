@extends('layouts.app')

@section('title', 'Проекты')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Студенческие проекты</h1>
    @auth
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Создать проект
        </a>
    @endauth
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('projects.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Поиск</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Название проекта...">
            </div>
            <div class="col-md-2">
                <label for="institute" class="form-label">Институт</label>
                <select class="form-select" id="institute" name="institute">
                    <option value="">Все институты</option>
                    @foreach(['ИИТУТ', 'ИМО', 'ИППИ', 'ИЭиУ', 'ИФИиВ', 'ИФКиС'] as $inst)
                        <option value="{{ $inst }}" {{ request('institute') == $inst ? 'selected' : '' }}>
                            {{ $inst }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="course" class="form-label">Курс</label>
                <select class="form-select" id="course" name="course">
                    <option value="">Все курсы</option>
                    @for($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}" {{ request('course') == $i ? 'selected' : '' }}>
                            {{ $i }} курс
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Статус</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Все статусы</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активные</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Завершенные</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>Поиск
                </button>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Сбросить
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Projects Grid -->
@if($projects->count() > 0)
    <div class="row">
        @foreach($projects as $project)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                @if($project->image)
                    <img src="{{ Storage::url($project->image) }}" class="card-img-top" alt="{{ $project->title }}" 
                         style="height: 200px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                         style="height: 200px;">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                @endif
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $project->title }}</h5>
                    <p class="card-text flex-grow-1">
                        {{ Str::limit($project->short_description ?? $project->description, 120) }}
                    </p>
                    
                    <!-- Tags -->
                    @if($project->tags->count() > 0)
                    <div class="mb-3">
                        @foreach($project->tags->take(3) as $tag)
                            <span class="tag" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                        @endforeach
                        @if($project->tags->count() > 3)
                            <span class="text-muted">+{{ $project->tags->count() - 3 }}</span>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Technologies -->
                    @if($project->technologies->count() > 0)
                    <div class="mb-3">
                        @foreach($project->technologies->take(3) as $tech)
                            <span class="technology-badge">{{ $tech->name }}</span>
                        @endforeach
                        @if($project->technologies->count() > 3)
                            <span class="text-muted">+{{ $project->technologies->count() - 3 }}</span>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Project Info -->
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>{{ $project->creator->name }}
                        </small>
                        @if($project->institute)
                            <br><small class="text-muted">
                                <i class="fas fa-university me-1"></i>{{ $project->institute }}
                            </small>
                        @endif
                        @if($project->course)
                            <br><small class="text-muted">
                                <i class="fas fa-graduation-cap me-1"></i>{{ $project->course }} курс
                            </small>
                        @endif
                    </div>
                    
                    <!-- Status Badge -->
                    <div class="mb-3">
                        @switch($project->status)
                            @case('active')
                                <span class="badge bg-success">Активный</span>
                                @break
                            @case('completed')
                                <span class="badge bg-primary">Завершен</span>
                                @break
                            @case('archived')
                                <span class="badge bg-secondary">Архивный</span>
                                @break
                        @endswitch
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            {{ $project->created_at->format('d.m.Y') }}
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
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $projects->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
        <h3>Проекты не найдены</h3>
        <p class="text-muted">Пока нет проектов, соответствующих вашим критериям поиска.</p>
        @auth
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Создать первый проект
            </a>
        @endauth
    </div>
@endif

@endsection
