@extends('layouts.app')

@section('title', 'Редактирование пользователя: ' . $user->name)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>Редактирование пользователя: {{ $user->name }}
                    </h4>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Назад
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Имя <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="role" class="form-label">Роль <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Пользователь</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Администратор</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="institute" class="form-label">Институт</label>
                                <input type="text" class="form-control @error('institute') is-invalid @enderror" 
                                       id="institute" name="institute" value="{{ old('institute', $user->institute) }}">
                                @error('institute')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="course" class="form-label">Курс</label>
                                <input type="number" class="form-control @error('course') is-invalid @enderror" 
                                       id="course" name="course" value="{{ old('course', $user->course) }}" min="1" max="6">
                                @error('course')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="group" class="form-label">Группа</label>
                                <input type="text" class="form-control @error('group') is-invalid @enderror" 
                                       id="group" name="group" value="{{ old('group', $user->group) }}">
                                @error('group')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">О себе</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                      id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary me-2">Отмена</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
