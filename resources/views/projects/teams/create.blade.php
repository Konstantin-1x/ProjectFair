@extends('layouts.app')

@section('title', 'Добавить команду к проекту - ' . $project->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Добавить команду к проекту
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('projects.teams.store', $project) }}">
                    @csrf
                    
                    <!-- Информация о проекте -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Информация о проекте
                        </h5>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Проект:</strong> {{ $project->title }}
                        </div>
                    </div>
                    
                    <!-- Выбор команды -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-users me-2"></i>Выбор команды
                        </h5>
                        
                        @if($availableTeams->count() > 0)
                            <div class="mb-3">
                                <label for="team_id" class="form-label">Команда <span class="text-danger">*</span></label>
                                <select class="form-select @error('team_id') is-invalid @enderror" id="team_id" name="team_id" required>
                                    <option value="">Выберите команду</option>
                                    @foreach($availableTeams as $team)
                                        <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }} ({{ $team->leader->name }}) - {{ $team->members->count() }} участников
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                У вас нет доступных команд для добавления к проекту. 
                                <a href="{{ route('teams.create') }}" class="alert-link">Создайте новую команду</a>.
                            </div>
                        @endif
                    </div>
                    
                    <!-- Роль команды -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-tasks me-2"></i>Роль в проекте
                        </h5>
                        
                        <div class="mb-3">
                            <label for="role_description" class="form-label">Описание роли команды</label>
                            <textarea class="form-control @error('role_description') is-invalid @enderror" 
                                      id="role_description" name="role_description" rows="4" 
                                      placeholder="Опишите, какую роль будет выполнять команда в проекте, какие задачи ей поручены...">{{ old('role_description') }}</textarea>
                            @error('role_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Опишите, какие задачи и ответственность возлагаются на команду в рамках проекта.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Сроки -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-calendar-alt me-2"></i>Сроки выполнения
                        </h5>
                        
                        <div class="mb-3">
                            <label for="deadline" class="form-label">Срок выполнения задач командой</label>
                            <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                   id="deadline" name="deadline" value="{{ old('deadline') }}">
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Укажите срок, до которого команда должна выполнить свои задачи в проекте.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('projects.teams.index', $project) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Отмена
                        </a>
                        @if($availableTeams->count() > 0)
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Добавить команду
                            </button>
                        @endif
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
