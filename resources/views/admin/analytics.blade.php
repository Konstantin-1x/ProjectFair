@extends('layouts.app')

@section('title', 'Аналитика')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-chart-bar me-2"></i>Аналитика</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Назад к панели
    </a>
</div>

<!-- Institute Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-university me-2"></i>Статистика по институтам</h5>
            </div>
            <div class="card-body">
                @if($institute_stats->count() > 0)
                    <div class="row">
                        @foreach($institute_stats as $stat)
                        <div class="col-md-4 mb-3">
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                <div>
                                    <h6 class="mb-1">{{ $stat->institute }}</h6>
                                    <small class="text-muted">Проектов</small>
                                </div>
                                <div class="text-end">
                                    <h4 class="text-primary mb-0">{{ $stat->count }}</h4>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Нет данных по институтам</p>
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
                <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Статистика по курсам</h5>
            </div>
            <div class="card-body">
                @if($course_stats->count() > 0)
                    <div class="row">
                        @foreach($course_stats as $stat)
                        <div class="col-md-2 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-success mb-1">{{ $stat->count }}</h4>
                                <small class="text-muted">{{ $stat->course }} курс</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Нет данных по курсам</p>
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
                <h5 class="mb-0"><i class="fas fa-code me-2"></i>Популярные технологии</h5>
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
                    <p class="text-muted">Нет данных по технологиям</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Популярные теги</h5>
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
                    <p class="text-muted">Нет данных по тегам</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Monthly Statistics -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Статистика по месяцам (последние 12 месяцев)</h5>
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
                    <p class="text-muted">Нет данных по месяцам</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
