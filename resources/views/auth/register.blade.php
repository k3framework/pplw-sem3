@extends('layouts.public')
@section('title', 'Buat akun')
@section('content')
<div class="auth-grid">
    <img class="auth-photo" src="{{ asset('images/restaurant/interior.jpg') }}" alt="Interior restoran bernuansa hangat. Foto ilustrasi." width="600" height="640">
    <form class="stack" action="{{ route('register') }}" method="post">
        @csrf
        <x-intro title="Buat akun" description="Simpan dan kelola reservasi dengan akunmu." />
        <x-field name="name" label="Nama" autocomplete="name" maxlength="100" :required="true" />
        <x-field name="email" label="Email" type="email" autocomplete="username" maxlength="255" :required="true" />
        <x-field name="phone" label="Nomor telepon" type="tel" autocomplete="tel" maxlength="16" hint="Nomor ini digunakan untuk kontak reservasi. Contoh: 081234567890." :required="true" />
        <x-field name="password" label="Kata sandi" type="password" autocomplete="new-password" minlength="8" maxlength="72" hint="Gunakan minimal 8 karakter." :required="true" />
        <x-field name="password_confirmation" label="Konfirmasi kata sandi" type="password" autocomplete="new-password" minlength="8" maxlength="72" :required="true" />
        <button class="button full-width" data-progress="Membuat akun…">Buat akun</button>
        <a href="{{ route('login') }}">Sudah punya akun? Masuk</a>
    </form>
</div>
@endsection

