<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Distretto 10') · Distretto 10</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="{{ asset('js/app.js') }}"></script>
</head>
<body>
    <a class="skip-link" href="#main">Lewati navigasi</a>
    <header class="public-header container">
        <a class="wordmark" href="{{ route('home') }}">Distretto 10</a>
        <nav aria-label="Navigasi utama">
            <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Beranda</a>
            <a href="{{ route('menu') }}" @if(request()->routeIs('menu')) aria-current="page" @endif>Menu</a>
            @if(auth()->user()?->isAdmin())
                <a href="{{ route('admin.dashboard') }}">Pengelolaan</a>
            @else
                <a href="{{ route('reservations.create') }}" @if(request()->routeIs('reservations.create', 'reservations.check', 'reservations.edit*')) aria-current="page" @endif>Reservasi</a>
                @auth
                    <a href="{{ route('reservations.index') }}" @if(request()->routeIs('reservations.index', 'reservations.show')) aria-current="page" @endif>Reservasi Saya</a>
                @endauth
            @endif
            @auth
                <form action="{{ route('logout') }}" method="post">@csrf
                    <button class="button secondary nav-button">Keluar</button>
                </form>
            @else
                <a class="button secondary nav-button" href="{{ route('login') }}">Masuk</a>
            @endauth
        </nav>
    </header>
    <main id="main" class="public-main container" tabindex="-1">
        @yield('content')
    </main>
    <footer class="public-footer">
        <div class="container">
            <strong>Distretto 10 · Restoran demo</strong>
            <p>Setiap hari, 12.00 - 22.00 WIB</p>
        </div>
    </footer>
</body>
</html>
