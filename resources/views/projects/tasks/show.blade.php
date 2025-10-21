@extends('layouts.app')

@section('title', $task->title . ' - ' . $project->title)

@section('content')
<div class="container">
    <!-- Заголовок -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-tasks me-2"></i>{{ $task->title }}</h1>
                    <p class="text-muted mb-0">Проект: {{ $project->title }}</p>
                </div>
                <div>
                    @if($task->status !== 'completed')
                        <button type="button" class="btn btn-success me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#completeTaskModal">
                            <i class="fas fa-check me-2"></i>Завершить задачу
                        </button>
                    @endif
                    <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>К задачам
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Основная информация -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Описание задачи</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $task->description }}</p>
                </div>
            </div>

            @if($task->status === 'completed' && ($task->completion_text || $task->completion_file))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 text-success">
                        <i class="fas fa-check-circle me-2"></i>Результат выполнения
                    </h5>
                </div>
                <div class="card-body">
                    @if($task->completion_text)
                        <p class="card-text">{{ $task->completion_text }}</p>
                    @endif
                    
                    @if($task->completion_file || $task->files->count() > 0)
                        <div class="mt-3">
                            <h6><i class="fas fa-file-alt me-2"></i>Файлы результата:</h6>
                            
                            @if($task->completion_file)
                                <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-light mb-2">
                                    <div>
                                        <i class="fas fa-file me-2 text-primary"></i>
                                        <strong>{{ basename($task->completion_file) }}</strong>
                                        @if(Storage::exists($task->completion_file))
                                            <br><small class="text-muted">
                                                Размер: {{ number_format(Storage::size($task->completion_file) / 1024, 2) }} KB
                                            </small>
                                        @endif
                                    </div>
                                    <a href="{{ Storage::url($task->completion_file) }}" target="_blank" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-download me-1"></i>Скачать
                                    </a>
                                </div>
                            @endif
                            
                            @if($task->files->count() > 0)
                                @foreach($task->files as $file)
                                    <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-light mb-2">
                                        <div>
                                            <i class="fas fa-file me-2 text-primary"></i>
                                            <strong>{{ $file->original_name }}</strong>
                                            <br><small class="text-muted">
                                                Размер: {{ number_format($file->file_size / 1024, 2) }} KB
                                            </small>
                                        </div>
                                        <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-download me-1"></i>Скачать
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Информация о задаче -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Информация о задаче</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Статус:</strong><br>
                        <span class="badge bg-{{ $task->status === 'completed' ? 'success' : 'secondary' }} fs-6">
                            {{ $task->status === 'completed' ? 'Выполнено' : 'В работе' }}
                        </span>
                    </div>
                    
                    @if($task->type)
                    <div class="mb-3">
                        <strong>Тип:</strong><br>
                        <span class="text-muted">{{ $task->type }}</span>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <strong>Сложность:</strong><br>
                        <span class="badge bg-{{ $task->difficulty === 'easy' ? 'success' : ($task->difficulty === 'medium' ? 'warning' : 'danger') }}">
                            {{ ucfirst($task->difficulty) }}
                        </span>
                    </div>
                    
                    @if($task->deadline)
                    <div class="mb-3">
                        <strong>Срок выполнения:</strong><br>
                        <span class="text-muted">
                            {{ $task->deadline->format('d.m.Y') }}
                            @if($task->deadline->isPast() && $task->status !== 'completed')
                                <span class="text-danger">(просрочено)</span>
                            @endif
                        </span>
                    </div>
                    @endif
                    
                    @if($task->status === 'completed' && $task->completed_at)
                    <div class="mb-3">
                        <strong>Завершено:</strong><br>
                        <span class="text-success">
                            {{ $task->completed_at->format('d.m.Y H:i') }}
                        </span>
                    </div>
                    @endif
                    
                    @if($task->is_basic_task)
                    <div class="mb-3">
                        <span class="badge bg-info">Базовая задача проекта</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Действия -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Действия</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($task->status !== 'completed')
                            <button type="button" class="btn btn-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#completeTaskModal">
                                <i class="fas fa-check me-2"></i>Завершить задачу
                            </button>
                        @endif
                        
                        @if(!$task->is_basic_task)
                            <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}" 
                                  onsubmit="return confirm('Удалить задачу?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash me-2"></i>Удалить задачу
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>К списку задач
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для завершения задачи -->
@if($task->status !== 'completed')
<div class="modal fade" id="completeTaskModal" tabindex="-1">
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
                        <label for="completion_text" class="form-label">Комментарий к завершению (необязательно):</label>
                        <textarea class="form-control" id="completion_text" name="completion_text" rows="3" placeholder="Опишите, что было выполнено..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="completion_files" class="form-label">Файлы результата (необязательно):</label>
                        <input type="file" class="form-control" id="completion_files" name="completion_files[]" 
                               accept=".pdf,.doc,.docx,.txt,.zip,.rar,.jpg,.jpeg,.png,.gif" multiple>
                        <div class="form-text">Поддерживаемые форматы: PDF, DOC, DOCX, TXT, ZIP, RAR, JPG, JPEG, PNG, GIF. Максимальный размер каждого файла: 10MB. Можно выбрать несколько файлов.</div>
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

@endsection
