@extends('layouts.app')

@section('title', 'Команды')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>
                <i class="fas fa-users me-2"></i>Все команды
            </h1>
            <p class="text-muted mb-0">Просматривайте команды и присоединяйтесь к ним</p>
        </div>
        @auth
            <a href="{{ route('teams.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Создать команду
            </a>
        @endauth
    </div>
    
    <!-- Фильтры -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('teams.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Статус команды</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Все команды</option>
                                <option value="recruiting" {{ request('status') === 'recruiting' ? 'selected' : '' }}>Набор участников</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Активные</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Неактивные</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">Поиск</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Название команды или лидер">
                        </div>
                        <div class="col-md-3">
                            <label for="max_members" class="form-label">Максимальный размер</label>
                            <select class="form-select" id="max_members" name="max_members">
                                <option value="">Любой размер</option>
                                <option value="2" {{ request('max_members') === '2' ? 'selected' : '' }}>2 участника</option>
                                <option value="3" {{ request('max_members') === '3' ? 'selected' : '' }}>3 участника</option>
                                <option value="4" {{ request('max_members') === '4' ? 'selected' : '' }}>4 участника</option>
                                <option value="5" {{ request('max_members') === '5' ? 'selected' : '' }}>5 участников</option>
                                <option value="6" {{ request('max_members') === '6' ? 'selected' : '' }}>6 участников</option>
                                <option value="7" {{ request('max_members') === '7' ? 'selected' : '' }}>7 участников</option>
                                <option value="8" {{ request('max_members') === '8' ? 'selected' : '' }}>8 участников</option>
                                <option value="9" {{ request('max_members') === '9' ? 'selected' : '' }}>9 участников</option>
                                <option value="10" {{ request('max_members') === '10' ? 'selected' : '' }}>10 участников</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Фильтровать
                                </button>
                                <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Сбросить
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
                            <span class="badge bg-{{ $team->status === 'recruiting' ? 'success' : ($team->status === 'active' ? 'primary' : 'secondary') }}">
                                {{ $team->status === 'recruiting' ? 'Набор' : ($team->status === 'active' ? 'Активная' : 'Неактивная') }}
                            </span>
                        </div>
                        
                        @if($team->description)
                            <p class="card-text text-muted flex-grow-1">{{ Str::limit($team->description, 120) }}</p>
                        @endif
                        
                        @if($team->tags->count() > 0)
                            <div class="mb-3">
                                @foreach($team->tags as $tag)
                                    <span class="badge bg-secondary me-1 mb-1">{{ $tag->name }}</span>
                                @endforeach
                            </div>
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
                            <div class="d-flex gap-2">
                                @auth
                                    @if($team->status === 'recruiting' && $team->canUserApply(Auth::id()))
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#applyModal{{ $team->id }}">
                                            <i class="fas fa-user-plus me-1"></i>Подать заявку
                                        </button>
                                    @elseif($team->members->contains(Auth::id()))
                                        <span class="badge bg-primary">Участник</span>
                                    @elseif($team->leader_id === Auth::id())
                                        <span class="badge bg-warning">Лидер</span>
                                    @elseif($team->hasUserApplied(Auth::id()))
                                        @php
                                            $application = $team->applications()->where('user_id', Auth::id())->first();
                                        @endphp
                                        <span class="badge bg-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'approved' ? 'success' : 'secondary') }}">
                                            @if($application->status === 'pending')
                                                Заявка на рассмотрении
                                            @elseif($application->status === 'approved')
                                                Заявка одобрена
                                            @elseif($application->status === 'rejected')
                                                Заявка отклонена
                                            @elseif($application->status === 'withdrawn')
                                                Заявка отозвана
                                            @endif
                                        </span>
                                    @endif
                                @endauth
                                <a href="{{ route('teams.show', $team) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Подробнее
                                </a>
                            </div>
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

<!-- Application Modals -->
@foreach($teams as $team)
    @if($team->status === 'recruiting')
        <div class="modal fade" id="applyModal{{ $team->id }}" tabindex="-1" aria-labelledby="applyModalLabel{{ $team->id }}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="applyModalLabel{{ $team->id }}">
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
                                <label for="message{{ $team->id }}" class="form-label">Сообщение лидеру команды (необязательно):</label>
                                <textarea class="form-control" id="message{{ $team->id }}" name="message" rows="4" 
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
@endforeach

@endsection
