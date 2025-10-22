@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $task->title }}</h4>
                    <div>
                        @switch($task->status)
                            @case('open')
                                <span class="badge bg-success fs-6">Открыта</span>
                                @break
                            @case('in_progress')
                                <span class="badge bg-warning text-dark fs-6">В работе</span>
                                @break
                            @case('completed')
                                <span class="badge bg-primary fs-6">Завершена</span>
                                @break
                            @case('closed')
                                <span class="badge bg-secondary fs-6">Закрыта</span>
                                @break
                        @endswitch
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Описание</h5>
                        <p class="text-muted">{{ $task->description }}</p>
                    </div>

                    @if($task->requirements)
                        <div class="mb-4">
                            <h5>Требования</h5>
                            <p class="text-muted">{{ $task->requirements }}</p>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Сложность</h6>
                            @switch($task->difficulty)
                                @case('easy')
                                    <span class="badge bg-success fs-6">Легкая</span>
                                    @break
                                @case('medium')
                                    <span class="badge bg-warning text-dark fs-6">Средняя</span>
                                    @break
                                @case('hard')
                                    <span class="badge bg-danger fs-6">Сложная</span>
                                    @break
                            @endswitch
                        </div>
                        <div class="col-md-6">
                            <h6>Тип</h6>
                            <span class="text-muted">{{ $task->type ?? 'Не указан' }}</span>
                        </div>
                    </div>

                    @if($task->deadline)
                        <div class="mb-4">
                            <h6>Дедлайн</h6>
                            <span class="text-muted">{{ $task->deadline->format('d.m.Y H:i') }}</span>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h6>Файлы</h6>
                        @if($task->files && $task->files->count() > 0)
                            <div class="list-group">
                                @foreach($task->files as $file)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-file me-2"></i>
                                            <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="text-decoration-none">
                                                {{ $file->original_name }}
                                            </a>
                                            <small class="text-muted ms-2">({{ number_format($file->file_size / 1024, 2) }} KB)</small>
                                        </div>
                                        <small class="text-muted">{{ $file->created_at->format('d.m.Y H:i') }}</small>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Файлы не прикреплены</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Информация о задаче</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Создатель</h6>
                        <a href="{{ route('users.show', $task->creator) }}" class="text-decoration-none">
                            <i class="fas fa-user me-2"></i>{{ $task->creator->name }}
                        </a>
                    </div>

                    @if($task->project)
                        <div class="mb-3">
                            <h6>Проект</h6>
                            <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                                <i class="fas fa-project-diagram me-2"></i>{{ $task->project->title }}
                            </a>
                        </div>
                    @endif

                    @if($task->assignedUser)
                        <div class="mb-3">
                            <h6>Назначена</h6>
                            <a href="{{ route('users.show', $task->assignedUser) }}" class="text-decoration-none">
                                <i class="fas fa-user-check me-2"></i>{{ $task->assignedUser->name }}
                            </a>
                        </div>
                    @endif

                    <div class="mb-3">
                        <h6>Создана</h6>
                        <span class="text-muted">{{ $task->created_at->format('d.m.Y H:i') }}</span>
                    </div>

                    @if($task->assigned_at)
                        <div class="mb-3">
                            <h6>Назначена</h6>
                            <span class="text-muted">{{ $task->assigned_at->format('d.m.Y H:i') }}</span>
                        </div>
                    @endif

                    @if($task->completed_at)
                        <div class="mb-3">
                            <h6>Завершена</h6>
                            <span class="text-muted">{{ $task->completed_at->format('d.m.Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Действия</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Редактировать
                            </a>
                        @endif

                        @if($task->assigned_user_id === Auth::id() && $task->status !== 'completed')
                            <form action="{{ route('tasks.complete', $task) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check me-2"></i>Завершить задачу
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Назад к списку
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
