@extends('layouts.public')
@section('title', 'Reservasi saya')
@section('content')
<x-intro title="Reservasi saya" description="Lihat jadwal dan status reservasi Anda." />
@if($reservations->count())
<div class="table-wrap">
    <table>
        <caption class="sr-only">Riwayat reservasi Anda</caption>
        <thead><tr><th scope="col">Kode</th><th scope="col">Tanggal & jam</th><th scope="col">Tamu</th><th scope="col">Status</th><th scope="col">Tindakan</th></tr></thead>
        <tbody>
            @foreach($reservations as $reservation)
                <tr><td class="record-code">{{ $reservation->code }}</td><td>{{ $reservation->reservation_date->translatedFormat('d M Y') }}<br>{{ $reservation->timeSlot->label() }} WIB</td><td>{{ $reservation->guest_count }} orang</td><td><x-status :reservation="$reservation" /></td><td><a href="{{ route('reservations.show', $reservation) }}">Lihat detail<span class="sr-only"> {{ $reservation->code }}</span></a></td></tr>
            @endforeach
        </tbody>
    </table>
</div>
<x-pagination :items="$reservations" />
@else
    <div class="empty"><h2>Belum ada reservasi.</h2><p>Mulai dengan memilih tanggal dan jumlah tamu untuk kunjungan Anda.</p></div>
@endif
<a class="button" href="{{ route('reservations.create') }}">Reservasi baru</a>
@endsection

