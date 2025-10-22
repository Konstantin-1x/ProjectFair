@extends('layouts.app')

@section('title', 'Редактирование задачи')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Редактирование задачи
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Название задачи <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $task->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Описание <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" required>{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">Требования</label>
                            <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                      id="requirements" name="requirements" rows="3">{{ old('requirements', $task->requirements) }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="difficulty" class="form-label">Сложность <span class="text-danger">*</span></label>
                                    <select class="form-select @error('difficulty') is-invalid @enderror" 
                                            id="difficulty" name="difficulty" required>
                                        <option value="easy" {{ old('difficulty', $task->difficulty) == 'easy' ? 'selected' : '' }}>Легкая</option>
                                        <option value="medium" {{ old('difficulty', $task->difficulty) == 'medium' ? 'selected' : '' }}>Средняя</option>
                                        <option value="hard" {{ old('difficulty', $task->difficulty) == 'hard' ? 'selected' : '' }}>Сложная</option>
                                    </select>
                                    @error('difficulty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Статус <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="open" {{ old('status', $task->status) == 'open' ? 'selected' : '' }}>Открыта</option>
                                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>В работе</option>
                                        <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Завершена</option>
                                        <option value="closed" {{ old('status', $task->status) == 'closed' ? 'selected' : '' }}>Закрыта</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Тип задачи</label>
                                    <input type="text" class="form-control @error('type') is-invalid @enderror" 
                                           id="type" name="type" value="{{ old('type', $task->type) }}">
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="deadline" class="form-label">Дедлайн</label>
                                    <input type="datetime-local" class="form-control @error('deadline') is-invalid @enderror" 
                                           id="deadline" name="deadline" 
                                           value="{{ old('deadline', $task->deadline ? $task->deadline->format('Y-m-d\TH:i') : '') }}">
                                    @error('deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="project_id" class="form-label">Проект <span class="text-danger">*</span></label>
                                    <select class="form-select @error('project_id') is-invalid @enderror" 
                                            id="project_id" name="project_id" required>
                                        <option value="">-- Выберите проект --</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" 
                                                    {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                                {{ $project->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="assigned_user_id" class="form-label">Назначить пользователю</label>
                                    <select class="form-select @error('assigned_user_id') is-invalid @enderror" 
                                            id="assigned_user_id" name="assigned_user_id">
                                        <option value="">-- Не назначено --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                    {{ old('assigned_user_id', $task->assigned_user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Отмена
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
