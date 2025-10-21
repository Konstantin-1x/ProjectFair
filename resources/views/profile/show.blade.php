@extends('layouts.app')

@section('title', 'Профиль - ' . $user->name)

@section('content')
<div class="row">
    <!-- Profile Info -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 150px; height: 150px;">
                        <i class="fas fa-user fa-4x"></i>
                    </div>
                @endif
                
                <h4 class="card-title">{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
                
                @if($user->institute)
                    <p class="mb-1"><i class="fas fa-university me-2"></i>{{ $user->institute }}</p>
                @endif
                
                @if($user->course)
                    <p class="mb-1"><i class="fas fa-graduation-cap me-2"></i>{{ $user->course }} курс</p>
                @endif
                
                @if($user->group)
                    <p class="mb-1"><i class="fas fa-users me-2"></i>Группа {{ $user->group }}</p>
                @endif
                
                @if($user->bio)
                    <div class="mt-3">
                        <h6>О себе:</h6>
                        <p class="text-muted">{{ $user->bio }}</p>
                    </div>
                @endif
                
                @if(Auth::id() === $user->id)
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-edit me-2"></i>Редактировать профиль
                    </a>
                @endif
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h5 class="card-title">Статистика</h5>
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="text-primary">{{ $projects->total() }}</h4>
                        <small class="text-muted">Проектов</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success">{{ $teams->count() }}</h4>
                        <small class="text-muted">Команд</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-warning">{{ $ledTeams->count() }}</h4>
                        <small class="text-muted">Руководит</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Content -->
    <div class="col-md-8">
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab">
                    <i class="fas fa-project-diagram me-2"></i>Проекты ({{ $projects->total() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="teams-tab" data-bs-toggle="tab" data-bs-target="#teams" type="button" role="tab">
                    <i class="fas fa-users me-2"></i>Команды ({{ $teams->count() }})
                </button>
            </li>
            @if($ledTeams->count() > 0)
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="led-teams-tab" data-bs-toggle="tab" data-bs-target="#led-teams" type="button" role="tab">
                    <i class="fas fa-crown me-2"></i>Руководит ({{ $ledTeams->count() }})
                </button>
            </li>
            @endif
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="profileTabsContent">
            <!-- Projects Tab -->
            <div class="tab-pane fade show active" id="projects" role="tabpanel">
                @if($projects->count() > 0)
                    <div class="row">
                        @foreach($projects as $project)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                @if($project->image)
                                    <img src="{{ Storage::url($project->image) }}" class="card-img-top" alt="{{ $project->title }}" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $project->title }}</h5>
                                    <p class="card-text text-muted flex-grow-1">
                                        {{ Str::limit($project->short_description ?? $project->description, 100) }}
                                    </p>
                                    <div class="mb-3">
                                        @foreach($project->tags->take(3) as $tag)
                                            <span class="tag">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-{{ $project->status === 'active' ? 'success' : ($project->status === 'completed' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-primary">Подробнее</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $projects->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-project-diagram fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Пока нет проектов</h5>
                        @if(Auth::id() === $user->id)
                            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Создать первый проект
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            
            <!-- Teams Tab -->
            <div class="tab-pane fade" id="teams" role="tabpanel">
                @if($teams->count() > 0)
                    <div class="row">
                        @foreach($teams as $team)
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $team->name }}</h5>
                                    <p class="card-text text-muted">{{ Str::limit($team->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>{{ $team->leader->name }}
                                        </small>
                                        <span class="badge bg-{{ $team->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($team->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Пока нет команд</h5>
                        <p class="text-muted mb-4">Присоединитесь к существующим командам или создайте свою</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('teams.index') }}" class="btn btn-primary">
                                <i class="fas fa-users me-2"></i>Все команды
                            </a>
                            <a href="{{ route('teams.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>Создать команду
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Led Teams Tab -->
            @if($ledTeams->count() > 0)
            <div class="tab-pane fade" id="led-teams" role="tabpanel">
                <div class="row">
                    @foreach($ledTeams as $team)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">
                                    {{ $team->name }}
                                    <i class="fas fa-crown text-warning ms-2"></i>
                                </h5>
                                <p class="card-text text-muted">{{ Str::limit($team->description, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-users me-1"></i>{{ $team->members->count() }} участников
                                    </small>
                                    <span class="badge bg-{{ $team->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($team->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
