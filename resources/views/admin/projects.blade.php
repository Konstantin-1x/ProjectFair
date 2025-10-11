@extends('layouts.app')

@section('title', 'Управление проектами')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-project-diagram me-2"></i>Управление проектами</h1>
        <a href="{{ route('projects.new.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Создать проект
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Список проектов</h5>
        </div>
        <div class="card-body">
            @if ($projects->isEmpty())
                <div class="alert alert-info mb-0" role="alert">
                    Проектов пока нет.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Создатель</th>
                                <th>Статус</th>
                                <th>Тип</th>
                                <th>Институт</th>
                                <th>Команда</th>
                                <th>Создан</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $project)
                                <tr>
                                    <td>{{ $project->id }}</td>
                                    <td>
                                        <strong>{{ $project->title }}</strong>
                                        @if($project->short_description)
                                            <br><small class="text-muted">{{ Str::limit($project->short_description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('users.show', $project->creator) }}" class="text-decoration-none">
                                            {{ $project->creator->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @switch($project->status)
                                            @case('active')
                                                <span class="badge bg-success">Активный</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-primary">Завершен</span>
                                                @break
                                            @case('archived')
                                                <span class="badge bg-secondary">Архивный</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ $project->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $project->type ?? '-' }}</td>
                                    <td>{{ $project->institute ?? '-' }}</td>
                                    <td>
                                        @if($project->team)
                                            <a href="{{ route('teams.show', $project->team) }}" class="text-decoration-none">
                                                {{ $project->team->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">Не назначена</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $project->created_at->format('d.m.Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Вы уверены, что хотите удалить этот проект?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
