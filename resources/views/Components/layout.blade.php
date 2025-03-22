<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Scheduler</title>

    @vite('resources/css/app.css')
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css"
    />
</head>
<body>
  @if (session('success'))
      <div id="flash" class="p-4 text-center bg-green-50 text-green-500 font-bold">
          {{ session('success') }}
      </div>
  @endif
  @if (session('error'))
      <div id="flash" class="p-4 text-center bg-red-50 text-red-500 font-bold">
          {{ session('error') }}
      </div>
  @endif

  <header class="navbar navbar-expand-md d-print-none">
    <div class="container py-2 d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="{{ route('tasks.index') }}">Task Scheduler</a>

        <!-- Дясно подравняване на бутоните -->
        <div class="d-flex align-items-center ms-auto">
            @guest
            <ul class="navbar-nav d-flex flex-row">
                <li class="nav-item me-4">
                    <a href="{{ route('show.login') }}" class="nav-link text-gray-900 hover:text-blue-700">Login</a>
                </li>
                <li class="nav-item me-4">
                    <a href="{{ route('show.register') }}" class="nav-link text-gray-900 hover:text-blue-700">Register</a>
                </li>
            </ul>
            @endguest
            @auth
            <div class="dropdown me-4">
                <a href="#" class="nav-link" data-bs-toggle="dropdown">
                    <span class="avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon avatar-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <circle cx="12" cy="7" r="4" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <span class="dropdown-header">Hi, {{ Auth::user()->email }}</span>
                    <a href="{{ route('profile') }}" class="dropdown-item">Settings</a>
                    <div class="dropdown-divider"></div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                </div>
            </div>
            @endauth

            <!-- Dark Mode Toggle -->
            <span class="avatar" id="theme-toggle" style="cursor: pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-moon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                </svg>
            </span>
        </div>
    </div>
</header>


</body>

  

    <main class="container">
        {{ $slot }}
    </main>

    @vite(['resources/js/app.js'])
</body>
</html>