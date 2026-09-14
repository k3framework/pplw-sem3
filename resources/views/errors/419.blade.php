@extends('layouts.public')
@section('title', 'Sesi kedaluwarsa')
@section('content')
<x-intro title="Sesi kedaluwarsa" description="Muat ulang form lalu kirim kembali agar perubahan dapat disimpan." />
<a class="button secondary" href="{{ url()->previous() }}">Muat ulang halaman sebelumnya</a>
@endsection

