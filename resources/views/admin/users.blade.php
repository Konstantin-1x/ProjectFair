@extends('layouts.app')

@section('title', __('Manage users'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-users me-2"></i>{{ __('Manage users') }}</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>{{ __('Back to panel') }}
    </a>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ __('Users list') }} ({{ $users->total() }})</h5>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Institute') }}</th>
                            <th>{{ __('Course') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Projects') }}</th>
                            <th>{{ __('Teams') }}</th>
                            <th>{{ __('Registration date') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 40px; height: 40px;">
                                        {{ $user->initials() }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $user->name }}</h6>
                                        @if($user->group)
                                            <small class="text-muted">{{ $user->group }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->institute ?? '-' }}</td>
                            <td>{{ $user->course ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                                    {{ $user->role === 'admin' ? __('Administrator') : __('User') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $user->projects_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $user->teams_count }}</span>
                            </td>
                            <td>{{ $user->created_at->format('d.m.Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" data-bs-target="#userModal{{ $user->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $user->role === 'admin' ? 'danger' : 'success' }}"
                                                    onclick="return confirm('{{ $user->role === 'admin' ? __('Revoke admin rights?') : __('Make admin?') }}')">
                                                <i class="fas fa-{{ $user->role === 'admin' ? 'user-minus' : 'user-plus' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h3>{{ __('No users found') }}</h3>
                <p class="text-muted">{{ __('There are no users yet.') }}</p>
            </div>
        @endif
    </div>
</div>

<!-- User Modals -->
@foreach($users as $user)
<div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1" aria-labelledby="userModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel{{ $user->id }}">{{ __('User info') }}: {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>{{ __('Main info') }}</h6>
                        <p><strong>{{ __('Name') }}:</strong> {{ $user->name }}</p>
                        <p><strong>{{ __('Email') }}:</strong> {{ $user->email }}</p>
                        <p><strong>{{ __('Role') }}:</strong> 
                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                                {{ $user->role === 'admin' ? __('Administrator') : __('User') }}
                            </span>
                        </p>
                        <p><strong>{{ __('Registration date') }}:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>{{ __('Academic info') }}</h6>
                        <p><strong>{{ __('Institute') }}:</strong> {{ $user->institute ?? __('Not specified') }}</p>
                        <p><strong>{{ __('Course') }}:</strong> {{ $user->course ?? __('Not specified') }}</p>
                        <p><strong>{{ __('Group') }}:</strong> {{ $user->group ?? __('Not specified') }}</p>
                    </div>
                </div>
                @if($user->bio)
                <div class="mt-3">
                    <h6>{{ __('About') }}</h6>
                    <p>{{ $user->bio }}</p>
                </div>
                @endif
                <div class="mt-3">
                    <h6>{{ __('Statistics') }}</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('Projects') }}:</strong> {{ $user->projects_count }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('Teams') }}:</strong> {{ $user->teams_count }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Предотвращаем мигание модальных окон
    document.querySelectorAll('.modal').forEach(function(modal) {
        // Убираем анимацию при загрузке
        modal.style.transition = 'none';
        
        // Восстанавливаем анимацию после загрузки
        setTimeout(function() {
            modal.style.transition = '';
        }, 100);
        
        // Очищаем модальные окна при закрытии
        modal.addEventListener('hidden.bs.modal', function() {
            // Убираем классы, которые могут остаться
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Убираем backdrop если он остался
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        });
        
        // Предотвращаем множественное открытие
        modal.addEventListener('show.bs.modal', function() {
            // Закрываем все другие модальные окна
            document.querySelectorAll('.modal.show').forEach(function(otherModal) {
                if (otherModal !== modal) {
                    const bsModal = bootstrap.Modal.getInstance(otherModal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                }
            });
        });
    });
});
</script>
@endpush
