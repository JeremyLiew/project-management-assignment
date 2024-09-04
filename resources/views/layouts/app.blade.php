<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('projects.index') }}">{{ __('Projects') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('tasks.index') }}">{{ __('Tasks') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('budgets.index') }}">{{ __('Budgets') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logs.index') }}">{{ __('Logs') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('about-us') }}">{{ __('About Us') }}</a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
          <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ (request()->is('login')) ? 'active' : '' }}" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                @else    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();"
                            >{{ __('Logout') }}</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                            </form>
                        </li>
                        </ul>
                    </li>
                @endguest
            </ul>
          </div>
        </div>
    </nav>    

       
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
</body>
</html>
