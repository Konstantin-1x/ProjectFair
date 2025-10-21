@extends('layouts.app')

@section('title', $team->name . ' - Команда')

@section('content')
<div class="container">
    <!-- Team Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                                <div>
                                    <h1 class="mb-1">{{ $team->name }}</h1>
                                    <span class="badge bg-{{ $team->status === 'recruiting' ? 'success' : 'secondary' }} fs-6">
                                        {{ $team->status === 'recruiting' ? 'Набор участников' : ucfirst($team->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($team->description)
                                <p class="text-muted mb-3">{{ $team->description }}</p>
                            @endif
                            
                            @if($team->tags->count() > 0)
                                <div class="mb-3">
                                    <strong>Теги команды:</strong>
                                    <div class="mt-2">
                                        @foreach($team->tags as $tag)
                                            <span class="badge bg-secondary me-1 mb-1">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user me-2 text-primary"></i>
                                        <strong>Лидер:</strong>
                                        <span class="ms-2">{{ $team->leader->name }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-users me-2 text-primary"></i>
                                        <strong>Участников:</strong>
                                        <span class="ms-2">{{ $team->members->count() }}/{{ $team->max_members }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-md-end">
                            @auth
                                @if($team->leader_id === Auth::id())
                                    <div class="d-flex flex-column gap-2">
                                        <a href="{{ route('teams.edit', $team) }}" class="btn btn-warning">
                                            <i class="fas fa-edit me-2"></i>Управлять командой
                                        </a>
                                        <a href="{{ route('teams.applications', $team) }}" class="btn btn-info">
                                            <i class="fas fa-file-alt me-2"></i>Заявки ({{ $team->pendingApplications()->count() }})
                                        </a>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-crown me-1"></i>Вы - лидер команды
                                        </span>
                                    </div>
                                @elseif($team->members()->where('user_id', Auth::id())->exists())
                                    <div class="d-flex flex-column gap-2">
                                        <form method="POST" action="{{ route('teams.leave', $team) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Покинуть команду
                                            </button>
                                        </form>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-user me-1"></i>Вы участник команды
                                        </span>
                                    </div>
                                @elseif($team->canUserApply(Auth::id()))
                                    <a href="{{ route('teams.apply', $team) }}" class="btn btn-success">
                                        <i class="fas fa-paper-plane me-2"></i>Подать заявку
                                    </a>
                                @elseif($team->hasUserApplied(Auth::id()))
                                    @php
                                        $userApplication = $team->getUserApplication(Auth::id());
                                    @endphp
                                    <div class="d-flex flex-column gap-2">
                                        <span class="badge bg-{{ $userApplication->status === 'pending' ? 'warning' : ($userApplication->status === 'approved' ? 'success' : 'danger') }}">
                                            @if($userApplication->status === 'pending')
                                                Заявка на рассмотрении
                                            @elseif($userApplication->status === 'approved')
                                                Заявка одобрена
                                            @else
                                                Заявка отклонена
                                            @endif
                                        </span>
                                        @if($userApplication->status === 'pending')
                                            <form method="POST" action="{{ route('teams.applications.withdraw', [$team, $userApplication]) }}" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('Отозвать заявку?')">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-times me-1"></i>Отозвать заявку
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <span class="badge bg-secondary">Команда полная или не принимает участников</span>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Войти для участия
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Team Members -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Участники команды ({{ $team->members->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($team->members->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($team->members as $member)
                            <div class="list-group-item d-flex align-items-center py-3">
                                <div class="me-3">
                                    @if($member->avatar)
                                        <img src="{{ Storage::url($member->avatar) }}" alt="{{ $member->name }}" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-user fa-lg"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="mb-0 me-2">
                                            {{ $member->name }}
                                            @if($member->id === $team->leader_id)
                                                <i class="fas fa-crown text-warning ms-1"></i>
                                            @endif
                                        </h6>
                                        <span class="badge bg-{{ $member->id === $team->leader_id ? 'warning' : 'primary' }}">
                                            {{ $member->id === $team->leader_id ? 'Лидер' : 'Участник' }}
                                        </span>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-envelope me-1"></i>{{ $member->email }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-calendar me-1"></i>Присоединился: {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d.m.Y H:i') : 'Неизвестно' }}
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    @auth
                                        @if(Auth::user()->isAdmin() && $member->id !== $team->leader_id)
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#removeMemberModal"
                                                    data-member-id="{{ $member->id }}"
                                                    data-member-name="{{ $member->name }}">
                                                <i class="fas fa-user-times me-1"></i>Удалить
                                            </button>
                                        @elseif(Auth::id() === $team->leader_id && $member->id !== $team->leader_id)
                                            <button type="button" class="btn btn-outline-warning btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#excludeMemberModal"
                                                    data-member-id="{{ $member->id }}"
                                                    data-member-name="{{ $member->name }}">
                                                <i class="fas fa-user-minus me-1"></i>Исключить
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">В команде пока нет участников</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Team Projects -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-project-diagram me-2"></i>Проекты команды ({{ $team->projects->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($team->projects->count() > 0)
                        <div class="row">
                            @foreach($team->projects as $project)
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $project->title }}</h6>
                                        <p class="card-text text-muted">{{ Str::limit($project->short_description ?? $project->description, 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-{{ $project->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($project->status) }}
                                            </span>
                                            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Подробнее
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                            <p class="text-muted">У команды пока нет проектов</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Team Info Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Информация о команде
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Дата создания:</strong><br>
                        <span class="text-muted">{{ $team->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    
                    @if($team->recruitment_start || $team->recruitment_end)
                    <div class="mb-3">
                        <strong>Период набора:</strong><br>
                        <span class="text-muted">
                            @if($team->recruitment_start)
                                {{ $team->recruitment_start->format('d.m.Y') }}
                            @endif
                            @if($team->recruitment_start && $team->recruitment_end)
                                -
                            @endif
                            @if($team->recruitment_end)
                                {{ $team->recruitment_end->format('d.m.Y') }}
                            @endif
                        </span>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <strong>Статус:</strong><br>
                        <span class="badge bg-{{ $team->status === 'recruiting' ? 'success' : 'secondary' }}">
                            {{ $team->status === 'recruiting' ? 'Набор участников' : ucfirst($team->status) }}
                        </span>
                    </div>
                    
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ ($team->members->count() / $team->max_members) * 100 }}%">
                            {{ $team->members->count() }}/{{ $team->max_members }}
                        </div>
                    </div>
                    
                    <small class="text-muted">
                        {{ $team->max_members - $team->members->count() }} свободных мест
                    </small>
                </div>
            </div>
            
            <!-- Team Actions -->
            @auth
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Действия
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($team->leader_id === Auth::id())
                            <a href="{{ route('teams.edit', $team) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-2"></i>Редактировать команду
                            </a>
                            <a href="{{ route('teams.applications', $team) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-users me-2"></i>Заявки команды
                            </a>
                            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Создать проект
                            </a>
                        @elseif(!$team->members->contains(Auth::id()) && $team->status === 'recruiting' && $team->members->count() < $team->max_members)
                            <button type="button" class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#applyModal">
                                <i class="fas fa-user-plus me-2"></i>Подать заявку
                            </button>
                        @endif
                        
                        <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Все команды
                        </a>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </div>
</div>

<!-- Application Modal -->
@if(!$team->members->contains(Auth::id()) && $team->status === 'recruiting' && $team->members->count() < $team->max_members)
    <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyModalLabel">
                        <i class="fas fa-user-plus me-2"></i>Подача заявки в команду
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('teams.apply.store', $team) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <h6>Команда: <strong>{{ $team->name }}</strong></h6>
                            <p class="text-muted">Лидер: {{ $team->leader->name }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Сообщение лидеру команды (необязательно):</label>
                            <textarea class="form-control" id="message" name="message" rows="4" 
                                      placeholder="Расскажите о себе, своих навыках и почему хотите присоединиться к этой команде..."></textarea>
                            <div class="form-text">Это сообщение будет отправлено лидеру команды для рассмотрения вашей заявки.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-1"></i>Подать заявку
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Unified Modals -->
<!-- Remove Member Modal -->
<div class="modal fade" id="removeMemberModal" tabindex="-1" aria-labelledby="removeMemberModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeMemberModalLabel">Удаление участника</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="removeMemberText">Вы уверены, что хотите удалить участника из команды?</p>
                <form id="removeMemberForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="reason" class="form-label">Причина удаления (необязательно):</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Укажите причину удаления..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" form="removeMemberForm" class="btn btn-danger">
                    <i class="fas fa-user-times me-1"></i>Удалить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Exclude Member Modal -->
<div class="modal fade" id="excludeMemberModal" tabindex="-1" aria-labelledby="excludeMemberModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="excludeMemberModalLabel">Исключение участника</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="excludeMemberText">Вы уверены, что хотите исключить участника из команды?</p>
                <form id="excludeMemberForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="excludeReason" class="form-label">Причина исключения (необязательно):</label>
                        <textarea class="form-control" id="excludeReason" name="reason" rows="3" placeholder="Укажите причину исключения..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" form="excludeMemberForm" class="btn btn-warning">
                    <i class="fas fa-user-minus me-1"></i>Исключить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle remove member modal
    document.querySelectorAll('[data-bs-target="#removeMemberModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const memberId = this.getAttribute('data-member-id');
            const memberName = this.getAttribute('data-member-name');
            
            document.getElementById('removeMemberText').innerHTML = 
                `Вы уверены, что хотите удалить <strong>${memberName}</strong> из команды?`;
            document.getElementById('removeMemberForm').action = 
                `{{ url('teams/' . $team->id . '/members') }}/${memberId}`;
        });
    });
    
    // Handle exclude member modal
    document.querySelectorAll('[data-bs-target="#excludeMemberModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const memberId = this.getAttribute('data-member-id');
            const memberName = this.getAttribute('data-member-name');
            
            document.getElementById('excludeMemberText').innerHTML = 
                `Вы уверены, что хотите исключить <strong>${memberName}</strong> из команды?`;
            document.getElementById('excludeMemberForm').action = 
                `{{ url('teams/' . $team->id . '/members') }}/${memberId}/exclude`;
        });
    });
});
</script>

@endsection
