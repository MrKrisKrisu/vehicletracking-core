<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

        <title>@hasSection('title')@yield('title') - @endif{{__('Vehicletracking')}}</title>

        @hasSection('meta-description')
            <meta name="description" content="@yield('title')"/>
        @endif

        <meta name="mobile-web-app-capable" content="yes"/>
        <meta name="apple-mobile-web-app-capable" content="yes"/>

        <meta name="apple-mobile-web-app-title" content="VehicleTracking"/>
        <meta name="application-name" content="VehicleTracking"/>

        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <script src="{{ mix('js/app.js') }}"></script>
        <link rel="shortcut icon" type="image/x-icon" href="/favicon.svg">
    </head>

    <body>
        <header>
            <nav class="navbar navbar-expand-md navbar-dark bg-dark">
                <div class="container">
                    <a class="navbar-brand" href="/">{{__('Vehicle tracking')}}</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <ul class="navbar-nav mr-auto">
                            @auth
                                <li class="nav-item">
                                    <a class="nav-link" href="/">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/verify/">Zuordnung</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{route('ignored')}}">Ausschluß</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{route('location')}}">Import</a>
                                </li>
                            @endauth
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('map')}}">Karte</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('companies')}}">Verkehrsunternehmen</a>
                            </li>
                            @auth
                                <li class="nav-item">
                                    <a class="nav-link" href="{{route('notifications')}}">Benachrichtigungen</a>
                                </li>
                            @endauth
                        </ul>

                        <ul class="navbar-nav mr-right">
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
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }} <span class="caret"></span>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('user.settings') }}">
                                            Einstellungen
                                        </a>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
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

            <div class="album py-2 bg-light">
                <div class="container">
                    @include('layout.components.alerts')

                    @yield('content')
                </div>
            </div>

        </main>

        <footer class="text-muted">
            <div class="container">
                <a href="{{route('imprint')}}">Impressum</a>
                <p class="float-right">
                    <a href="#">Back to top</a>
                </p>
            </div>
        </footer>
    </body>
    <script type="text/javascript">
        var _paq = window._paq = window._paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function () {
            var u = "//{{config('app.matomo.url')}}/";
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', '{{config('app.matomo.id')}}']);
            var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
            g.type = 'text/javascript';
            g.async = true;
            g.src = u + 'matomo.js';
            s.parentNode.insertBefore(g, s);
        })();
    </script>
    <noscript><p><img src="//{{config('app.matomo.url')}}/matomo.php?idsite={{config('app.matomo.id')}}&amp;rec=1"
                      style="border:0;" alt=""/></p></noscript>
</html>
