@extends('layouts.admin')
@section('title', 'Catat pembayaran')
@section('content')
<x-intro title="Catat pembayaran" :description="'Deposit untuk '.$reservation->code" />
<form class="panel master-panel payment-form stack" id="payment" method="post" action="{{ route('admin.payments.store', $reservation) }}">
    @csrf
    <div class="notice"><strong>Pastikan pembayaran sudah diterima</strong><p>Setelah disimpan, pembayaran tidak dapat diedit atau dihapus.</p></div>
    <x-field name="reservation_code" label="Kode reservasi" :value="$reservation->code" disabled />
    <x-field name="deposit_display" label="Jumlah deposit" :value="'Rp'.number_format((float) $reservation->deposit_amount, fmod((float) $reservation->deposit_amount, 1) == 0 ? 0 : 2, ',', '.')" hint="Nominal mengikuti deposit saat reservasi dibuat." disabled />
    <x-field name="method" label="Metode pembayaran" type="select" hint="Tunai atau transfer dicatat manual." required>
        @foreach(App\Models\Payment::METHODS as $value => $label)<option value="{{ $value }}" @selected(old('method', 'cash') === $value)>{{ $label }}</option>@endforeach
    </x-field>
    <x-field name="reference" label="Referensi (opsional)" maxlength="255" placeholder="Contoh: nomor bukti transfer" />
    <div class="actions">
        <button class="button" data-progress="Menyimpan…">Simpan pembayaran</button>
        <a class="button secondary" href="{{ route('admin.reservations.show', $reservation) }}">Kembali</a>
    </div>
    <p class="small muted">Pembayaran simulasi.</p>
</form>
@endsection
