@extends('layouts.public')
@section('title', 'Beranda')
@section('content')
@include('partials.feedback')
<section class="hero">
    <div class="stack">
        <h1 class="display">Meja untuk<br>makan bersama.</h1>
        <p class="muted hero-copy">Pasta, pizza, dan waktu untuk menikmati makan bersama di Distretto 10.</p>
        <p>Reservasi untuk 1 sampai 6 orang.<br>Deposit @rupiah(config('restaurant.deposit_amount')) per reservasi.</p>
        <a class="button" href="{{ route(auth()->user()?->isAdmin() ? 'admin.reservations.index' : 'reservations.create') }}">Reservasi meja</a>
        <p class="small muted">Setiap hari, 12.00 - 22.00 WIB</p>
    </div>
    <img class="hero-photo" src="{{ asset('images/menu/pasta-pesto.jpg') }}" alt="Pasta dengan pesto basil dan tomat ceri. Foto ilustrasi." width="600" height="440">
</section>
<hr>
<section class="menu-preview">
    <div class="stack">
        <h2>Pasta, pizza,<br>dan dolci.</h2>
        <p class="muted">Menu sederhana dengan pilihan untuk makan siang atau malam.</p>
        <a class="button secondary" href="{{ route('menu') }}">Lihat menu</a>
    </div>
    <div>
        @forelse($items as $item)
            <article class="preview-row">
                <div><h3>{{ $item->name }}</h3><strong>@rupiah($item->price)</strong></div>
                <p class="muted">{{ $item->description }}</p>
            </article>
        @empty
            <p class="muted">Menu sedang diperbarui. Silakan cek kembali nanti.</p>
        @endforelse
    </div>
</section>
@endsection
