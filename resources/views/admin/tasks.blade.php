@extends('layouts.app')

@section('title', 'Управление задачами')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tasks me-2"></i>Управление задачами</h1>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Создать задачу
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Список задач</h5>
        </div>
        <div class="card-body">
            @if ($tasks->isEmpty())
                <div class="alert alert-info mb-0" role="alert">
                    Задач пока нет.
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
                                <th>Сложность</th>
                                <th>Назначена команде</th>
                                <th>Создана</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td>{{ $task->id }}</td>
                                    <td>
                                        <strong>{{ $task->title }}</strong>
                                        @if($task->type)
                                            <br><small class="text-muted">{{ $task->type }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('users.show', $task->creator) }}" class="text-decoration-none">
                                            {{ $task->creator->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @switch($task->status)
                                            @case('open')
                                                <span class="badge bg-success">Открыта</span>
                                                @break
                                            @case('in_progress')
                                                <span class="badge bg-warning text-dark">В работе</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-primary">Завершена</span>
                                                @break
                                            @case('closed')
                                                <span class="badge bg-secondary">Закрыта</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ $task->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($task->difficulty)
                                            @case('easy')
                                                <span class="badge bg-success">Легкая</span>
                                                @break
                                            @case('medium')
                                                <span class="badge bg-warning text-dark">Средняя</span>
                                                @break
                                            @case('hard')
                                                <span class="badge bg-danger">Сложная</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ $task->difficulty }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($task->assignedTeam)
                                            <a href="{{ route('teams.show', $task->assignedTeam) }}" class="text-decoration-none">
                                                {{ $task->assignedTeam->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">Не назначена</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $task->created_at->format('d.m.Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Вы уверены, что хотите удалить эту задачу?')">
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
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
