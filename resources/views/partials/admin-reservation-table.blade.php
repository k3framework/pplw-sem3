<div class="table-wrap">
<table>
    <caption class="sr-only">Daftar reservasi restoran</caption>
    <thead><tr><th scope="col">Kode</th><th scope="col">Pelanggan</th><th scope="col">Jadwal</th><th scope="col">Tamu</th><th scope="col">Status</th><th scope="col">Tindakan</th></tr></thead>
    <tbody>
        @forelse($reservations as $reservation)
            <tr><td class="record-code">{{ $reservation->code }}</td><td>{{ $reservation->user->name }}</td><td>{{ $reservation->reservation_date->translatedFormat('d M Y') }}<br>{{ $reservation->timeSlot->label() }} WIB</td><td>{{ $reservation->guest_count }} orang</td><td><x-status :reservation="$reservation" /></td><td><a href="{{ route('admin.reservations.show', $reservation) }}">Lihat detail<span class="sr-only"> {{ $reservation->code }}</span></a></td></tr>
        @empty
            <tr><td colspan="6" class="empty-cell">Belum ada reservasi untuk pilihan ini. Coba tanggal atau status lain.</td></tr>
        @endforelse
    </tbody>
</table>
</div>

