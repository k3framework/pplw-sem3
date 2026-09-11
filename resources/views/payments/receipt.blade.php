@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.public')
@section('title', 'Bukti pembayaran')
@section('breadcrumb', 'Transaksi')
@section('content')
@php
    $isAdmin = auth()->user()->isAdmin();
    $reservation = $payment->reservation;
    $zone = config('restaurant.timezone');
@endphp
<x-intro :title="$isAdmin ? 'Bukti transaksi' : 'Bukti pembayaran'" :description="$isAdmin ? $payment->code : 'Deposit reservasi Distretto 10'" />
<article class="panel stack receipt-panel {{ $isAdmin ? 'admin-receipt' : 'customer-receipt' }}" aria-label="Bukti transaksi deposit">
    <h2 class="wordmark {{ $isAdmin ? 'print-only' : '' }}">Distretto 10</h2>
    <div><x-payment-status :payment="$payment" /></div>
    <dl class="details">
        <div><dt>Kode transaksi</dt><dd>{{ $payment->code }}</dd></div>
        <div><dt>Kode reservasi</dt><dd>{{ $reservation->code }}</dd></div>
        <div><dt>Pelanggan</dt><dd>{{ $reservation->user->name }}</dd></div>
        <div><dt>Jadwal kunjungan</dt><dd>{{ $reservation->reservation_date->translatedFormat('d F Y') }} · {{ $reservation->timeSlot->label() }} WIB</dd></div>
        <div><dt>Metode</dt><dd>{{ $payment->methodLabel() }}</dd></div>
        <div><dt>Referensi</dt><dd class="preserve-lines">{{ $payment->reference ?: 'Tidak ada referensi' }}</dd></div>
        <div><dt>Waktu pembayaran</dt><dd>{{ $payment->paid_at->timezone($zone)->translatedFormat('d F Y, H.i') }} WIB</dd></div>
        <div><dt>Dicatat oleh</dt><dd>{{ $payment->processor->name }}</dd></div>
        @if($payment->status === 'refunded')
            <div><dt>Dikembalikan</dt><dd>{{ $payment->refunded_at->timezone($zone)->translatedFormat('d F Y, H.i') }} WIB</dd></div>
            <div><dt>Dicatat oleh</dt><dd>{{ $payment->refunder->name }} (pengembalian)</dd></div>
        @endif
    </dl>
    <hr>
    <dl class="details"><div><dt>Jumlah deposit</dt><dd>@rupiah($payment->amount)</dd></div></dl>
    @if($payment->status === 'refunded')<p class="muted">Deposit dikembalikan penuh. Reservasi dibatalkan.</p>@endif
    <p class="small muted">Bukti transaksi simulasi.</p>
</article>
<div class="actions receipt-actions">
    <a class="button secondary" href="{{ $isAdmin ? route('admin.payments.index') : route('reservations.show', $reservation) }}">{{ $isAdmin ? 'Kembali ke transaksi' : 'Kembali ke reservasi' }}</a>
    <button type="button" class="button secondary" data-print>Cetak bukti</button>
</div>
@endsection
