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
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="institute" class="form-label">Институт</label>
                                    <select class="form-select @error('institute') is-invalid @enderror" id="institute" name="institute">
                                        <option value="">Выберите институт</option>
                                        <option value="ИИТУТ" {{ old('institute') === 'ИИТУТ' ? 'selected' : '' }}>ИИТУТ</option>
                                        <option value="ИМО" {{ old('institute') === 'ИМО' ? 'selected' : '' }}>ИМО</option>
                                        <option value="ИППИ" {{ old('institute') === 'ИППИ' ? 'selected' : '' }}>ИППИ</option>
                                        <option value="ИЭУП" {{ old('institute') === 'ИЭУП' ? 'selected' : '' }}>ИЭУП</option>
                                        <option value="ИФКС" {{ old('institute') === 'ИФКС' ? 'selected' : '' }}>ИФКС</option>
                                    </select>
                                    @error('institute')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course" class="form-label">Курс</label>
                                    <select class="form-select @error('course') is-invalid @enderror" id="course" name="course">
                                        <option value="">Выберите курс</option>
                                        @for($i = 1; $i <= 6; $i++)
                                            <option value="{{ $i }}" {{ old('course') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                    @error('course')
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
                                    <label for="max_team_size" class="form-label">Максимальный размер команды <span class="text-danger">*</span></label>
                                    <select class="form-select @error('max_team_size') is-invalid @enderror" id="max_team_size" name="max_team_size" required>
                                        <option value="">Выберите размер команды</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('max_team_size') == $i ? 'selected' : '' }}>
                                                {{ $i }} {{ $i == 1 ? 'участник' : ($i < 5 ? 'участника' : 'участников') }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('max_team_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
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
                    
                    <!-- Назначение команде -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-users me-2"></i>Назначение команде
                        </h5>
                        
                        <div class="mb-3">
                            <label for="assigned_team_id" class="form-label">Назначить команде</label>
                            <select class="form-select @error('assigned_team_id') is-invalid @enderror" id="assigned_team_id" name="assigned_team_id">
                                <option value="">Оставить открытой для всех</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('assigned_team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }} ({{ $team->leader->name }}) - {{ $team->members->count() }}/{{ $team->max_members }} участников
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_team_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Если выберете команду, задача будет назначена только ей. Иначе любая команда сможет взять задачу.</div>
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
                        <button type="submit" class="btn btn-primary">
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
