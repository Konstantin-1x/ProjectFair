@extends('layouts.app')

@section('title', 'Создание задачи - ' . $project->title)

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
                <form method="POST" action="{{ route('projects.tasks.store', $project) }}">
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
                    </div>
                    
                    <!-- Классификация -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-tags me-2"></i>Классификация
                        </h5>
                        
                        <div class="row">
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
                                        <option value="Организационная" {{ old('type') === 'Организационная' ? 'selected' : '' }}>Организационная</option>
                                        <option value="Другое" {{ old('type') === 'Другое' ? 'selected' : '' }}>Другое</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
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
                        </div>
                    </div>
                    
                    <!-- Сроки -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-calendar me-2"></i>Сроки
                        </h5>
                        
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
                    
                    <!-- Информация о проекте -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-project-diagram me-2"></i>Информация о проекте
                        </h5>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Проект:</strong> {{ $project->title }}
                        </div>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Отмена
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Создать задачу
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
@endsection
