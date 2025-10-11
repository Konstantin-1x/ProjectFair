@extends('layouts.app')

@section('title', 'Команды')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-users me-2"></i>Команды
        </h1>
        @auth
            <a href="{{ route('teams.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Создать команду
            </a>
        @endauth
    </div>
    
    @if($teams->count() > 0)
        <div class="row">
            @foreach($teams as $team)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">{{ $team->name }}</h5>
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>Лидер: {{ $team->leader->name }}
                                </small>
                            </div>
                            <span class="badge bg-success">Набор</span>
                        </div>
                        
                        @if($team->description)
                            <p class="card-text text-muted flex-grow-1">{{ Str::limit($team->description, 120) }}</p>
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
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>{{ $team->created_at->format('d.m.Y') }}
                            </small>
                            <a href="{{ route('teams.show', $team) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Подробнее
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $teams->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-users fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Пока нет команд</h4>
            <p class="text-muted mb-4">Создайте первую команду и начните совместную работу над проектами!</p>
            @auth
                <a href="{{ route('teams.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Создать команду
                </a>
            @endauth
        </div>
    @endif
</div>
@endsection
