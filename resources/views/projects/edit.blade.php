@extends('layouts.app')

@section('title', 'Редактирование проекта: ' . $project->title)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Редактирование проекта: {{ $project->title }}
                    </h4>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Назад
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projects.update', $project) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Основная информация -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Основная информация
                            </h5>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Название проекта <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $project->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="short_description" class="form-label">Краткое описание</label>
                                <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                          id="short_description" name="short_description" rows="2" maxlength="500">{{ old('short_description', $project->short_description) }}</textarea>
                                <div class="form-text">Максимум 500 символов</div>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Полное описание <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="6" required>{{ old('description', $project->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Детали проекта -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-cog me-2"></i>Детали проекта
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Тип проекта</label>
                                        <input type="text" class="form-control @error('type') is-invalid @enderror" 
                                               id="type" name="type" value="{{ old('type', $project->type) }}">
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Статус проекта <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Активный</option>
                                            <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Завершен</option>
                                            <option value="archived" {{ old('status', $project->status) == 'archived' ? 'selected' : '' }}>Архивный</option>
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
                                        <label for="institute" class="form-label">Институт</label>
                                        <input type="text" class="form-control @error('institute') is-invalid @enderror" 
                                               id="institute" name="institute" value="{{ old('institute', $project->institute) }}">
                                        @error('institute')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="course" class="form-label">Курс</label>
                                        <input type="number" class="form-control @error('course') is-invalid @enderror" 
                                               id="course" name="course" value="{{ old('course', $project->course) }}" min="1" max="6">
                                        @error('course')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Медиа и ссылки -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-image me-2"></i>Медиа и ссылки
                            </h5>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Изображение проекта</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                @if($project->image)
                                    <div class="mt-2">
                                        <small class="text-muted">Текущее изображение:</small><br>
                                        <img src="{{ Storage::url($project->image) }}" alt="{{ $project->title }}" 
                                             class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                @endif
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="demo_url" class="form-label">Ссылка на демо</label>
                                        <input type="url" class="form-control @error('demo_url') is-invalid @enderror" 
                                               id="demo_url" name="demo_url" value="{{ old('demo_url', $project->demo_url) }}">
                                        @error('demo_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="github_url" class="form-label">Ссылка на GitHub</label>
                                        <input type="url" class="form-control @error('github_url') is-invalid @enderror" 
                                               id="github_url" name="github_url" value="{{ old('github_url', $project->github_url) }}">
                                        @error('github_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Команда -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-users me-2"></i>Команда
                            </h5>
                            
                            <div class="mb-3">
                                <label for="team_id" class="form-label">Назначить команде</label>
                                <select class="form-select @error('team_id') is-invalid @enderror" id="team_id" name="team_id">
                                    <option value="">Не назначать</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('team_id', $project->team_id) == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }} ({{ $team->members->count() }}/{{ $team->max_members }} участников)
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Теги и технологии -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-tags me-2"></i>Теги и технологии
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Теги</label>
                                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($tags as $tag)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="tags[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}"
                                                           {{ in_array($tag->id, old('tags', $project->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="tag_{{ $tag->id }}">
                                                        {{ $tag->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Технологии</label>
                                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($technologies as $technology)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="technologies[]" value="{{ $technology->id }}" id="tech_{{ $technology->id }}"
                                                           {{ in_array($technology->id, old('technologies', $project->technologies->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="tech_{{ $technology->id }}">
                                                        {{ $technology->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Даты -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-calendar me-2"></i>Временные рамки
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="started_at" class="form-label">Дата начала</label>
                                        <input type="date" class="form-control @error('started_at') is-invalid @enderror" 
                                               id="started_at" name="started_at" 
                                               value="{{ old('started_at', $project->started_at ? $project->started_at->format('Y-m-d') : '') }}">
                                        @error('started_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="completed_at" class="form-label">Дата завершения</label>
                                        <input type="date" class="form-control @error('completed_at') is-invalid @enderror" 
                                               id="completed_at" name="completed_at" 
                                               value="{{ old('completed_at', $project->completed_at ? $project->completed_at->format('Y-m-d') : '') }}">
                                        @error('completed_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary me-2">Отмена</a>
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
