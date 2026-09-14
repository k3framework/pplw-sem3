@extends('layouts.public')
@section('title', 'Akses dibatasi')
@section('content')
<x-intro title="Akses dibatasi" description="Akun Anda tidak memiliki akses, atau reservasi sudah tidak dapat diubah." />
<p>Reservasi hanya dapat dikelola pemiliknya dan admin. Perubahan jadwal memerlukan status menunggu pembayaran serta waktu kunjungan yang belum dimulai.</p>
<a class="button secondary" href="{{ route(auth()->user()?->isAdmin() ? 'admin.reservations.index' : 'home') }}">Kembali</a>
@endsection

