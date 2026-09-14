@extends('layouts.public')
@section('title', 'Halaman tidak ditemukan')
@section('content')
<x-intro title="Halaman tidak ditemukan" description="Alamat atau data yang Anda buka tidak tersedia." />
<a class="button secondary" href="{{ route('home') }}">Kembali ke beranda</a>
@endsection

