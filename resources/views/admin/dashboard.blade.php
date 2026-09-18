@extends('layouts.admin')
@section('title', 'Ringkasan')
@section('breadcrumb', 'Ringkasan')
@section('content')
<x-intro title="Ringkasan" :description="now(config('restaurant.timezone'))->translatedFormat('l, d F Y').' · Asia/Jakarta'" />
<div class="metrics">
    <section class="panel stack compact"><h2>Reservasi hari ini</h2><p class="amount">{{ $todayCount }}</p><p class="small muted">Reservasi aktif pada tanggal hari ini.</p></section>
    <section class="panel stack compact"><h2>Menunggu pembayaran</h2><p class="amount">{{ $pendingCount }}</p><p class="small muted">Reservasi hari ini dan mendatang.</p></section>
    <section class="panel stack compact"><h2>Deposit tercatat</h2><p class="amount">@rupiah($depositTotal)</p><p class="small muted">Nominal dicatat hari ini, sebelum refund.</p></section>
</div>
<h2 class="section-title">Jadwal hari ini</h2>
@include('partials.admin-reservation-table')
<a class="button secondary" href="{{ route('admin.reservations.index') }}">Lihat semua reservasi</a>
@endsection
