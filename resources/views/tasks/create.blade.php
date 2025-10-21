@extends('layouts.app')

@section('title', 'Создание задачи')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Создание новой задачи
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('tasks.store') }}">
                    @csrf
                    
                    <!-- Основная информация -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Основная информация
                        </h5>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Название задачи <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="Введите название задачи" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание задачи <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5" 
                                      placeholder="Подробно опишите задачу, что нужно сделать, какие результаты ожидаются..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="requirements" class="form-label">Требования к решению</label>
                            <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                      id="requirements" name="requirements" rows="4" 
                                      placeholder="Укажите технические требования, ограничения, критерии оценки...">{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Проект -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-project-diagram me-2"></i>Проект
                        </h5>
                        
                        @if($projects->count() > 0)
                            <div class="mb-3">
                                <label for="project_id" class="form-label">Выберите проект <span class="text-danger">*</span></label>
                                <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id" required>
                                    <option value="">Выберите проект</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ (old('project_id') == $project->id || ($selectedProject && $selectedProject->id == $project->id)) ? 'selected' : '' }}>
                                            {{ $project->title }} - {{ $project->status }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Задача будет привязана к выбранному проекту (только ваши проекты)</div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>У вас нет активных проектов!</strong>
                                <p class="mb-2">Для создания задачи необходимо сначала создать проект.</p>
                                <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>Создать проект
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Классификация -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-tags me-2"></i>Классификация
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="difficulty" class="form-label">Сложность <span class="text-danger">*</span></label>
                                    <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty" required>
                                        <option value="">Выберите сложность</option>
                                        <option value="easy" {{ old('difficulty') === 'easy' ? 'selected' : '' }}>
                                            🟢 Легкая - для начинающих
                                        </option>
                                        <option value="medium" {{ old('difficulty') === 'medium' ? 'selected' : '' }}>
                                            🟡 Средняя - требует опыта
                                        </option>
                                        <option value="hard" {{ old('difficulty') === 'hard' ? 'selected' : '' }}>
                                            🔴 Сложная - для экспертов
                                        </option>
                                    </select>
                                    @error('difficulty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Тип задачи</label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                                        <option value="">Выберите тип задачи</option>
                                        <option value="Разработка" {{ old('type') === 'Разработка' ? 'selected' : '' }}>Разработка</option>
                                        <option value="Исследование" {{ old('type') === 'Исследование' ? 'selected' : '' }}>Исследование</option>
                                        <option value="Дизайн" {{ old('type') === 'Дизайн' ? 'selected' : '' }}>Дизайн</option>
                                        <option value="Тестирование" {{ old('type') === 'Тестирование' ? 'selected' : '' }}>Тестирование</option>
                                        <option value="Документация" {{ old('type') === 'Документация' ? 'selected' : '' }}>Документация</option>
                                        <option value="Анализ" {{ old('type') === 'Анализ' ? 'selected' : '' }}>Анализ</option>
                                        <option value="Другое" {{ old('type') === 'Другое' ? 'selected' : '' }}>Другое</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Параметры выполнения -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-cogs me-2"></i>Параметры выполнения
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="deadline" class="form-label">Срок выполнения</label>
                                    <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                           id="deadline" name="deadline" value="{{ old('deadline') }}">
                                    @error('deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Оставьте пустым, если срок не ограничен</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Назначение пользователю -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-user me-2"></i>Назначение пользователю
                        </h5>
                        
                        <div class="mb-3">
                            <label for="assigned_user_id" class="form-label">Назначить пользователю</label>
                            <select class="form-select @error('assigned_user_id') is-invalid @enderror" id="assigned_user_id" name="assigned_user_id">
                                <option value="">Оставить открытой для всех</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Если выберете пользователя, задача будет назначена только ему. Иначе любой пользователь сможет взять задачу.</div>
                        </div>
                    </div>
                    
                    <!-- Информация о создателе -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-user me-2"></i>Информация о создателе
                        </h5>
                        
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                                        <small class="text-muted">{{ Auth::user()->email }}</small>
                                        @if(Auth::user()->institute && Auth::user()->course)
                                            <br><small class="text-muted">{{ Auth::user()->institute }}, {{ Auth::user()->course }} курс</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Отмена
                        </a>
                        <button type="submit" class="btn btn-primary" {{ $projects->count() == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-save me-2"></i>Создать задачу
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Автоматически устанавливаем минимальную дату на завтра
    document.addEventListener('DOMContentLoaded', function() {
        const deadlineInput = document.getElementById('deadline');
        if (!deadlineInput.value) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            deadlineInput.min = tomorrow.toISOString().split('T')[0];
        }
    });
</script>
@endpush
