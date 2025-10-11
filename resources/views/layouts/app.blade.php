<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Витрина студенческих проектов СевГУ')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Витрина проектов СевГУ
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Главная</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('projects.index') }}">Проекты</a>
                        </li>
                        @auth
                            @if(auth()->user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-cog me-1"></i>Админ-панель
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <!-- User Info -->
                                    <div class="dropdown-header">
                                        <div class="d-flex align-items-center">
                                            @if(Auth::user()->avatar)
                                                <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ Auth::user()->name }}</div>
                                                <small class="text-muted">{{ Auth::user()->email }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="dropdown-divider"></div>
                                    
                                    <!-- Main Actions -->
                                    <a class="dropdown-item" href="{{ route('home') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Личный кабинет
                                    </a>
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="fas fa-user me-2"></i>Мой профиль
                                    </a>
                                    <a class="dropdown-item" href="{{ route('profile.projects') }}">
                                        <i class="fas fa-project-diagram me-2"></i>Мои проекты
                                    </a>
                                    <a class="dropdown-item" href="{{ route('profile.teams') }}">
                                        <i class="fas fa-users me-2"></i>Мои команды
                                    </a>
                                    
                                    <div class="dropdown-divider"></div>
                                    
                                    <!-- Create Actions -->
                                    <a class="dropdown-item" href="{{ route('projects.new.create') }}">
                                        <i class="fas fa-plus me-2"></i>Создать проект
                                    </a>
                                    <a class="dropdown-item" href="{{ route('teams.create') }}">
                                        <i class="fas fa-users me-2"></i>Создать команду
                                    </a>
                                    <a class="dropdown-item" href="{{ route('tasks.create') }}">
                                        <i class="fas fa-tasks me-2"></i>Создать задачу
                                    </a>
                                    
                                    @if(Auth::user()->isAdmin())
                                    <div class="dropdown-divider"></div>
                                    <div class="dropdown-header">
                                        <i class="fas fa-cog me-2"></i>Администрирование
                                    </div>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Админ-панель
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.analytics') }}">
                                        <i class="fas fa-chart-bar me-2"></i>Аналитика
                                    </a>
                                    @endif
                                    
                                    <div class="dropdown-divider"></div>
                                    
                                    <!-- Settings -->
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-cog me-2"></i>Настройки
                                    </a>
                                    
                                    <!-- Logout -->
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Выйти
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
