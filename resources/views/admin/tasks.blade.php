@extends('layouts.app')

@section('title', __('Manage tasks'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tasks me-2"></i>{{ __('Manage tasks') }}</h1>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>{{ __('Create task') }}
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Tasks list') }}</h5>
        </div>
        <div class="card-body">
            @if ($tasks->isEmpty())
                <div class="alert alert-info mb-0" role="alert">
                    {{ __('No tasks yet.') }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Creator') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Difficulty') }}</th>
                                <th>{{ __('Assigned to team') }}</th>
                                <th>{{ __('Created at') }}</th>
                                <th>{{ __('Actions') }}</th>
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
                                                <span class="badge bg-success">{{ __('Open') }}</span>
                                                @break
                                            @case('in_progress')
                                                <span class="badge bg-warning text-dark">{{ __('In progress') }}</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-primary">{{ __('Completed') }}</span>
                                                @break
                                            @case('closed')
                                                <span class="badge bg-secondary">{{ __('Closed') }}</span>
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
                                        @if($task->assignedUser)
                                            <a href="{{ route('users.show', $task->assignedUser) }}" class="text-decoration-none">
                                                {{ $task->assignedUser->name }}
                                            </a>
                                        @elseif($task->project)
                                            <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                                                {{ $task->project->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('Not assigned') }}</span>
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
                                                        onclick="return confirm('{{ __('Are you sure you want to delete this task?') }}')">
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
                    {{ $tasks->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
