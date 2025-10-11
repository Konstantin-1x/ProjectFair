@extends('layouts.app')

@section('title', 'Тест создания проекта')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Тест создания проекта</h4>
                </div>
                <div class="card-body">
                    <p>Это тестовое представление для проверки работы маршрута.</p>
                    <p>Tags count: {{ $tags->count() ?? 'N/A' }}</p>
                    <p>Technologies count: {{ $technologies->count() ?? 'N/A' }}</p>
                    <p>Teams count: {{ $teams->count() ?? 'N/A' }}</p>
                    
                    <form method="POST" action="{{ route('projects.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Название проекта</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Создать проект</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
