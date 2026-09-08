@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.public')
@section('title', 'Detail reservasi')
@section('content')
@php($prefix = auth()->user()->isAdmin() ? 'admin.reservations.' : 'reservations.')
<x-intro title="Detail reservasi" :description="$reservation->code" />
@if($reservation->status === 'confirmed' && !session('success') && !$errors->any())
    <div class="notice success"><strong>Reservasi terkonfirmasi</strong><p>{{ $reservation->isFuture() ? 'Deposit sudah tercatat. Datang sesuai jadwal yang dipilih.' : 'Deposit sudah tercatat. Jadwal kunjungan telah dimulai.' }}</p></div>
@endif
<div class="reservation-grid">
    <section class="panel stack">
        <div id="status"><x-status :reservation="$reservation" /></div>
        <dl class="details">
            <div><dt>Pelanggan</dt><dd>{{ $reservation->user->name }}</dd></div>
            @if(auth()->user()->isAdmin())<div><dt>Kontak</dt><dd>{{ $reservation->user->email }}<br>{{ $reservation->user->phone }}</dd></div>@endif
            <div><dt>Tanggal</dt><dd>{{ $reservation->reservation_date->translatedFormat('l, d F Y') }}</dd></div>
            <div><dt>Jam</dt><dd>{{ $reservation->timeSlot->label() }} WIB</dd></div>
            <div><dt>Jumlah tamu</dt><dd>{{ $reservation->guest_count }} orang</dd></div>
            <div><dt>Meja</dt><dd>{{ $reservation->table->code }} · Kapasitas {{ $reservation->table->capacity }} orang</dd></div>
            <div><dt>Catatan</dt><dd class="preserve-lines">{{ $reservation->notes ?: 'Tidak ada catatan' }}</dd></div>
        </dl>
        <div class="actions">
            @can('update', $reservation)<a class="button secondary" href="{{ route($prefix.'edit', $reservation) }}">Ubah reservasi</a>@endcan
            @can('cancel', $reservation)<x-confirm-form :action="route($prefix.'cancel', $reservation)" label="Batalkan reservasi" message="Batalkan reservasi ini? Meja akan tersedia kembali dan riwayat reservasi tetap tersimpan." />@endcan
        </div>
        @if($reservation->status === 'pending' && !$reservation->isFuture() && !auth()->user()->isAdmin())
            <p class="small muted">Waktu kunjungan sudah lewat. Hubungi admin restoran untuk membatalkan reservasi.</p>
        @endif
        @if(auth()->user()->isAdmin() && $reservation->status === 'confirmed')
            <div class="actions">
                @foreach(['completed' => 'Selesai', 'no_show' => 'Tidak hadir'] as $value => $label)
                    @can('finish', $reservation)
                        <x-confirm-form :action="route('admin.payments.finish', $reservation)" :label="$label" :message="'Catat kunjungan sebagai '.strtolower($label).'? Status akhir tidak dapat diubah kembali.'" name="status" :value="$value" button-class="button secondary" :confirm-label="'Catat '.$label" />
                    @else
                        <button class="button secondary" disabled>{{ $label }}</button>
                    @endcan
                @endforeach
            </div>
            @cannot('finish', $reservation)<p class="small muted">Status kunjungan dapat dicatat setelah slot berakhir.</p>@endcannot
            @can('refund', $reservation)
                <x-confirm-form :action="route('admin.payments.refund', $reservation)" label="Batalkan & kembalikan deposit" title="Batalkan dan kembalikan deposit?" confirm-label="Catat pengembalian" :message="'Reservasi '.$reservation->code.' akan dibatalkan. Pastikan Rp'.number_format((float) $reservation->deposit_amount, fmod((float) $reservation->deposit_amount, 1) == 0 ? 0 : 2, ',', '.').' telah dikembalikan kepada pelanggan sebelum menyimpan.'" />
            @endcan
        @endif
    </section>
    <aside class="panel subtle stack" id="payment">
        <h2 class="section-title">Deposit reservasi</h2>
        <p class="amount">@rupiah($reservation->deposit_amount)</p>
        @if($reservation->payment)
            <div><x-payment-status :payment="$reservation->payment" /></div>
            <dl class="details"><div><dt>Metode</dt><dd>{{ $reservation->payment->methodLabel() }}</dd></div></dl>
            <p class="small record-code">{{ $reservation->payment->code }}</p>
            <p class="small muted">Dicatat {{ $reservation->payment->paid_at->timezone(config('restaurant.timezone'))->translatedFormat('d M Y, H.i') }} WIB.</p>
            @if($reservation->payment->status === 'refunded')<p class="small muted">Dikembalikan penuh {{ $reservation->payment->refunded_at->timezone(config('restaurant.timezone'))->translatedFormat('d M Y, H.i') }} WIB.</p>@endif
            <a class="button secondary full-width" href="{{ route(auth()->user()->isAdmin() ? 'admin.payments.show' : 'payments.show', $reservation->payment) }}">{{ auth()->user()->isAdmin() ? 'Lihat transaksi' : 'Lihat bukti pembayaran' }}</a>
            @if($reservation->status === 'confirmed' && !auth()->user()->isAdmin())<p class="small muted">Pembatalan reservasi terkonfirmasi ditangani admin sebelum waktu mulai.</p>@endif
        @else
            <p class="muted">{{ $reservation->status === 'pending' ? 'Menunggu pembayaran. Admin restoran mencatat pembayaran deposit secara langsung.' : 'Belum ada pembayaran.' }}</p>
            @can('pay', $reservation)<a class="button full-width" href="{{ route('admin.payments.create', $reservation) }}">Catat pembayaran</a>@endcan
            @if(auth()->user()->isAdmin() && $reservation->status === 'pending' && !$reservation->isFuture())<p class="small muted">Waktu mulai sudah lewat. Deposit tidak dapat dicatat; batalkan reservasi tertunda ini.</p>@endif
        @endif
        <p class="small muted">Pembayaran dan pengembalian pada proyek demo ini merupakan simulasi.</p>
    </aside>
</div>
<a href="{{ route($prefix.'index') }}">{{ auth()->user()->isAdmin() ? 'Kembali ke daftar reservasi' : 'Kembali ke reservasi saya' }}</a>
@endsection
