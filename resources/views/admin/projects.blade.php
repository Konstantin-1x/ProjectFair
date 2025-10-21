@extends('layouts.app')

@section('title', __('Manage projects'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-project-diagram me-2"></i>{{ __('Manage projects') }}</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>{{ __('Create project') }}
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Projects list') }}</h5>
        </div>
        <div class="card-body">
            @if ($projects->isEmpty())
                <div class="alert alert-info mb-0" role="alert">
                    {{ __('No projects yet.') }}
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
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Institute') }}</th>
                                <th>{{ __('Team') }}</th>
                                <th>{{ __('Created at') }}</th>
                                <th>{{ __('Actions') }}</th>
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
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-primary">{{ __('Completed') }}</span>
                                                @break
                                            @case('archived')
                                                <span class="badge bg-secondary">{{ __('Archived') }}</span>
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
                                            <span class="text-muted">{{ __('Not assigned') }}</span>
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
                                                        onclick="return confirm('{{ __('Are you sure you want to delete this project?') }}')">
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
                    {{ $projects->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
