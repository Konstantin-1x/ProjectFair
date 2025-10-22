@extends('layouts.app')

@section('title', 'Задачи проекта - ' . $project->title)

@section('content')
<div class="container">
    <!-- Заголовок -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-tasks me-2"></i>Задачи проекта</h1>
                    <p class="text-muted mb-0">{{ $project->title }}</p>
                </div>
                <div>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Добавить задачу
                        </a>
                    @endif
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>К проекту
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Прогресс проекта -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Прогресс выполнения</h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Выполнено задач: {{ $progress['completed'] }} из {{ $progress['total'] }}</span>
                        <span class="badge bg-primary">{{ $progress['percentage'] }}%</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-{{ $progress['percentage'] >= 80 ? 'success' : ($progress['percentage'] >= 50 ? 'warning' : 'danger') }}" 
                             role="progressbar" 
                             style="width: {{ $progress['percentage'] }}%">
                            {{ $progress['percentage'] }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Список задач -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Список задач ({{ $tasks->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($tasks->count() > 0)
                        <div class="row">
                            @foreach($tasks as $task)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 {{ $task->status === 'completed' ? 'border-success' : 'border-primary' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">{{ $task->title }}</h6>
                                            @if($task->is_basic_task)
                                                <span class="badge bg-info">Базовая</span>
                                            @endif
                                        </div>
                                        
                                        <p class="card-text text-muted small">
                                            {{ Str::limit($task->description, 100) }}
                                        </p>
                                        
                                        <div class="mb-2">
                                            @if($task->is_rejected)
                                                <span class="badge bg-danger">Отклонено</span>
                                            @elseif($task->status === 'completed')
                                                <span class="badge bg-success">Выполнено</span>
                                            @else
                                                <span class="badge bg-secondary">В работе</span>
                                            @endif
                                            <span class="badge bg-{{ $task->difficulty === 'easy' ? 'success' : ($task->difficulty === 'medium' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($task->difficulty) }}
                                            </span>
                                        </div>
                                        
                                        @if($task->type)
                                            <small class="text-muted d-block mb-2">
                                                <i class="fas fa-tag me-1"></i>{{ $task->type }}
                                            </small>
                                        @endif
                                        
                                        @if($task->deadline)
                                            <small class="text-muted d-block mb-2">
                                                <i class="fas fa-calendar me-1"></i>
                                                Срок: {{ $task->deadline->format('d.m.Y') }}
                                            </small>
                                        @endif
                                        
                                        @if($task->is_rejected)
                                            <div class="alert alert-danger py-2 px-3 mb-2">
                                                <small>
                                                    <i class="fas fa-times-circle me-1"></i>
                                                    <strong>Отклонено:</strong> {{ $task->rejection_reason }}
                                                </small>
                                            </div>
                                        @elseif($task->status === 'completed' && $task->completed_at)
                                            <small class="text-success d-block mb-2">
                                                <i class="fas fa-check me-1"></i>
                                                Завершено: {{ $task->completed_at->format('d.m.Y H:i') }}
                                            </small>
                                        @endif
                                        
                                        <div class="d-flex gap-2 flex-wrap">
                                            <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Подробнее
                                            </a>
                                            
                                            @if(Auth::user()->isAdmin())
                                                <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-outline-warning btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Редактировать
                                                </a>
                                            @endif
                                            
                                            @if($task->status !== 'completed' && !$task->is_rejected)
                                                @php
                                                    $isProjectMember = false;
                                                    foreach($project->teams as $team) {
                                                        if($team->members()->where('user_id', Auth::id())->exists()) {
                                                            $isProjectMember = true;
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                @if(Auth::user()->isAdmin() || $isProjectMember)
                                                    <button type="button" class="btn btn-success btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#completeTaskModal{{ $task->id }}">
                                                        <i class="fas fa-check me-1"></i>Завершить
                                                    </button>
                                                @endif
                                            @endif
                                            
                                            @if(Auth::user()->isAdmin())
                                                @if($task->status === 'completed' && !$task->is_rejected)
                                                    <button type="button" class="btn btn-warning btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#rejectTaskModal{{ $task->id }}">
                                                        <i class="fas fa-times me-1"></i>Отклонить
                                                    </button>
                                                @endif
                                            @endif
                                            
                                            @if(!$task->is_basic_task && Auth::user()->isAdmin())
                                                <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}" 
                                                      class="d-inline" 
                                                      onsubmit="return confirm('Удалить задачу?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash me-1"></i>Удалить
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <p class="text-muted">У проекта пока нет задач</p>
                            <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Создать первую задачу
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальные окна для завершения задач -->
@foreach($tasks as $task)
@if($task->status !== 'completed')
<div class="modal fade" id="completeTaskModal{{ $task->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Завершить задачу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('projects.tasks.complete', [$project, $task]) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p>Вы уверены, что хотите отметить задачу <strong>"{{ $task->title }}"</strong> как выполненную?</p>
                    <div class="mb-3">
                        <label for="completion_text{{ $task->id }}" class="form-label">Отчет о выполнении <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('completion_text') is-invalid @enderror" 
                                  id="completion_text{{ $task->id }}" name="completion_text" rows="4" 
                                  placeholder="Опишите, что было выполнено, какие результаты получены, какие проблемы возникли..." required></textarea>
                        <div class="form-text">Обязательно опишите результаты работы по задаче</div>
                    </div>
                    <div class="mb-3">
                        <label for="completion_files{{ $task->id }}" class="form-label">Файлы результата (необязательно):</label>
                        <input type="file" class="form-control" id="completion_files{{ $task->id }}" name="completion_files[]" 
                               accept=".pdf,.doc,.docx,.txt,.zip,.rar,.jpg,.jpeg,.png,.gif" multiple>
                        <div class="form-text">Поддерживаемые форматы: PDF, DOC, DOCX, TXT, ZIP, RAR, JPG, JPEG, PNG, GIF. Максимальный размер каждого файла: 10MB</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Завершить задачу
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- Модальные окна для отклонения задач -->
@foreach($tasks as $task)
@if($task->status === 'completed' && !$task->is_rejected && Auth::user()->isAdmin())
<div class="modal fade" id="rejectTaskModal{{ $task->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Отклонить выполнение задачи</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('projects.tasks.reject', [$project, $task]) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p>Вы уверены, что хотите отклонить выполнение задачи <strong>"{{ $task->title }}"</strong>?</p>
                    <div class="mb-3">
                        <label for="rejection_reason{{ $task->id }}" class="form-label">Причина отклонения <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror" 
                                  id="rejection_reason{{ $task->id }}" name="rejection_reason" rows="4" 
                                  placeholder="Укажите причину отклонения выполнения задачи..." required></textarea>
                        <div class="form-text">Задача будет возвращена в статус "Открыта" и участники смогут переделать её</div>
                    </div>
                    <div class="mb-3">
                        <label for="rejection_files{{ $task->id }}" class="form-label">Прикрепить файлы (необязательно):</label>
                        <input type="file" class="form-control" id="rejection_files{{ $task->id }}" name="rejection_files[]" 
                               accept=".pdf,.doc,.docx,.txt,.zip,.rar,.jpg,.jpeg,.png,.gif" multiple>
                        <div class="form-text">Поддерживаемые форматы: PDF, DOC, DOCX, TXT, ZIP, RAR, JPG, JPEG, PNG, GIF. Максимальный размер каждого файла: 10MB</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-times me-1"></i>Отклонить выполнение
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection
