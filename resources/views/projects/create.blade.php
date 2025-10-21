@extends('layouts.app')

@section('title', 'Создание проекта')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Создание нового проекта
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Основная информация -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Основная информация
                        </h5>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Название проекта <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="Введите название проекта" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="short_description" class="form-label">Краткое описание</label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                      id="short_description" name="short_description" rows="3" 
                                      placeholder="Краткое описание проекта (до 500 символов)">{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Максимум 500 символов</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Подробное описание <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="6" 
                                      placeholder="Подробное описание проекта, цели, задачи, результаты..." required>{{ old('description') }}</textarea>
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
                                    <label for="type" class="form-label">Тип проекта</label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                                        <option value="">Выберите тип проекта</option>
                                        <option value="Учебный" {{ old('type') === 'Учебный' ? 'selected' : '' }}>Учебный</option>
                                        <option value="Исследовательский" {{ old('type') === 'Исследовательский' ? 'selected' : '' }}>Исследовательский</option>
                                        <option value="Коммерческий" {{ old('type') === 'Коммерческий' ? 'selected' : '' }}>Коммерческий</option>
                                        <option value="Социальный" {{ old('type') === 'Социальный' ? 'selected' : '' }}>Социальный</option>
                                        <option value="Хакатон" {{ old('type') === 'Хакатон' ? 'selected' : '' }}>Хакатон</option>
                                        <option value="Дипломный" {{ old('type') === 'Дипломный' ? 'selected' : '' }}>Дипломный</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>
                        
                        <!-- Выбор команды -->
                        <div class="mb-3">
                            <label for="team_id" class="form-label">Команда для проекта <span class="text-danger">*</span></label>
                            <select name="team_id" id="team_id" class="form-select @error('team_id') is-invalid @enderror" required>
                                <option value="">-- Выберите команду --</option>
                                @foreach($userTeams as $team)
                                    <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }} ({{ $team->members->count() }} участников, статус: {{ $team->status === 'active' ? 'Активная' : 'Набор' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('team_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Выберите команду, для которой создается проект</div>
                        </div>
                    </div>
                    
                    <!-- Сроки выполнения -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-calendar-alt me-2"></i>Сроки выполнения
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="project_deadline" class="form-label">Срок завершения проекта</label>
                                    <input type="date" class="form-control @error('project_deadline') is-invalid @enderror" 
                                           id="project_deadline" name="project_deadline" value="{{ old('project_deadline') }}">
                                    @error('project_deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="team_join_deadline" class="form-label">Срок вступления команд</label>
                                    <input type="date" class="form-control @error('team_join_deadline') is-invalid @enderror" 
                                           id="team_join_deadline" name="team_join_deadline" value="{{ old('team_join_deadline') }}">
                                    @error('team_join_deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="individual_join_deadline" class="form-label">Срок вступления одиночных участников</label>
                                    <input type="date" class="form-control @error('individual_join_deadline') is-invalid @enderror" 
                                           id="individual_join_deadline" name="individual_join_deadline" value="{{ old('individual_join_deadline') }}">
                                    @error('individual_join_deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="allows_individual_join" name="allows_individual_join" value="1" {{ old('allows_individual_join') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allows_individual_join">
                                            Разрешить вступление одиночных участников
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deadline_description" class="form-label">Описание сроков</label>
                            <textarea class="form-control @error('deadline_description') is-invalid @enderror" 
                                      id="deadline_description" name="deadline_description" rows="3" 
                                      placeholder="Дополнительная информация о сроках и требованиях...">{{ old('deadline_description') }}</textarea>
                            @error('deadline_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Технологии и теги -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-code me-2"></i>Технологии и теги
                        </h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Технологии</label>
                            <div class="row">
                                @foreach($technologies as $technology)
                                <div class="col-md-4 col-lg-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="technologies[]" value="{{ $technology->id }}" 
                                               id="tech_{{ $technology->id }}"
                                               {{ in_array($technology->id, old('technologies', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tech_{{ $technology->id }}">
                                            {{ $technology->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('technologies')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Теги</label>
                            <div class="row">
                                @foreach($tags as $tag)
                                <div class="col-md-4 col-lg-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="tags[]" value="{{ $tag->id }}" 
                                               id="tag_{{ $tag->id }}"
                                               {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tag_{{ $tag->id }}">
                                            {{ $tag->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('tags')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_tags" class="form-label">Добавить свои теги</label>
                            <input type="text" class="form-control @error('new_tags') is-invalid @enderror" 
                                   id="new_tags" name="new_tags" value="{{ old('new_tags') }}" 
                                   placeholder="Например: Дизайн, Маркетинг, Исследования (разделяйте запятыми)">
                            @error('new_tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Если не нашли подходящий тег, добавьте свой. Разделяйте несколько тегов запятыми.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_technologies" class="form-label">Добавить свои технологии</label>
                            <input type="text" class="form-control @error('new_technologies') is-invalid @enderror" 
                                   id="new_technologies" name="new_technologies" value="{{ old('new_technologies') }}" 
                                   placeholder="Например: React, Node.js, Python (разделяйте запятыми)">
                            @error('new_technologies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Если не нашли подходящую технологию, добавьте свою. Разделяйте несколько технологий запятыми.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Медиа и ссылки -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-image me-2"></i>Медиа и ссылки
                        </h5>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Главное изображение</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Рекомендуемый размер: 1200x630px. Максимальный размер: 2MB</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="demo_url" class="form-label">Ссылка на демо</label>
                                    <input type="url" class="form-control @error('demo_url') is-invalid @enderror" 
                                           id="demo_url" name="demo_url" value="{{ old('demo_url') }}" 
                                           placeholder="https://example.com/demo">
                                    @error('demo_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="github_url" class="form-label">Ссылка на GitHub</label>
                                    <input type="url" class="form-control @error('github_url') is-invalid @enderror" 
                                           id="github_url" name="github_url" value="{{ old('github_url') }}" 
                                           placeholder="https://github.com/username/repository">
                                    @error('github_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Отмена
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Создать проект
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
    // Предварительный просмотр изображения
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Создаем элемент для предварительного просмотра
                let preview = document.getElementById('image-preview');
                if (!preview) {
                    preview = document.createElement('img');
                    preview.id = 'image-preview';
                    preview.className = 'img-thumbnail mt-2';
                    preview.style.maxWidth = '200px';
                    document.getElementById('image').parentNode.appendChild(preview);
                }
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
