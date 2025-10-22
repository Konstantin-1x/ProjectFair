@extends('layouts.app')

@section('title', 'Задача - ' . $task->title)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <!-- Заголовок -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1><i class="fas fa-tasks me-2"></i>{{ $task->title }}</h1>
                    <p class="text-muted mb-0">{{ $project->title }}</p>
                </div>
                <div>
                    <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>К задачам
                    </a>
                </div>
            </div>

            <!-- Информация о задаче -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Информация о задаче</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Описание:</h6>
                            <p>{{ $task->description }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Статус:</h6>
                            @if($task->is_rejected)
                                <span class="badge bg-danger">Отклонено</span>
                            @elseif($task->status === 'completed')
                                <span class="badge bg-success">Выполнено</span>
                            @else
                                <span class="badge bg-secondary">В работе</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($task->requirements)
                    <div class="row">
                        <div class="col-12">
                            <h6>Требования:</h6>
                            <p>{{ $task->requirements }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <h6>Сложность:</h6>
                            <span class="badge bg-{{ $task->difficulty === 'easy' ? 'success' : ($task->difficulty === 'medium' ? 'warning' : 'danger') }}">
                                {{ ucfirst($task->difficulty) }}
                            </span>
                        </div>
                        @if($task->type)
                        <div class="col-md-4">
                            <h6>Тип:</h6>
                            <span class="badge bg-info">{{ $task->type }}</span>
                        </div>
                        @endif
                        @if($task->deadline)
                        <div class="col-md-4">
                            <h6>Срок:</h6>
                            <p>{{ $task->deadline->format('d.m.Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Комментарии -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">История выполнения</h5>
                </div>
                <div class="card-body">
                    @if($task->comments->count() > 0)
                        @foreach($task->comments as $comment)
                        <div class="mb-4 p-3 border rounded {{ $comment->type === 'rejection' ? 'border-danger bg-light' : ($comment->type === 'completion' ? 'border-success bg-light' : 'border-primary bg-light') }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        {{ $comment->user->initials() }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $comment->user->name }}</h6>
                                        <small class="text-muted">
                                            @if($comment->type === 'completion')
                                                <i class="fas fa-check text-success me-1"></i>Завершение задачи
                                            @elseif($comment->type === 'rejection')
                                                <i class="fas fa-times text-danger me-1"></i>Отклонение
                                            @else
                                                <i class="fas fa-comment text-primary me-1"></i>Комментарий преподавателя
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <small class="text-muted">{{ $comment->created_at->format('d.m.Y H:i') }}</small>
                            </div>
                            
                            <p class="mb-2">{{ $comment->comment }}</p>
                            
                            @if($comment->attached_files && count($comment->attached_files) > 0)
                                <div class="mt-2">
                                    <h6 class="small">Прикрепленные файлы:</h6>
                                    @foreach($comment->attached_files as $file)
                                        <div class="d-flex align-items-center justify-content-between p-2 border rounded bg-white mb-1">
                                            <div>
                                                <i class="fas fa-file me-2 text-primary"></i>
                                                <strong>{{ $file['original_name'] }}</strong>
                                            </div>
                                            <a href="{{ Storage::url($file['file_path']) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-download me-1"></i>Скачать
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">Пока нет комментариев по этой задаче.</p>
                    @endif
                </div>
            </div>

            <!-- Форма добавления комментария (только для преподавателей) -->
            @if(Auth::user()->isAdmin())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Добавить комментарий</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projects.tasks.comment', [$project, $task]) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="comment" class="form-label">Комментарий <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                      id="comment" name="comment" rows="4" 
                                      placeholder="Добавьте комментарий к задаче..." required>{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="comment_files" class="form-label">Прикрепить файлы (необязательно):</label>
                            <input type="file" class="form-control" id="comment_files" name="comment_files[]" 
                                   accept=".pdf,.doc,.docx,.txt,.zip,.rar,.jpg,.jpeg,.png,.gif" multiple>
                            <div class="form-text">Поддерживаемые форматы: PDF, DOC, DOCX, TXT, ZIP, RAR, JPG, JPEG, PNG, GIF. Максимальный размер каждого файла: 10MB</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-comment me-1"></i>Добавить комментарий
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <!-- Действия -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Действия</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Редактировать
                            </a>
                        @endif
                        
                        @if($task->status !== 'completed' || $task->is_rejected)
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
                                <button type="button" class="btn btn-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#completeTaskModal">
                                    <i class="fas fa-check me-2"></i>{{ $task->is_rejected ? 'Переделать задачу' : 'Завершить задачу' }}
                                </button>
                            @endif
                        @endif
                        
                        @if(Auth::user()->isAdmin())
                            @if($task->status === 'completed' && !$task->is_rejected)
                                <button type="button" class="btn btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#rejectTaskModal">
                                    <i class="fas fa-times me-2"></i>Отклонить выполнение
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно завершения задачи -->
@if($task->status !== 'completed' || $task->is_rejected)
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
                    <p>Вы уверены, что хотите {{ $task->is_rejected ? 'переделать' : 'отметить как выполненную' }} задачу <strong>"{{ $task->title }}"</strong>?</p>
                    <div class="mb-3">
                        <label for="completion_text" class="form-label">Отчет о выполнении <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('completion_text') is-invalid @enderror" 
                                  id="completion_text" name="completion_text" rows="4" 
                                  placeholder="Опишите, что было выполнено, какие результаты получены, какие проблемы возникли..." required></textarea>
                        <div class="form-text">Обязательно опишите результаты работы по задаче</div>
                    </div>
                    <div class="mb-3">
                        <label for="completion_files" class="form-label">Файлы результата (необязательно):</label>
                        <input type="file" class="form-control" id="completion_files" name="completion_files[]" 
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
@endif

<!-- Модальное окно отклонения задачи -->
@if($task->status === 'completed' && !$task->is_rejected && Auth::user()->isAdmin())
<div class="modal fade" id="rejectTaskModal" tabindex="-1">
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
                        <label for="rejection_reason" class="form-label">Причина отклонения <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror" 
                                  id="rejection_reason" name="rejection_reason" rows="4" 
                                  placeholder="Укажите причину отклонения выполнения задачи..." required></textarea>
                        <div class="form-text">Задача будет возвращена в статус "Открыта" и участники смогут переделать её</div>
                    </div>
                    <div class="mb-3">
                        <label for="rejection_files" class="form-label">Прикрепить файлы (необязательно):</label>
                        <input type="file" class="form-control" id="rejection_files" name="rejection_files[]" 
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

@endsection