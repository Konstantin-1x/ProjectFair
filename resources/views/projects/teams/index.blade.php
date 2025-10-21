@extends('layouts.app')

@section('title', 'Команды проекта - ' . $project->title)

@section('content')
<div class="container">
    <!-- Заголовок -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-users me-2"></i>Команды проекта</h1>
                    <p class="text-muted mb-0">{{ $project->title }}</p>
                </div>
                <div>
                    <a href="{{ route('projects.teams.create', $project) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Добавить команду
                    </a>
                    <a href="{{ route('projects.team-applications.index', $project) }}" class="btn btn-info">
                        <i class="fas fa-file-alt me-2"></i>Заявки команд
                    </a>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>К проекту
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Информация о сроках -->
    @if($project->project_deadline || $project->team_join_deadline || $project->individual_join_deadline)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Сроки проекта</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($project->project_deadline)
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-primary">Срок завершения проекта</h6>
                                <p class="mb-0">{{ $project->project_deadline instanceof \Carbon\Carbon ? $project->project_deadline->format('d.m.Y') : $project->project_deadline }}</p>
                                @if($project->isProjectDeadlinePassed())
                                    <small class="text-danger">Просрочено</small>
                                @else
                                    <small class="text-muted">{{ $project->project_deadline instanceof \Carbon\Carbon ? $project->project_deadline->diffForHumans() : $project->project_deadline }}</small>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($project->team_join_deadline)
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-info">Срок вступления команд</h6>
                                <p class="mb-0">{{ $project->team_join_deadline instanceof \Carbon\Carbon ? $project->team_join_deadline->format('d.m.Y') : $project->team_join_deadline }}</p>
                                @if($project->isTeamJoinDeadlinePassed())
                                    <small class="text-danger">Просрочено</small>
                                @else
                                    <small class="text-muted">{{ $project->team_join_deadline instanceof \Carbon\Carbon ? $project->team_join_deadline->diffForHumans() : $project->team_join_deadline }}</small>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($project->individual_join_deadline)
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-success">Срок вступления одиночных участников</h6>
                                <p class="mb-0">{{ $project->individual_join_deadline instanceof \Carbon\Carbon ? $project->individual_join_deadline->format('d.m.Y') : $project->individual_join_deadline }}</p>
                                @if($project->isIndividualJoinDeadlinePassed())
                                    <small class="text-danger">Просрочено</small>
                                @else
                                    <small class="text-muted">{{ $project->individual_join_deadline instanceof \Carbon\Carbon ? $project->individual_join_deadline->diffForHumans() : $project->individual_join_deadline }}</small>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Список команд -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Участники проекта ({{ $project->teams->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($project->teams->count() > 0)
                        <div class="row">
                            @foreach($project->teams as $team)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 {{ $team->pivot->status === 'completed' ? 'border-success' : ($team->pivot->status === 'withdrawn' ? 'border-danger' : 'border-primary') }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">{{ $team->name }}</h6>
                                            <span class="badge bg-{{ $team->pivot->status === 'completed' ? 'success' : ($team->pivot->status === 'withdrawn' ? 'danger' : 'primary') }}">
                                                {{ $team->pivot->status === 'completed' ? 'Завершено' : ($team->pivot->status === 'withdrawn' ? 'Отозвано' : 'Активно') }}
                                            </span>
                                        </div>
                                        
                                        @if($team->pivot->role_description)
                                            <p class="card-text text-muted small">
                                                {{ Str::limit($team->pivot->role_description, 100) }}
                                            </p>
                                        @endif
                                        
                                        <div class="mb-2">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-user me-1"></i>Лидер: {{ $team->leader->name }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-users me-1"></i>Участников: {{ $team->members->count() }}
                                            </small>
                                            @if($team->pivot->joined_at)
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    Присоединилась: {{ $team->pivot->joined_at instanceof \Carbon\Carbon ? $team->pivot->joined_at->format('d.m.Y') : $team->pivot->joined_at }}
                                                </small>
                                            @endif
                                            @if($team->pivot->deadline)
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Срок: {{ $team->pivot->deadline instanceof \Carbon\Carbon ? $team->pivot->deadline->format('d.m.Y') : $team->pivot->deadline }}
                                                </small>
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('teams.show', $team) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Подробнее
                                            </a>
                                            
                                            @if($project->created_by === Auth::id())
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-cog me-1"></i>Управление
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="updateTeamStatus({{ $team->id }}, 'completed')">
                                                                <i class="fas fa-check me-2"></i>Отметить как завершенную
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="updateTeamStatus({{ $team->id }}, 'withdrawn')">
                                                                <i class="fas fa-times me-2"></i>Отозвать участие
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="updateTeamStatus({{ $team->id }}, 'active')">
                                                                <i class="fas fa-play me-2"></i>Вернуть в активные
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form method="POST" action="{{ route('projects.teams.destroy', [$project, $team]) }}" 
                                                                  onsubmit="return confirm('Удалить команду из проекта?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash me-2"></i>Удалить из проекта
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">К проекту пока не присоединились команды</p>
                            <a href="{{ route('projects.teams.create', $project) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Добавить первую команду
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Форма для обновления статуса команды -->
<form id="updateStatusForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="statusInput">
</form>

@push('scripts')
<script>
function updateTeamStatus(teamId, status) {
    if (confirm('Вы уверены, что хотите изменить статус команды?')) {
        const form = document.getElementById('updateStatusForm');
        form.action = '{{ route("projects.teams.update_status", [$project, ":team"]) }}'.replace(':team', teamId);
        document.getElementById('statusInput').value = status;
        form.submit();
    }
}
</script>
@endpush
@endsection
