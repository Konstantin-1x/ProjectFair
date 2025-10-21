@extends('layouts.app')

@section('title', __('Admin Panel'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-tachometer-alt me-2"></i>{{ __('Admin Panel') }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.analytics') }}" class="btn btn-outline-primary">
            <i class="fas fa-chart-bar me-2"></i>{{ __('Analytics') }}
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-2 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                <h3 class="text-primary">{{ $stats['users'] }}</h3>
                <p class="card-text">{{ __('Users') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-project-diagram fa-2x text-success mb-2"></i>
                <h3 class="text-success">{{ $stats['projects'] }}</h3>
                <p class="card-text">{{ __('Projects') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users-cog fa-2x text-info mb-2"></i>
                <h3 class="text-info">{{ $stats['teams'] }}</h3>
                <p class="card-text">{{ __('Teams') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-tasks fa-2x text-warning mb-2"></i>
                <h3 class="text-warning">{{ $stats['tasks'] }}</h3>
                <p class="card-text">{{ __('Tasks') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-play-circle fa-2x text-success mb-2"></i>
                <h3 class="text-success">{{ $stats['active_projects'] }}</h3>
                <p class="card-text">{{ __('Active') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-check-circle fa-2x text-primary mb-2"></i>
                <h3 class="text-primary">{{ $stats['completed_projects'] }}</h3>
                <p class="card-text">{{ __('Completed') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>{{ __('Quick actions') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-users me-2"></i>{{ __('Manage users') }}
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.projects') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-project-diagram me-2"></i>{{ __('Manage projects') }}
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.teams') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-users-cog me-2"></i>{{ __('Manage teams') }}
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.tasks') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-tasks me-2"></i>{{ __('Manage tasks') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>{{ __('Recent projects') }}</h5>
            </div>
            <div class="card-body">
                @if($recent_projects->count() > 0)
                    @foreach($recent_projects as $project)
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px;">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                    {{ $project->title }}
                                </a>
                            </h6>
                            <small class="text-muted">
                                {{ $project->creator->name }} • {{ $project->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <span class="badge bg-{{ $project->status === 'active' ? 'success' : ($project->status === 'completed' ? 'primary' : 'secondary') }}">
                            {{ $project->status === 'active' ? __('Active') : ($project->status === 'completed' ? __('Completed') : __('Archived')) }}
                        </span>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">{{ __('No projects') }}</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>{{ __('New users') }}</h5>
            </div>
            <div class="card-body">
                @if($recent_users->count() > 0)
                    @foreach($recent_users as $user)
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px;">
                            {{ $user->initials() }}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            <small class="text-muted">
                                {{ $user->email }} • {{ $user->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                            {{ $user->role === 'admin' ? __('Admin') : __('User') }}
                        </span>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">{{ __('No users') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
