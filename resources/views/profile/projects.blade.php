@extends('layouts.app')

@section('title', 'Мои проекты - ' . $user->name)

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
                    <a href="{{ route('profile.teams', $user) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-users me-2"></i>Команды
                    </a>
                    @if(Auth::id() === $user->id)
                        <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Создать проект
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Projects Content -->
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-project-diagram me-2"></i>
                Проекты {{ Auth::id() === $user->id ? 'пользователя' : $user->name }}
            </h2>
            <span class="badge bg-primary fs-6">{{ $projects->total() }} проектов</span>
        </div>
        
        @if($projects->count() > 0)
            <div class="row">
                @foreach($projects as $project)
                <div class="col-md-6 col-lg-4 mb-4">
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
                                {{ Str::limit($project->short_description ?? $project->description, 120) }}
                            </p>
                            
                            <!-- Tags -->
                            @if($project->tags->count() > 0)
                            <div class="mb-3">
                                @foreach($project->tags->take(3) as $tag)
                                    <span class="tag">{{ $tag->name }}</span>
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
                                    <i class="fas fa-calendar me-1"></i>{{ $project->created_at->format('d.m.Y') }}
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
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Подробнее
                                </a>
                                @if(Auth::id() === $user->id && (Auth::user()->isAdmin() || $project->created_by === Auth::id()))
                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-edit me-1"></i>Редактировать
                                    </a>
                                @endif
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
                <i class="fas fa-project-diagram fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">
                    {{ Auth::id() === $user->id ? 'У вас пока нет проектов' : 'У пользователя пока нет проектов' }}
                </h4>
                <p class="text-muted mb-4">
                    {{ Auth::id() === $user->id ? 'Создайте свой первый проект и поделитесь им с сообществом!' : 'Этот пользователь еще не создал ни одного проекта.' }}
                </p>
                @if(Auth::id() === $user->id)
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Создать первый проект
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
