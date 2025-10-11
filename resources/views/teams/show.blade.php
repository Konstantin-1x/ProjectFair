@extends('layouts.app')

@section('title', $team->name . ' - Команда')

@section('content')
<div class="container">
    <!-- Team Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                                <div>
                                    <h1 class="mb-1">{{ $team->name }}</h1>
                                    <span class="badge bg-{{ $team->status === 'recruiting' ? 'success' : 'secondary' }} fs-6">
                                        {{ $team->status === 'recruiting' ? 'Набор участников' : ucfirst($team->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($team->description)
                                <p class="text-muted mb-3">{{ $team->description }}</p>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user me-2 text-primary"></i>
                                        <strong>Лидер:</strong>
                                        <span class="ms-2">{{ $team->leader->name }}</span>
                                    </div>
                                    @if($team->institute)
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-university me-2 text-primary"></i>
                                            <strong>Институт:</strong>
                                            <span class="ms-2">{{ $team->institute }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-users me-2 text-primary"></i>
                                        <strong>Участников:</strong>
                                        <span class="ms-2">{{ $team->members->count() }}/{{ $team->max_members }}</span>
                                    </div>
                                    @if($team->course)
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-graduation-cap me-2 text-primary"></i>
                                            <strong>Курс:</strong>
                                            <span class="ms-2">{{ $team->course }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-md-end">
                            @auth
                                @if($team->leader_id === Auth::id())
                                    <div class="d-flex flex-column gap-2">
                                        <a href="{{ route('teams.edit', $team) }}" class="btn btn-warning">
                                            <i class="fas fa-edit me-2"></i>Управлять командой
                                        </a>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-crown me-1"></i>Вы - лидер команды
                                        </span>
                                    </div>
                                @elseif($team->members->contains(Auth::id()))
                                    <div class="d-flex flex-column gap-2">
                                        <form method="POST" action="{{ route('teams.leave', $team) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Покинуть команду
                                            </button>
                                        </form>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-user me-1"></i>Вы участник команды
                                        </span>
                                    </div>
                                @elseif($team->status === 'recruiting' && $team->members->count() < $team->max_members)
                                    <form method="POST" action="{{ route('teams.join', $team) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-user-plus me-2"></i>Присоединиться
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary">Команда полная или не принимает участников</span>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Войти для участия
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Team Members -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Участники команды ({{ $team->members->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($team->members->count() > 0)
                        <div class="row">
                            @foreach($team->members as $member)
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-3 border rounded">
                                    @if($member->avatar)
                                        <img src="{{ Storage::url($member->avatar) }}" alt="{{ $member->name }}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            {{ $member->name }}
                                            @if($member->id === $team->leader_id)
                                                <i class="fas fa-crown text-warning ms-1"></i>
                                            @endif
                                        </h6>
                                        <small class="text-muted">{{ $member->email }}</small>
                                        @if($member->institute && $member->course)
                                            <br><small class="text-muted">{{ $member->institute }}, {{ $member->course }} курс</small>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $member->id === $team->leader_id ? 'warning' : 'primary' }}">
                                            {{ $member->id === $team->leader_id ? 'Лидер' : 'Участник' }}
                                        </span>
                                        <br><small class="text-muted">{{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d.m.Y') : 'Неизвестно' }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">В команде пока нет участников</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Team Projects -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-project-diagram me-2"></i>Проекты команды ({{ $team->projects->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($team->projects->count() > 0)
                        <div class="row">
                            @foreach($team->projects as $project)
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $project->title }}</h6>
                                        <p class="card-text text-muted">{{ Str::limit($project->short_description ?? $project->description, 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-{{ $project->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($project->status) }}
                                            </span>
                                            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Подробнее
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                            <p class="text-muted">У команды пока нет проектов</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Team Info Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Информация о команде
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Дата создания:</strong><br>
                        <span class="text-muted">{{ $team->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    
                    @if($team->recruitment_start || $team->recruitment_end)
                    <div class="mb-3">
                        <strong>Период набора:</strong><br>
                        <span class="text-muted">
                            @if($team->recruitment_start)
                                {{ $team->recruitment_start->format('d.m.Y') }}
                            @endif
                            @if($team->recruitment_start && $team->recruitment_end)
                                -
                            @endif
                            @if($team->recruitment_end)
                                {{ $team->recruitment_end->format('d.m.Y') }}
                            @endif
                        </span>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <strong>Статус:</strong><br>
                        <span class="badge bg-{{ $team->status === 'recruiting' ? 'success' : 'secondary' }}">
                            {{ $team->status === 'recruiting' ? 'Набор участников' : ucfirst($team->status) }}
                        </span>
                    </div>
                    
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ ($team->members->count() / $team->max_members) * 100 }}%">
                            {{ $team->members->count() }}/{{ $team->max_members }}
                        </div>
                    </div>
                    
                    <small class="text-muted">
                        {{ $team->max_members - $team->members->count() }} свободных мест
                    </small>
                </div>
            </div>
            
            <!-- Team Actions -->
            @auth
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Действия
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($team->leader_id === Auth::id())
                            <a href="{{ route('teams.edit', $team) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-2"></i>Редактировать команду
                            </a>
                            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Создать проект
                            </a>
                        @elseif(!$team->members->contains(Auth::id()) && $team->status === 'recruiting' && $team->members->count() < $team->max_members)
                            <form method="POST" action="{{ route('teams.join', $team) }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-user-plus me-2"></i>Присоединиться к команде
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Все команды
                        </a>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </div>
</div>
@endsection
