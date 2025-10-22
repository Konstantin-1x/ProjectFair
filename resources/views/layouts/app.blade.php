<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('SevSU Project Showcase'))</title>

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
                    {{ __('SevSU Project Showcase') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">{{ __('Home') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('projects.index') }}">{{ __('Projects') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('teams.index') }}">{{ __('Teams') }}</a>
                        </li>
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teams.my-applications') }}">
                                    <i class="fas fa-file-alt me-1"></i>{{ __('My applications') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('my-team-applications') }}">
                                    <i class="fas fa-users me-1"></i>{{ __('Team applications') }}
                                </a>
                            </li>
                            @if(auth()->user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>{{ __('Teacher Panel') }}
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Language Switcher -->
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-globe me-1"></i>{{ strtoupper(app()->getLocale()) }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('locale.switch', ['lang' => 'ru']) }}">Русский</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('locale.switch', ['lang' => 'en']) }}">English</a>
                                </li>
                            </ul>
                        </li>
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
                                        <i class="fas fa-tachometer-alt me-2"></i>{{ __('Personal cabinet') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="fas fa-user me-2"></i>{{ __('My profile') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('profile.projects') }}">
                                        <i class="fas fa-project-diagram me-2"></i>{{ __('My projects') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('profile.teams') }}">
                                        <i class="fas fa-users me-2"></i>{{ __('My teams') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('teams.my-applications') }}">
                                        <i class="fas fa-file-alt me-2"></i>{{ __('My applications') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('my-team-applications') }}">
                                        <i class="fas fa-users me-2"></i>{{ __('Team applications') }}
                                    </a>
                                    
                                    <div class="dropdown-divider"></div>
                                    
                                    <!-- Create Actions -->
                                    <a class="dropdown-item" href="{{ route('projects.create') }}">
                                        <i class="fas fa-plus me-2"></i>{{ __('Create project') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('teams.create') }}">
                                        <i class="fas fa-users me-2"></i>{{ __('Create team') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('tasks.create') }}">
                                        <i class="fas fa-tasks me-2"></i>{{ __('Create task') }}
                                    </a>
                                    
                                    @if(Auth::user()->isAdmin())
                                    <div class="dropdown-divider"></div>
                                    <div class="dropdown-header">
                                        <i class="fas fa-chalkboard-teacher me-2"></i>{{ __('Teacher') }}
                                    </div>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>{{ __('Teacher Panel') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.analytics') }}">
                                        <i class="fas fa-chart-bar me-2"></i>{{ __('Analytics') }}
                                    </a>
                                    @endif
                                    
                                    <div class="dropdown-divider"></div>
                                    
                                    <!-- Settings -->
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-cog me-2"></i>{{ __('Settings') }}
                                    </a>
                                    
                                    <!-- Logout -->
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
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
    
    <!-- Fix for modal backdrop flickering -->
    <style>
        /* Fix modal backdrop flickering */
        .modal-backdrop {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 1040 !important;
            width: 100vw !important;
            height: 100vh !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
            opacity: 0.5 !important;
            transition: none !important;
        }
        
        .modal-backdrop.fade {
            opacity: 0.5 !important;
            transition: none !important;
        }
        
        .modal-backdrop.show {
            opacity: 0.5 !important;
            transition: none !important;
        }
        
        /* Prevent hover effects on backdrop */
        .modal-backdrop:hover {
            opacity: 0.5 !important;
        }
        
        /* Ensure modal is above backdrop */
        .modal {
            z-index: 1055 !important;
        }
        
        /* Custom pagination styles */
        .pagination .page-link {
            border: 1px solid #dee2e6;
            color: #6c757d;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            transition: all 0.15s ease-in-out;
        }

        .pagination .page-link:hover {
            color: #495057;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
        }

        .pagination .page-link i {
            font-size: 0.75rem;
        }
    </style>
    
    <!-- Simple modal flickering fix -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple fix for modal backdrop flickering
            document.addEventListener('show.bs.modal', function (event) {
                // Remove any existing backdrops to prevent duplicates
                const existingBackdrops = document.querySelectorAll('.modal-backdrop');
                existingBackdrops.forEach(backdrop => backdrop.remove());
            });
            
            document.addEventListener('hidden.bs.modal', function (event) {
                // Clean up any remaining backdrops
                const existingBackdrops = document.querySelectorAll('.modal-backdrop');
                existingBackdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
