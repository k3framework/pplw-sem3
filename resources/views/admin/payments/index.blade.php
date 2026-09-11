@extends('layouts.admin')
@section('title', 'Transaksi')
@section('breadcrumb', 'Transaksi')
@section('content')
<x-intro title="Transaksi" description="Catatan deposit dan pengembalian. Transaksi tidak dapat diedit atau dihapus." />
<form class="filter-form" method="get" action="{{ route('admin.payments.index') }}">
    <x-field name="q" label="Cari kode transaksi / reservasi" :value="request('q')" maxlength="100" placeholder="Semua transaksi" />
    <x-field name="status" label="Status pembayaran" type="select">
        <option value="">Semua status</option>
        @foreach(App\Models\Payment::LABELS as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach
    </x-field>
    <button class="button secondary" data-progress="Memfilter…">Terapkan</button>
    @if(request()->anyFilled(['q', 'status']))<a href="{{ route('admin.payments.index') }}">Reset</a>@endif
</form>
<div class="table-wrap">
    <table>
        <caption class="sr-only">Catatan transaksi deposit dan pengembalian</caption>
        <thead><tr><th scope="col">Kode transaksi</th><th scope="col">Reservasi</th><th scope="col">Jumlah</th><th scope="col">Metode</th><th scope="col">Status</th><th scope="col">Tindakan</th></tr></thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td class="record-code">{{ $payment->code }}</td>
                    <td class="record-code"><a href="{{ route('admin.reservations.show', $payment->reservation) }}">{{ $payment->reservation->code }}</a></td>
                    <td>@rupiah($payment->amount)</td><td>{{ $payment->methodLabel() }}</td>
                    <td><x-payment-status :payment="$payment" /></td>
                    <td><a href="{{ route('admin.payments.show', $payment) }}">Lihat bukti<span class="sr-only"> {{ $payment->code }}</span></a></td>
                </tr>
            @empty
                <tr><td class="empty-cell" colspan="6">{{ request()->anyFilled(['q', 'status']) ? 'Tidak ada transaksi yang cocok dengan filter.' : 'Belum ada transaksi. Catat pembayaran dari detail reservasi yang menunggu pembayaran.' }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<p class="small muted">{{ $payments->total() }} transaksi · Menampilkan {{ $payments->firstItem() ?? 0 }}–{{ $payments->lastItem() ?? 0 }}</p>
<x-pagination :items="$payments" />
@endsection
