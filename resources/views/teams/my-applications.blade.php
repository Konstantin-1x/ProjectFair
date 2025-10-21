@extends('layouts.app')

@section('title', 'Мои заявки')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>Мои заявки на вступление в команды
                </h4>
            </div>
            <div class="card-body">
                @if($applications->count() > 0)
                    <div class="row">
                        @foreach($applications as $application)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="card-title mb-0">{{ $application->team->name }}</h6>
                                            <span class="badge bg-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'approved' ? 'success' : 'danger') }}">
                                                @if($application->status === 'pending')
                                                    Ожидает
                                                @elseif($application->status === 'approved')
                                                    Одобрена
                                                @elseif($application->status === 'rejected')
                                                    Отклонена
                                                @else
                                                    Отозвана
                                                @endif
                                            </span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                Лидер: {{ $application->team->leader->name }}
                                            </small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-users me-1"></i>
                                                Участников: {{ $application->team->approvedMembers()->count() }}/{{ $application->team->max_members }}
                                            </small>
                                        </div>
                                        
                                        @if($application->message)
                                            <div class="mb-3">
                                                <strong>Ваше сообщение:</strong>
                                                <p class="mt-1 text-muted small">{{ Str::limit($application->message, 100) }}</p>
                                            </div>
                                        @endif
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Подана: {{ $application->applied_at->format('d.m.Y H:i') }}
                                            </small>
                                        </div>
                                        
                                        @if($application->status === 'rejected' && $application->rejection_reason)
                                            <div class="mb-3">
                                                <small class="text-danger">
                                                    <strong>Причина отклонения:</strong> {{ $application->rejection_reason }}
                                                </small>
                                            </div>
                                        @endif
                                        
                                        @if($application->status === 'approved')
                                            <div class="mb-3">
                                                <small class="text-success">
                                                    <i class="fas fa-check me-1"></i>
                                                    Заявка одобрена! Вы теперь участник команды.
                                                </small>
                                            </div>
                                        @endif
                                        
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('teams.show', $application->team) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Посмотреть команду
                                            </a>
                                            
                                            @if($application->status === 'pending')
                                                <form method="POST" action="{{ route('teams.applications.withdraw', [$application->team, $application]) }}" 
                                                      class="d-inline" 
                                                      onsubmit="return confirm('Отозвать заявку в команду {{ $application->team->name }}?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-times me-1"></i>Отозвать
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Пагинация -->
                    <div class="d-flex justify-content-center">
                        {{ $applications->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">У вас нет заявок</h5>
                        <p class="text-muted">Вы еще не подавали заявки на вступление в команды.</p>
                        <a href="{{ route('teams.index') }}" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Найти команды
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
