@extends('layouts.app')

@section('title', __('Manage teams'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users me-2"></i>{{ __('Manage teams') }}</h1>
        <a href="{{ route('teams.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>{{ __('Create team') }}
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Teams list') }}</h5>
        </div>
        <div class="card-body">
            @if ($teams->isEmpty())
                <div class="alert alert-info mb-0" role="alert">
                    {{ __('No teams yet.') }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Leader') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Members') }}</th>
                                <th>{{ __('Institute') }}</th>
                                <th>{{ __('Course') }}</th>
                                <th>{{ __('Created at') }}</th>
                                <th>{{ __('Actions') }}</th>
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
                                                <span class="badge bg-success">{{ __('Recruiting') }}</span>
                                                @break
                                            @case('active')
                                                <span class="badge bg-primary">{{ __('Active') }}</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-info">{{ __('Completed') }}</span>
                                                @break
                                            @case('disbanded')
                                                <span class="badge bg-secondary">{{ __('Disbanded') }}</span>
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
                                                        onclick="return confirm('{{ __('Are you sure you want to delete this team?') }}')">
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
                    {{ $teams->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
