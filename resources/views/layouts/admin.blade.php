<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Pengelolaan Distretto 10</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="{{ asset('js/app.js') }}"></script>
</head>
<body class="admin-body">
    <a class="skip-link" href="#main">Lewati navigasi</a>
    <aside class="admin-sidebar">
        <a class="wordmark" href="{{ route('home') }}">Distretto 10</a>
        <p class="small muted">Pengelolaan restoran</p>
        <hr>
        <nav aria-label="Navigasi pengelolaan">
            @php($isReservationPayment = request()->routeIs('admin.payments.create', 'admin.payments.store', 'admin.payments.refund', 'admin.payments.finish'))
            @foreach(['dashboard' => 'Dashboard', 'reservations' => 'Reservasi', 'categories' => 'Kategori Menu', 'menus' => 'Menu', 'tables' => 'Meja', 'payments' => 'Transaksi'] as $key => $label)
                <a href="{{ route('admin.'.$key.($key === 'dashboard' ? '' : '.index')) }}"
                    @if(($key === 'reservations' && $isReservationPayment) || (request()->routeIs('admin.'.$key, 'admin.'.$key.'.*') && !($key === 'payments' && $isReservationPayment))) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>
        <div class="sidebar-account">
            <strong>{{ auth()->user()->name }}</strong>
            <form action="{{ route('logout') }}" method="post">@csrf
                <button class="button secondary full-width">Keluar</button>
            </form>
        </div>
    </aside>
    <main id="main" class="admin-main" tabindex="-1">
        <div class="account-bar"><p class="small muted">Pengelolaan / @yield('breadcrumb', 'Reservasi')</p><strong>{{ auth()->user()->name }}</strong></div>
        @yield('content')
    </main>
</body>
</html>
