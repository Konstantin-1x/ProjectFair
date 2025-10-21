@extends('layouts.app')

@section('title', 'Подача заявки в команду - ' . $team->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>Подача заявки в команду "{{ $team->name }}"
                </h4>
            </div>
            <div class="card-body">
                <!-- Информация о команде -->
                <div class="mb-4">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-info-circle me-2"></i>Информация о команде
                    </h5>
                    
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <strong>Лидер команды:</strong>
                                        <span class="ms-2">{{ $team->leader->name }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Текущих участников:</strong>
                                        <span class="badge bg-primary ms-2">{{ $team->approvedMembers()->count() }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <strong>Максимальный размер:</strong>
                                        <span class="badge bg-info ms-2">{{ $team->max_members }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Статус:</strong>
                                        <span class="badge bg-{{ $team->status === 'recruiting' ? 'success' : 'secondary' }} ms-2">
                                            {{ $team->status === 'recruiting' ? 'Набор участников' : ucfirst($team->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            @if($team->description)
                                <div class="mt-3">
                                    <strong>Описание команды:</strong>
                                    <p class="mt-2 text-muted">{{ $team->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Форма заявки -->
                <form method="POST" action="{{ route('teams.apply.store', $team) }}">
                    @csrf
                    
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-edit me-2"></i>Ваша заявка
                        </h5>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Сообщение команде (необязательно)</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="4" 
                                      placeholder="Расскажите о себе, своих навыках и почему хотите присоединиться к этой команде...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Максимум 1000 символов</div>
                        </div>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('teams.show', $team) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Отмена
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Подать заявку
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
