@extends('layouts.app')

@section('title', 'Редактирование команды - ' . $team->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Редактирование команды "{{ $team->name }}"
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('teams.update', $team) }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Основная информация -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Основная информация
                        </h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Название команды <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $team->name) }}" 
                                   placeholder="Введите название команды" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание команды</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Опишите цели и задачи команды, требуемые навыки, ожидания от участников...">{{ old('description', $team->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Максимум 1000 символов</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_members" class="form-label">Максимальное количество участников <span class="text-danger">*</span></label>
                                    <select class="form-select @error('max_members') is-invalid @enderror" id="max_members" name="max_members" required>
                                        <option value="">Выберите количество</option>
                                        @for($i = 2; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('max_members', $team->max_members) == $i ? 'selected' : '' }}>
                                                {{ $i }} {{ $i == 1 ? 'участник' : ($i < 5 ? 'участника' : 'участников') }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('max_members')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Статус команды <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="recruiting" {{ old('status', $team->status) === 'recruiting' ? 'selected' : '' }}>
                                            Набор участников
                                        </option>
                                        <option value="active" {{ old('status', $team->status) === 'active' ? 'selected' : '' }}>
                                            Активная
                                        </option>
                                        <option value="completed" {{ old('status', $team->status) === 'completed' ? 'selected' : '' }}>
                                            Завершенная
                                        </option>
                                        <option value="disbanded" {{ old('status', $team->status) === 'disbanded' ? 'selected' : '' }}>
                                            Распущенная
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Период набора -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-calendar me-2"></i>Период набора участников
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="recruitment_start" class="form-label">Дата начала набора</label>
                                    <input type="date" class="form-control @error('recruitment_start') is-invalid @enderror" 
                                           id="recruitment_start" name="recruitment_start" 
                                           value="{{ old('recruitment_start', $team->recruitment_start ? $team->recruitment_start->format('Y-m-d') : '') }}">
                                    @error('recruitment_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="recruitment_end" class="form-label">Дата окончания набора</label>
                                    <input type="date" class="form-control @error('recruitment_end') is-invalid @enderror" 
                                           id="recruitment_end" name="recruitment_end" 
                                           value="{{ old('recruitment_end', $team->recruitment_end ? $team->recruitment_end->format('Y-m-d') : '') }}">
                                    @error('recruitment_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Совет:</strong> Укажите период набора участников, чтобы другие студенты знали, когда можно присоединиться к вашей команде.
                        </div>
                    </div>
                    
                    <!-- Информация о команде -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-users me-2"></i>Информация о команде
                        </h5>
                        
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Текущих участников:</strong>
                                            <span class="badge bg-primary ms-2">{{ $team->members->count() }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Максимальный размер:</strong>
                                            <span class="badge bg-info ms-2">{{ $team->max_members }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Проектов команды:</strong>
                                            <span class="badge bg-success ms-2">{{ $team->projects->count() }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Дата создания:</strong>
                                            <span class="text-muted ms-2">{{ $team->created_at->format('d.m.Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($team->members->count() > 0)
                                    <div class="mt-3">
                                        <strong>Участники команды:</strong>
                                        <div class="mt-2">
                                            @foreach($team->members as $member)
                                                <span class="badge bg-{{ $member->id === $team->leader_id ? 'warning' : 'secondary' }} me-1">
                                                    {{ $member->name }}
                                                    @if($member->id === $team->leader_id)
                                                        <i class="fas fa-crown ms-1"></i>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('teams.show', $team) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Отмена
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Сохранить изменения
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Автоматически устанавливаем минимальную дату для окончания набора
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('recruitment_start');
        const endDateInput = document.getElementById('recruitment_end');
        
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
        });
    });
</script>
@endpush
