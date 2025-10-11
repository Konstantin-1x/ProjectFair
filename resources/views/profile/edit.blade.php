@extends('layouts.app')

@section('title', 'Редактирование профиля')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Редактирование профиля
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <div class="mb-3">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 150px; height: 150px;">
                                        <i class="fas fa-user fa-4x"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Аватар</label>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Максимальный размер: 2MB. Поддерживаемые форматы: JPEG, PNG, JPG, GIF</div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Имя <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="institute" class="form-label">Институт</label>
                                        <select class="form-select @error('institute') is-invalid @enderror" id="institute" name="institute">
                                            <option value="">Выберите институт</option>
                                            <option value="ИИТУТ" {{ old('institute', $user->institute) === 'ИИТУТ' ? 'selected' : '' }}>ИИТУТ</option>
                                            <option value="ИМО" {{ old('institute', $user->institute) === 'ИМО' ? 'selected' : '' }}>ИМО</option>
                                            <option value="ИППИ" {{ old('institute', $user->institute) === 'ИППИ' ? 'selected' : '' }}>ИППИ</option>
                                            <option value="ИЭУП" {{ old('institute', $user->institute) === 'ИЭУП' ? 'selected' : '' }}>ИЭУП</option>
                                            <option value="ИФКС" {{ old('institute', $user->institute) === 'ИФКС' ? 'selected' : '' }}>ИФКС</option>
                                        </select>
                                        @error('institute')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="course" class="form-label">Курс</label>
                                        <select class="form-select @error('course') is-invalid @enderror" id="course" name="course">
                                            <option value="">Выберите курс</option>
                                            @for($i = 1; $i <= 6; $i++)
                                                <option value="{{ $i }}" {{ old('course', $user->course) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('course')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="group" class="form-label">Группа</label>
                                        <input type="text" class="form-control @error('group') is-invalid @enderror" id="group" name="group" value="{{ old('group', $user->group) }}" placeholder="Например: ИС-21-1">
                                        @error('group')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bio" class="form-label">О себе</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4" placeholder="Расскажите о себе, своих интересах и навыках...">{{ old('bio', $user->bio) }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Максимум 1000 символов</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Назад
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
