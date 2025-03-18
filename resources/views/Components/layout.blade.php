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
    @if (@session('success'))
        <div id="flash" class="p04 text-center bg-green-50 text-green-500 font-bold">
            {{ session('success') }}
        </div>
    @endif
    @if (@session('error'))
    <div id="flash" class="p04 text-center bg-red-50 text-red-500 font-bold">
        {{ session('error') }}
    </div>
    @endif

    <header class="navbar navbar-expand-md d-print-none">
        <div class="container py-2">
          <a class="navbar-brand" href="./index.html">Task Scheduler</a>
          <div class="d-flex align-items-center">
            <div class="dropdown">
              <a href="#" class="nav-link" data-bs-toggle="dropdown">
                <span class="avatar">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="icon avatar-icon"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <circle cx="12" cy="7" r="4" />
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                  </svg>
                </span>
              </a>
              <div class="dropdown-menu">
                <span class="dropdown-header">Hi, Angel.Valkov</span>
                <a href="./settings.html" class="dropdown-item">Settings</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">Logout</a>
              </div>
            </div>
  
            <!-- Dark Mode Toggle -->
            <span class="avatar ms-3" id="theme-toggle" style="cursor: pointer">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.75"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="icon icon-tabler icons-tabler-outline icon-tabler-moon"
              >
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path
                  d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z"
                />
              </svg>
            </span>
          </div>
        </div>
      </header>

    <main class="container">
        {{ $slot }}
    </main>

    <script>

    </script>
    @vite(['resources/js/app.js'])
</body>
</html>