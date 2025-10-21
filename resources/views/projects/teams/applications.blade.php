@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Заявки команд на проект "{{ $project->title }}"
                    </h4>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Назад к проекту
                    </a>
                </div>
                <div class="card-body">
                    @if($applications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Команда</th>
                                        <th>Лидер команды</th>
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
                                                        @if($application->team->image)
                                                            <img src="{{ Storage::url($application->team->image) }}" 
                                                                alt="{{ $application->team->name }}" 
                                                                class="rounded-circle" 
                                                                style="width: 40px; height: 40px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                                style="width: 40px; height: 40px;">
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
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        @if($application->appliedBy->avatar)
                                                            <img src="{{ Storage::url($application->appliedBy->avatar) }}" 
                                                                alt="{{ $application->appliedBy->name }}" 
                                                                class="rounded-circle" 
                                                                style="width: 30px; height: 30px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                                style="width: 30px; height: 30px;">
                                                                <i class="fas fa-user"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $application->appliedBy->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $application->appliedBy->email }}</small>
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
                                                        <form action="{{ route('projects.team-applications.approve', [$project, $application]) }}" 
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm" 
                                                                onclick="return confirm('Одобрить заявку этой команды?')">
                                                                <i class="fas fa-check me-1"></i>
                                                                Одобрить
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('projects.team-applications.reject', [$project, $application]) }}" 
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                onclick="return confirm('Отклонить заявку этой команды?')">
                                                                <i class="fas fa-times me-1"></i>
                                                                Отклонить
                                                            </button>
                                                        </form>
                                                    </div>
                                                @elseif($application->status === 'approved')
                                                    <span class="text-success">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        Команда добавлена к проекту
                                                    </span>
                                                @elseif($application->status === 'rejected')
                                                    <span class="text-danger">
                                                        <i class="fas fa-times-circle me-1"></i>
                                                        Заявка отклонена
                                                    </span>
                                                @elseif($application->status === 'withdrawn')
                                                    <span class="text-secondary">
                                                        <i class="fas fa-undo me-1"></i>
                                                        Заявка отозвана
                                                    </span>
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
                            <h5 class="text-muted">Заявок команд пока нет</h5>
                            <p class="text-muted">Команды еще не подавали заявки на участие в этом проекте.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
