@extends('layouts.public')
@section('title', 'Masuk')
@section('content')
<div class="auth-grid">
    <img class="auth-photo" src="{{ asset('images/restaurant/interior.jpg') }}" alt="Interior restoran bernuansa hangat. Foto ilustrasi." width="600" height="640">
    <form class="stack" action="{{ route('login') }}" method="post">
        @csrf
        <x-intro title="Masuk" description="Masuk untuk membuat atau melihat reservasi." />
        <x-field name="email" label="Email" type="email" autocomplete="username" maxlength="255" :required="true" />
        <x-field name="password" label="Kata sandi" type="password" autocomplete="current-password" :required="true" />
        <button class="button full-width" data-progress="Memeriksa akun…">Masuk</button>
        <a href="{{ route('register') }}">Belum punya akun? Daftar</a>
    </form>
</div>
@endsection

