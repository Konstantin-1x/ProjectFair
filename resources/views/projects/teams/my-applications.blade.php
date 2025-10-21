@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i>
                        Мои заявки команд на проекты
                    </h4>
                </div>
                <div class="card-body">
                    @if($applications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Проект</th>
                                        <th>Команда</th>
                                        <th>Сообщение</th>
                                        <th>Статус</th>
                                        <th>Дата подачи</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($application->project->image)
                                                            <img src="{{ Storage::url($application->project->image) }}" 
                                                                alt="{{ $application->project->title }}" 
                                                                class="rounded" 
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                                                style="width: 50px; height: 50px;">
                                                                <i class="fas fa-project-diagram"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $application->project->title }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $application->project->description }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        @if($application->team->image)
                                                            <img src="{{ Storage::url($application->team->image) }}" 
                                                                alt="{{ $application->team->name }}" 
                                                                class="rounded-circle" 
                                                                style="width: 30px; height: 30px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                                style="width: 30px; height: 30px;">
                                                                <i class="fas fa-users"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $application->team->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $application->team->members->count() }} участников</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($application->message)
                                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $application->message }}">
                                                        {{ $application->message }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">Нет сообщения</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($application->status === 'pending')
                                                    <span class="badge bg-warning">На рассмотрении</span>
                                                @elseif($application->status === 'approved')
                                                    <span class="badge bg-success">Одобрена</span>
                                                @elseif($application->status === 'rejected')
                                                    <span class="badge bg-danger">Отклонена</span>
                                                @elseif($application->status === 'withdrawn')
                                                    <span class="badge bg-secondary">Отозвана</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $application->applied_at->format('d.m.Y H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($application->status === 'pending')
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('projects.show', $application->project) }}" 
                                                            class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye me-1"></i>
                                                            Проект
                                                        </a>
                                                        <form action="{{ route('projects.team-applications.withdraw', [$application->project, $application]) }}" 
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                                onclick="return confirm('Отозвать заявку?')">
                                                                <i class="fas fa-undo me-1"></i>
                                                                Отозвать
                                                            </button>
                                                        </form>
                                                    </div>
                                                @elseif($application->status === 'approved')
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-success me-2">
                                                            <i class="fas fa-check-circle"></i>
                                                        </span>
                                                        <a href="{{ route('projects.show', $application->project) }}" 
                                                            class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye me-1"></i>
                                                            Проект
                                                        </a>
                                                    </div>
                                                @elseif($application->status === 'rejected')
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-danger me-2">
                                                            <i class="fas fa-times-circle"></i>
                                                        </span>
                                                        <a href="{{ route('projects.show', $application->project) }}" 
                                                            class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye me-1"></i>
                                                            Проект
                                                        </a>
                                                    </div>
                                                @elseif($application->status === 'withdrawn')
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-secondary me-2">
                                                            <i class="fas fa-undo"></i>
                                                        </span>
                                                        <a href="{{ route('projects.show', $application->project) }}" 
                                                            class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye me-1"></i>
                                                            Проект
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">У вас нет заявок команд на проекты</h5>
                            <p class="text-muted">Вы еще не подавали заявки от имени ваших команд на участие в проектах.</p>
                            <a href="{{ route('projects.index') }}" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>
                                Найти проекты
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
