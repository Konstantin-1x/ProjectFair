@extends('layouts.app')

@section('title', $project->title)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Project Header -->
        <div class="card mb-4">
            @if($project->image)
                <img src="{{ Storage::url($project->image) }}" class="card-img-top" alt="{{ $project->title }}" 
                     style="height: 300px; object-fit: cover;">
            @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                     style="height: 300px;">
                    <i class="fas fa-image fa-4x text-muted"></i>
                </div>
            @endif
            
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h1 class="card-title">{{ $project->title }}</h1>
                    @switch($project->status)
                        @case('active')
                            <span class="badge bg-success fs-6">Активный</span>
                            @break
                        @case('completed')
                            <span class="badge bg-primary fs-6">Завершен</span>
                            @break
                        @case('archived')
                            <span class="badge bg-secondary fs-6">Архивный</span>
                            @break
                    @endswitch
                </div>
                
                <p class="card-text lead">{{ $project->description }}</p>
                
                <!-- Project Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="fas fa-user me-2"></i>Автор</h6>
                        <p class="text-muted">{{ $project->creator->name }}</p>
                    </div>
                    @if($project->institute)
                    <div class="col-md-6">
                        <h6><i class="fas fa-university me-2"></i>Институт</h6>
                        <p class="text-muted">{{ $project->institute }}</p>
                    </div>
                    @endif
                    @if($project->course)
                    <div class="col-md-6">
                        <h6><i class="fas fa-graduation-cap me-2"></i>Курс</h6>
                        <p class="text-muted">{{ $project->course }} курс</p>
                    </div>
                    @endif
                    @if($project->type)
                    <div class="col-md-6">
                        <h6><i class="fas fa-tag me-2"></i>Тип проекта</h6>
                        <p class="text-muted">{{ $project->type }}</p>
                    </div>
                    @endif
                </div>
                
                <!-- Links -->
                <div class="d-flex gap-3 mb-4">
                    @if($project->demo_url)
                        <a href="{{ $project->demo_url }}" target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-external-link-alt me-2"></i>Демо
                        </a>
                    @endif
                    @if($project->github_url)
                        <a href="{{ $project->github_url }}" target="_blank" class="btn btn-outline-dark">
                            <i class="fab fa-github me-2"></i>GitHub
                        </a>
                    @endif
                </div>
                
                <!-- Actions -->
                @auth
                    @can('update', $project)
                        <div class="d-flex gap-2">
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Редактировать
                            </a>
                            @can('delete', $project)
                                <form method="POST" action="{{ route('projects.destroy', $project) }}" 
                                      onsubmit="return confirm('Вы уверены, что хотите удалить этот проект?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-2"></i>Удалить
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endcan
                @endauth
            </div>
        </div>
        
        <!-- Team Information -->
        @if($project->team)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Команда проекта</h5>
            </div>
            <div class="card-body">
                <h6>Лидер команды: {{ $project->team->leader->name }}</h6>
                <p class="text-muted">{{ $project->team->description }}</p>
                
                <h6>Участники команды:</h6>
                <div class="row">
                    @foreach($project->team->members as $member)
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px;">
                                {{ $member->initials() }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $member->name }}</div>
                                <small class="text-muted">{{ $member->pivot->role }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-lg-4">
        <!-- Tags -->
        @if($project->tags->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Теги</h5>
            </div>
            <div class="card-body">
                @foreach($project->tags as $tag)
                    <span class="tag me-2 mb-2" style="background-color: {{ $tag->color }}">
                        {{ $tag->name }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Technologies -->
        @if($project->technologies->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-code me-2"></i>Технологии</h5>
            </div>
            <div class="card-body">
                @foreach($project->technologies as $tech)
                    <span class="technology-badge me-2 mb-2">
                        @if($tech->icon)
                            <i class="{{ $tech->icon }} me-1"></i>
                        @endif
                        {{ $tech->name }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Project Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Информация о проекте</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="text-primary">{{ $project->tags->count() }}</h6>
                        <small class="text-muted">Теги</small>
                    </div>
                    <div class="col-6">
                        <h6 class="text-primary">{{ $project->technologies->count() }}</h6>
                        <small class="text-muted">Технологии</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-12">
                        <h6 class="text-primary">{{ $project->created_at->format('d.m.Y') }}</h6>
                        <small class="text-muted">Дата создания</small>
                    </div>
                </div>
                @if($project->started_at)
                <div class="row text-center mt-2">
                    <div class="col-12">
                        <h6 class="text-primary">{{ $project->started_at->format('d.m.Y') }}</h6>
                        <small class="text-muted">Дата начала</small>
                    </div>
                </div>
                @endif
                @if($project->completed_at)
                <div class="row text-center mt-2">
                    <div class="col-12">
                        <h6 class="text-primary">{{ $project->completed_at->format('d.m.Y') }}</h6>
                        <small class="text-muted">Дата завершения</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Back to Projects -->
        <div class="card">
            <div class="card-body text-center">
                <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Назад к проектам
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
