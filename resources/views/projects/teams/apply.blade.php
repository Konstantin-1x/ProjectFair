@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Подать заявку командой на проект
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Проект: {{ $project->title }}</h5>
                        <p class="text-muted">{{ $project->description }}</p>
                    </div>

                    <form action="{{ route('projects.team-applications.store', $project) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="team_id" class="form-label">Выберите команду</label>
                            <select name="team_id" id="team_id" class="form-select @error('team_id') is-invalid @enderror" required>
                                <option value="">-- Выберите команду --</option>
                                @foreach($availableTeams as $team)
                                    <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }} ({{ $team->members->count() }} участников)
                                    </option>
                                @endforeach
                            </select>
                            @error('team_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Сообщение (необязательно)</label>
                            <textarea name="message" id="message" class="form-control @error('message') is-invalid @enderror" 
                                rows="4" placeholder="Расскажите, почему ваша команда хочет участвовать в этом проекте...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Назад к проекту
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-1"></i>
                                Подать заявку
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
