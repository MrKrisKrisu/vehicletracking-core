<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

        <title>
            @hasSection('title')
                @yield('title') -
            @endif
            {{config('app.name')}}
        </title>

        @hasSection('meta-description')
            <meta name="description" content="@yield('title')"/>
        @endif
        <meta name="csrf-token" content="{{ csrf_token() }}"/>

        <meta name="mobile-web-app-capable" content="yes"/>
        <meta name="apple-mobile-web-app-capable" content="yes"/>

        <meta name="apple-mobile-web-app-title" content="{{config('app.name')}}"/>
        <meta name="application-name" content="{{config('app.name')}}"/>

        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <script src="{{ mix('js/app.js') }}"></script>
        <link rel="shortcut icon" type="image/x-icon" href="/favicon.svg">
    </head>

    <body data-bs-theme="dark">
        <header>
            <nav class="navbar navbar-expand-md bg-black">
                <div class="container">
                    <a class="navbar-brand" href="/">
                        {{config('app.name')}}
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarCollapse"
                            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('user.home')}}">
                                    <i class="fa-solid fa-house-chimney"></i>
                                    Startseite
                                </a>
                            </li>
                            @auth
                                <li class="nav-item">
                                    <a class="nav-link" href="{{route('user.dashboard')}}">
                                        <i class="fa-solid fa-ranking-star"></i>
                                        Meine Übersicht
                                    </a>
                                </li>
                            @endauth
                            @auth
                                <li class="nav-item">
                                    <a class="nav-link" href="{{route('map')}}">Karte</a>
                                </li>
                            @endauth
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('companies')}}">
                                    Verkehrsunternehmen
                                </a>
                            </li>
                        </ul>

                        <ul class="navbar-nav me-right">
                            @guest
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @else
                                @admin
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" role="button"
                                       data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-toolbox"></i>
                                        Administration
                                        <span class="caret"></span>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{route('admin.dashboard')}}">Dashboard</a>
                                        <a class="dropdown-item" href="{{route('admin.verify')}}">Zuordnung</a>
                                        <a class="dropdown-item" href="{{route('admin.ignored')}}">Ausschluß</a>
                                        <a class="dropdown-item" href="{{route('admin.location')}}">Import</a>
                                    </div>
                                </li>
                                @endadmin

                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" role="button"
                                       data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-regular fa-user"></i>
                                        {{ Auth::user()->name }}
                                        <span class="caret"></span>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('user.settings') }}">
                                            <i class="fa-solid fa-user-gear"></i>
                                            Einstellungen
                                        </a>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <main role="main">
            @yield('jumbotron')

            <div class="album py-2">
                <div class="container">
                    @include('layout.components.alerts')

                    @yield('content')
                </div>
            </div>
        </main>

        <footer class="text-muted">
            <div class="container">
                <a href="{{route('imprint')}}">Impressum</a>
                <p class="float-end">
                    <a href="#">Back to top</a>
                </p>
            </div>
        </footer>
    </body>
</html>
