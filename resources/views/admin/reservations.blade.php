@extends('layouts.admin')
@section('title', 'Reservasi')
@section('content')
<x-intro title="Reservasi" description="Kelola jadwal kunjungan, jumlah tamu, dan status reservasi." />
<form class="filter-form" method="get" action="{{ route('admin.reservations.index') }}">
    <x-field name="q" label="Cari kode reservasi" :value="request('q')" maxlength="100" placeholder="Semua reservasi" />
    <x-field name="date" label="Tanggal" type="date" :value="request('date')" />
    <x-field name="status" label="Status" type="select">
        <option value="">Semua status</option>
        @foreach(App\Models\Reservation::LABELS as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach
    </x-field>
    <button class="button secondary" data-progress="Memfilter…">Terapkan</button>
    @if(request()->anyFilled(['q', 'date', 'status']))<a href="{{ route('admin.reservations.index') }}">Reset</a>@endif
</form>
@include('partials.admin-reservation-table')
<p class="small muted">{{ $reservations->total() }} reservasi · Menampilkan {{ $reservations->firstItem() ?? 0 }}–{{ $reservations->lastItem() ?? 0 }}</p>
<x-pagination :items="$reservations" />
@endsection

