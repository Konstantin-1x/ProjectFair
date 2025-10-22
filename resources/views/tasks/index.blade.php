@extends('layouts.app')

@section('title', 'Задачи')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tasks me-2"></i>Задачи</h1>
        @if(Auth::user()->isAdmin())
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Создать задачу
            </a>
        @endif
    </div>

    <!-- Фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tasks.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Поиск</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Название или описание">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Статус</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Все</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Открытые</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>В работе</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Завершенные</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Закрытые</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="difficulty" class="form-label">Сложность</label>
                        <select class="form-select" id="difficulty" name="difficulty">
                            <option value="">Все</option>
                            <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Легкая</option>
                            <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Средняя</option>
                            <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Сложная</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="institute" class="form-label">Институт</label>
                        <select class="form-select" id="institute" name="institute">
                            <option value="">Все</option>
                            <option value="ИИТиИБ" {{ request('institute') == 'ИИТиИБ' ? 'selected' : '' }}>ИИТиИБ</option>
                            <option value="ИМО" {{ request('institute') == 'ИМО' ? 'selected' : '' }}>ИМО</option>
                            <option value="ИПТИБ" {{ request('institute') == 'ИПТИБ' ? 'selected' : '' }}>ИПТИБ</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="course" class="form-label">Курс</label>
                        <select class="form-select" id="course" name="course">
                            <option value="">Все</option>
                            <option value="1" {{ request('course') == '1' ? 'selected' : '' }}>1 курс</option>
                            <option value="2" {{ request('course') == '2' ? 'selected' : '' }}>2 курс</option>
                            <option value="3" {{ request('course') == '3' ? 'selected' : '' }}>3 курс</option>
                            <option value="4" {{ request('course') == '4' ? 'selected' : '' }}>4 курс</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-outline-primary d-block w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($tasks->isEmpty())
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle me-2"></i>Задачи не найдены.
        </div>
    @else
        <div class="row">
            @foreach ($tasks as $task)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $task->title }}</h6>
                            @switch($task->status)
                                @case('open')
                                    <span class="badge bg-success">Открыта</span>
                                    @break
                                @case('in_progress')
                                    <span class="badge bg-warning text-dark">В работе</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-primary">Завершена</span>
                                    @break
                                @case('closed')
                                    <span class="badge bg-secondary">Закрыта</span>
                                    @break
                            @endswitch
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ Str::limit($task->description, 100) }}</p>
                            
                            <div class="mb-2">
                                <strong>Сложность:</strong>
                                @switch($task->difficulty)
                                    @case('easy')
                                        <span class="badge bg-success">Легкая</span>
                                        @break
                                    @case('medium')
                                        <span class="badge bg-warning text-dark">Средняя</span>
                                        @break
                                    @case('hard')
                                        <span class="badge bg-danger">Сложная</span>
                                        @break
                                @endswitch
                            </div>

                            @if($task->project)
                                <div class="mb-2">
                                    <strong>Проект:</strong>
                                    <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                                        {{ $task->project->title }}
                                    </a>
                                </div>
                            @endif

                            @if($task->assignedUser)
                                <div class="mb-2">
                                    <strong>Назначена:</strong>
                                    <a href="{{ route('users.show', $task->assignedUser) }}" class="text-decoration-none">
                                        {{ $task->assignedUser->name }}
                                    </a>
                                </div>
                            @endif

                            <div class="mb-2">
                                <strong>Создана:</strong>
                                <small class="text-muted">{{ $task->created_at->format('d.m.Y H:i') }}</small>
                            </div>

                            @if($task->deadline)
                                <div class="mb-2">
                                    <strong>Дедлайн:</strong>
                                    <small class="text-muted">{{ $task->deadline->format('d.m.Y') }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> Просмотр
                                </a>
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-edit"></i> Редактировать
                                    </a>
                                @endif
                                @if($task->assigned_user_id === Auth::id() && $task->status !== 'completed')
                                    <form action="{{ route('tasks.complete', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-check"></i> Завершить
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $tasks->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
