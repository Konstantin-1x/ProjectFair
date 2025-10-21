@extends('layouts.app')

@section('title', __('Analytics'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-chart-bar me-2"></i>{{ __('Analytics') }}</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>{{ __('Back to panel') }}
    </a>
</div>

<!-- Institute Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-university me-2"></i>{{ __('Institute statistics') }}</h5>
            </div>
            <div class="card-body">
                @if($institute_stats->count() > 0)
                    <div class="row">
                        @foreach($institute_stats as $stat)
                        <div class="col-md-4 mb-3">
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                <div>
                                    <h6 class="mb-1">{{ $stat->institute }}</h6>
                                    <small class="text-muted">{{ __('Projects') }}</small>
                                </div>
                                <div class="text-end">
                                    <h4 class="text-primary mb-0">{{ $stat->count }}</h4>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">{{ __('No institute data') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Course Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>{{ __('Course statistics') }}</h5>
            </div>
            <div class="card-body">
                @if($course_stats->count() > 0)
                    <div class="row">
                        @foreach($course_stats as $stat)
                        <div class="col-md-2 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-success mb-1">{{ $stat->count }}</h4>
                                <small class="text-muted">{{ $stat->course }} {{ __('course') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">{{ __('No course data') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Technology Statistics -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-code me-2"></i>{{ __('Popular technologies') }}</h5>
            </div>
            <div class="card-body">
                @if($technology_stats->count() > 0)
                    @foreach($technology_stats as $stat)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ $stat->name }}</span>
                        <span class="badge bg-primary">{{ $stat->count }}</span>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">{{ __('No technology data') }}</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>{{ __('Popular tags') }}</h5>
            </div>
            <div class="card-body">
                @if($tag_stats->count() > 0)
                    @foreach($tag_stats as $stat)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ $stat->name }}</span>
                        <span class="badge bg-success">{{ $stat->count }}</span>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">{{ __('No tag data') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Monthly Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>{{ __('Monthly statistics (last 12 months)') }}</h5>
            </div>
            <div class="card-body">
                @if($monthly_stats->count() > 0)
                    <div class="row">
                        @foreach($monthly_stats as $stat)
                        <div class="col-md-2 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-info mb-1">{{ $stat->count }}</h6>
                                <small class="text-muted">{{ \Carbon\Carbon::createFromFormat('Y-m', $stat->month)->format('M Y') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">{{ __('No monthly data') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Project Tasks Analytics -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>{{ __('Project tasks analytics') }}</h5>
            </div>
            <div class="card-body">
                @if($project_tasks_analytics->count() > 0)
                    <div class="row">
                        @foreach($project_tasks_analytics as $analytics)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $analytics['project']->title }}</h6>
                                    <p class="card-text text-muted">{{ Str::limit($analytics['project']->short_description ?? $analytics['project']->description, 80) }}</p>
                                    
                                    <!-- Progress Bar -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">Прогресс выполнения</small>
                                            <small class="text-muted">{{ $analytics['progress_percentage'] }}%</small>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $analytics['progress_percentage'] >= 80 ? 'success' : ($analytics['progress_percentage'] >= 50 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $analytics['progress_percentage'] }}%">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Task Statistics -->
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="border-end">
                                                <h6 class="text-primary mb-1">{{ $analytics['total_tasks'] }}</h6>
                                                <small class="text-muted">Всего</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border-end">
                                                <h6 class="text-success mb-1">{{ $analytics['completed_tasks'] }}</h6>
                                                <small class="text-muted">Выполнено</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <h6 class="text-warning mb-1">{{ $analytics['in_progress_tasks'] }}</h6>
                                            <small class="text-muted">В работе</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <span class="badge bg-{{ $analytics['project']->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($analytics['project']->status) }}
                                        </span>
                                        @if($analytics['project']->created_at)
                                            <small class="text-muted ms-2">
                                                {{ $analytics['project']->created_at->format('d.m.Y') }}
                                            </small>
                                        @endif
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="mt-3 d-flex gap-2">
                                        <a href="{{ route('projects.show', $analytics['project']) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Просмотр проекта
                                        </a>
                                        <a href="{{ route('projects.tasks.index', $analytics['project']) }}" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-tasks me-1"></i>Задачи проекта
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">{{ __('No project tasks data') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
