@extends('layouts.app')

@section('title', 'Управление командами')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users me-2"></i>Управление командами</h1>
        <a href="{{ route('teams.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Создать команду
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Список команд</h5>
        </div>
        <div class="card-body">
            @if ($teams->isEmpty())
                <div class="alert alert-info mb-0" role="alert">
                    Команд пока нет.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Лидер</th>
                                <th>Статус</th>
                                <th>Участников</th>
                                <th>Институт</th>
                                <th>Курс</th>
                                <th>Создана</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teams as $team)
                                <tr>
                                    <td>{{ $team->id }}</td>
                                    <td>
                                        <strong>{{ $team->name }}</strong>
                                        @if($team->description)
                                            <br><small class="text-muted">{{ Str::limit($team->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('users.show', $team->leader) }}" class="text-decoration-none">
                                            {{ $team->leader->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @switch($team->status)
                                            @case('recruiting')
                                                <span class="badge bg-success">Набирает</span>
                                                @break
                                            @case('active')
                                                <span class="badge bg-primary">Активна</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-info">Завершена</span>
                                                @break
                                            @case('disbanded')
                                                <span class="badge bg-secondary">Расформирована</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ $team->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $team->members_count }} / {{ $team->max_members }}</span>
                                    </td>
                                    <td>{{ $team->institute ?? '-' }}</td>
                                    <td>{{ $team->course ?? '-' }}</td>
                                    <td>
                                        <small class="text-muted">{{ $team->created_at->format('d.m.Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('teams.show', $team) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('teams.destroy', $team) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Вы уверены, что хотите удалить эту команду?')">
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
                    {{ $teams->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
