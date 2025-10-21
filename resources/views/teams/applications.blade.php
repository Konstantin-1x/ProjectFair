@extends('layouts.app')

@section('title', 'Заявки команды - ' . $team->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2"></i>Заявки команды "{{ $team->name }}"
                    </h4>
                    <a href="{{ route('teams.show', $team) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Назад к команде
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($applications->count() > 0)
                    <div class="row">
                        @foreach($applications as $application)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            @if($application->user->avatar)
                                                <img src="{{ Storage::url($application->user->avatar) }}" 
                                                     alt="{{ $application->user->name }}" 
                                                     class="rounded-circle me-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $application->user->name }}</h6>
                                                <small class="text-muted">{{ $application->user->email }}</small>
                                            </div>
                                            <a href="{{ route('users.show', $application->user) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-user me-1"></i>Профиль
                                            </a>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <span class="badge bg-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'approved' ? 'success' : 'danger') }}">
                                                @if($application->status === 'pending')
                                                    Ожидает рассмотрения
                                                @elseif($application->status === 'approved')
                                                    Одобрена
                                                @elseif($application->status === 'rejected')
                                                    Отклонена
                                                @else
                                                    Отозвана
                                                @endif
                                            </span>
                                        </div>
                                        
                                        @if($application->message)
                                            <div class="mb-3">
                                                <strong>Сообщение:</strong>
                                                <p class="mt-1 text-muted small">{{ Str::limit($application->message, 100) }}</p>
                                            </div>
                                        @endif
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Подана: {{ $application->applied_at->format('d.m.Y H:i') }}
                                            </small>
                                        </div>
                                        
                                        @if($application->status === 'pending')
                                            <div class="d-flex gap-2">
                                                <form method="POST" action="{{ route('teams.applications.approve', [$team, $application]) }}" 
                                                      class="d-inline" 
                                                      onsubmit="return confirm('Одобрить заявку от {{ $application->user->name }}?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check me-1"></i>Одобрить
                                                    </button>
                                                </form>
                                                
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal{{ $application->id }}">
                                                    <i class="fas fa-times me-1"></i>Отклонить
                                                </button>
                                            </div>
                                        @elseif($application->status === 'rejected' && $application->rejection_reason)
                                            <div class="mt-2">
                                                <small class="text-danger">
                                                    <strong>Причина отклонения:</strong> {{ $application->rejection_reason }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal для отклонения заявки -->
                            @if($application->status === 'pending')
                                <div class="modal fade" id="rejectModal{{ $application->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Отклонение заявки</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('teams.applications.reject', [$team, $application]) }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>Вы уверены, что хотите отклонить заявку от <strong>{{ $application->user->name }}</strong>?</p>
                                                    <div class="mb-3">
                                                        <label for="rejection_reason{{ $application->id }}" class="form-label">Причина отклонения (необязательно)</label>
                                                        <textarea class="form-control" 
                                                                  id="rejection_reason{{ $application->id }}" 
                                                                  name="rejection_reason" 
                                                                  rows="3" 
                                                                  placeholder="Укажите причину отклонения заявки..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                                    <button type="submit" class="btn btn-danger">Отклонить заявку</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    
                    <!-- Пагинация -->
                    <div class="d-flex justify-content-center">
                        {{ $applications->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Заявок пока нет</h5>
                        <p class="text-muted">Когда пользователи будут подавать заявки на вступление в вашу команду, они появятся здесь.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
