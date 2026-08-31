@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.public')
@section('title', $reservation ? 'Ubah reservasi' : 'Reservasi meja')
@section('content')
@php
    $prefix = auth()->user()->isAdmin() ? 'admin.reservations.' : 'reservations.';
    $date = old('reservation_date', $input['reservation_date'] ?? now(config('restaurant.timezone'))->addDay()->toDateString());
    $guests = old('guest_count', $input['guest_count'] ?? 2);
    $selectedSlot = old('time_slot_id', $input['time_slot_id'] ?? '');
    $date = is_string($date) ? $date : '';
    $guests = is_scalar($guests) ? $guests : '';
    $selectedSlot = is_scalar($selectedSlot) ? $selectedSlot : '';
    $availableCount = collect($availability ?? [])->sum('count');
    $checkUrl = $reservation ? route($prefix.'edit-check', $reservation) : route('reservations.check');
    $saveUrl = $reservation ? route($prefix.'update', $reservation) : route('reservations.store');
@endphp
<x-intro :title="$reservation ? 'Ubah reservasi' : 'Reservasi meja'" description="Pilih tanggal, jumlah tamu, dan jam kunjungan. Satu reservasi untuk satu meja." />
<div class="reservation-grid">
    <form class="panel stack" action="{{ $saveUrl }}" method="post" data-booking-form>
        @csrf
        <h2 class="section-title">Jadwal kunjungan</h2>
        @if($reservation)<p class="small muted record-code">{{ $reservation->code }} · {{ $reservation->user->name }}</p>@endif
        <div class="two-columns">
            <x-field name="reservation_date" label="Tanggal" type="date" :value="$date" :min="now(config('restaurant.timezone'))->toDateString()" :max="now(config('restaurant.timezone'))->addDays(30)->toDateString()" hint="Sampai 30 hari ke depan." :required="true" />
            <x-field name="guest_count" label="Jumlah tamu" type="select" hint="1 sampai 6 orang." :required="true">
                @for($i = 1; $i <= 6; $i++)<option value="{{ $i }}" @selected((int)$guests === $i)>{{ $i }} orang</option>@endfor
            </x-field>
        </div>
        <button type="submit" class="button secondary" formaction="{{ $checkUrl }}" formnovalidate data-check data-progress="Mengecek meja…">Cek ketersediaan</button>
        <div class="notice {{ $availability && !$availableCount ? 'warning' : ($availability ? 'success' : 'neutral') }}" data-availability-notice role="status" aria-live="polite">
            @if($availability === null)
                <strong>Cek jadwal terlebih dahulu.</strong><p>Isi tanggal dan jumlah tamu, lalu cek ketersediaan untuk melihat pilihan jam.</p>
            @elseif($availableCount)
                <strong>Meja tersedia</strong><p>Pilih salah satu jam yang tersedia untuk {{ $guests }} orang. Ketersediaan diperiksa lagi saat reservasi disimpan.</p>
            @else
                <strong>Meja tidak tersedia.</strong><p>Semua slot penuh atau sudah lewat. Coba tanggal lain atau kurangi jumlah tamu, lalu cek kembali.</p>
            @endif
        </div>
        <x-field name="time_slot_id" label="Jam kunjungan" type="select" :disabled="!$availableCount" hint="Meja dipilih otomatis sesuai kapasitas." :required="true">
            <option value="">Pilih jam kunjungan</option>
            @foreach($availability ?? [] as $slot)
                <option value="{{ $slot['id'] }}" @selected((string)$selectedSlot === (string)$slot['id'] && $slot['count']) @disabled(!$slot['count'])>{{ $slot['label'] }} WIB{{ $slot['count'] ? ' · '.$slot['count'].' meja tersedia' : ' · Tidak tersedia' }}</option>
            @endforeach
        </x-field>
        <x-field name="notes" label="Catatan (opsional)" type="textarea" :value="$input['notes'] ?? ''" maxlength="500" hint="Maksimal 500 karakter." placeholder="Contoh: ada tamu yang alergi kacang." />
        <button class="button full-width" type="submit" @if($reservation) name="_method" value="PATCH" @endif @disabled(!$availableCount) data-save data-progress="Menyimpan reservasi…">{{ $reservation ? 'Simpan perubahan' : 'Buat reservasi' }}</button>
        @if($reservation)<a href="{{ route($prefix.'show', $reservation) }}">Kembali ke detail reservasi</a>@endif
    </form>
    <aside class="panel subtle stack">
        <h2 class="section-title">Ringkasan</h2>
        <dl class="details">
            <div><dt>Tanggal</dt><dd data-summary-date>{{ $date }}</dd></div>
            <div><dt>Jumlah tamu</dt><dd data-summary-guests>{{ $guests }} orang</dd></div>
            <div><dt>Jam</dt><dd data-summary-slot>{{ collect($availability ?? [])->firstWhere('id', (int)$selectedSlot)['label'] ?? 'Belum dipilih' }}</dd></div>
        </dl>
        <hr>
        <p class="muted">Deposit reservasi</p>
        <p class="amount">@rupiah($reservation?->deposit_amount ?? config('restaurant.deposit_amount'))</p>
        <p class="muted">Deposit dicatat oleh admin. Reservasi terkonfirmasi setelah pembayaran tercatat.</p>
        <p class="small muted">Reservasi yang belum dibayar dapat diubah atau dibatalkan sebelum waktu mulai.</p>
    </aside>
</div>
@endsection
