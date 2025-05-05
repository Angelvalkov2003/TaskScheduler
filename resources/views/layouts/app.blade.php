<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Task Scheduler</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" />

    @livewireStyles
</head>
<body>
    @if (session('success'))
        <div id="flash" class="p-4 text-center bg-green-50 text-green-500 font-bold">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div id="flash" class="p-4 text-center bg-red-50 text-red-500 font-bold">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <header class="navbar navbar-expand-md d-print-none shadow-sm">
        <div class="container py-2 d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="{{ route('tasks.index') }}">Task Scheduler</a>

            <div class="d-flex align-items-center ms-auto">
                @guest
                <ul class="navbar-nav d-flex flex-row">
                    <li class="nav-item me-4">
                        <a href="{{ route('login') }}" class="nav-link text-gray-900 hover:text-blue-700">Login</a>
                    </li>
                    <li class="nav-item me-4">
                        <a href="{{ route('register') }}" class="nav-link text-gray-900 hover:text-blue-700">Register</a>
                    </li>
                </ul>
                @endguest

                @auth
            <div class="dropdown me-4">
                <a href="#" class="nav-link" data-bs-toggle="dropdown">
                    <span class="avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon avatar-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                        </svg>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="{{ route('profile') }}" class="dropdown-item">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </div>
            </div>
            @endauth

                <span class="avatar" id="theme-toggle" style="cursor: pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                         stroke-linejoin="round" class="icon icon-tabler icon-tabler-moon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                    </svg>
                </span>
            </div>
        </div>
    </header>

    <main class="container py-4">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
