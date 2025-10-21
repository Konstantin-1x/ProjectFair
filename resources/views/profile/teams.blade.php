@extends('layouts.app')

@section('title', 'Мои команды - ' . $user->name)

@section('content')
<div class="row">
    <!-- Profile Sidebar -->
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 100px; height: 100px;">
                        <i class="fas fa-user fa-3x"></i>
                    </div>
                @endif
                
                <h5 class="card-title">{{ $user->name }}</h5>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('profile.show', $user) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-user me-2"></i>Профиль
                    </a>
                    <a href="{{ route('profile.projects', $user) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-project-diagram me-2"></i>Проекты
                    </a>
                    @if(Auth::id() === $user->id)
                        <a href="{{ route('teams.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-2"></i>Создать команду
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Teams Content -->
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-users me-2"></i>
                Команды {{ Auth::id() === $user->id ? 'пользователя' : $user->name }}
            </h2>
            <span class="badge bg-success fs-6">{{ $teams->count() + $ledTeams->count() }} команд</span>
        </div>
        
        <!-- Teams I'm part of -->
        @if($teams->count() > 0)
            <div class="mb-5">
                <h4 class="mb-3">
                    <i class="fas fa-users me-2"></i>Участвую в командах ({{ $teams->count() }})
                </h4>
                <div class="row">
                    @foreach($teams as $team)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="card-title mb-1">{{ $team->name }}</h5>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>Лидер: {{ $team->leader->name }}
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-{{ $team->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($team->status) }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if($team->description)
                                    <p class="card-text text-muted">{{ Str::limit($team->description, 100) }}</p>
                                @endif
                                
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <div class="text-primary fw-bold">{{ $team->members->count() }}</div>
                                        <small class="text-muted">Участников</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-success fw-bold">{{ $team->projects->count() }}</div>
                                        <small class="text-muted">Проектов</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-info fw-bold">{{ $team->max_members }}</div>
                                        <small class="text-muted">Макс. размер</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>{{ $team->created_at->format('d.m.Y') }}
                                    </small>
                                    <a href="{{ route('teams.show', $team) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Подробнее
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Teams I lead -->
        @if($ledTeams->count() > 0)
            <div class="mb-5">
                <h4 class="mb-3">
                    <i class="fas fa-crown me-2 text-warning"></i>Руковожу командами ({{ $ledTeams->count() }})
                </h4>
                <div class="row">
                    @foreach($ledTeams as $team)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-warning">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-crown"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="card-title mb-1">
                                            {{ $team->name }}
                                            <i class="fas fa-crown text-warning ms-1"></i>
                                        </h5>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>Вы - лидер команды
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-{{ $team->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($team->status) }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if($team->description)
                                    <p class="card-text text-muted">{{ Str::limit($team->description, 100) }}</p>
                                @endif
                                
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <div class="text-primary fw-bold">{{ $team->members->count() }}</div>
                                        <small class="text-muted">Участников</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-success fw-bold">{{ $team->projects->count() }}</div>
                                        <small class="text-muted">Проектов</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-info fw-bold">{{ $team->max_members }}</div>
                                        <small class="text-muted">Макс. размер</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>{{ $team->created_at->format('d.m.Y') }}
                                    </small>
                                    <div>
                                        <a href="{{ route('teams.edit', $team) }}" class="btn btn-warning btn-sm me-1">
                                            <i class="fas fa-edit me-1"></i>Управлять
                                        </a>
                                        <a href="{{ route('teams.show', $team) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Подробнее
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Empty state -->
        @if($teams->count() === 0 && $ledTeams->count() === 0)
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">
                    {{ Auth::id() === $user->id ? 'Вы пока не участвуете в командах' : 'Пользователь пока не участвует в командах' }}
                </h4>
                <p class="text-muted mb-4">
                    {{ Auth::id() === $user->id ? 'Создайте команду или присоединитесь к существующей для совместной работы над проектами!' : 'Этот пользователь еще не создал и не присоединился ни к одной команде.' }}
                </p>
                @if(Auth::id() === $user->id)
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('teams.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Создать команду
                        </a>
                        <a href="{{ route('teams.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i>Найти команды
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
