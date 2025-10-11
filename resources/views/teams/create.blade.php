@extends('layouts.app')

@section('title', 'Создание команды')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Создание новой команды
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('teams.store') }}">
                    @csrf
                    
                    <!-- Основная информация -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Основная информация
                        </h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Название команды <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="Введите название команды" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание команды</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Опишите цели и задачи команды, требуемые навыки, ожидания от участников...">{{ old('description') }}</textarea>
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
                                            <option value="{{ $i }}" {{ old('max_members') == $i ? 'selected' : '' }}>
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
                        </div>
                        
                        <div class="row">
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
                                           id="recruitment_start" name="recruitment_start" value="{{ old('recruitment_start') }}">
                                    @error('recruitment_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="recruitment_end" class="form-label">Дата окончания набора</label>
                                    <input type="date" class="form-control @error('recruitment_end') is-invalid @enderror" 
                                           id="recruitment_end" name="recruitment_end" value="{{ old('recruitment_end') }}">
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
                    
                    <!-- Информация о лидере -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-crown me-2"></i>Информация о лидере
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
                        <a href="{{ route('teams.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Отмена
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Создать команду
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
    // Автоматически устанавливаем дату начала набора на сегодня
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('recruitment_start');
        const endDateInput = document.getElementById('recruitment_end');
        
        if (!startDateInput.value) {
            const today = new Date().toISOString().split('T')[0];
            startDateInput.value = today;
        }
        
        // Устанавливаем минимальную дату для окончания набора
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
        });
    });
</script>
@endpush
