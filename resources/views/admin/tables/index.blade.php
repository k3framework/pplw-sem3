@extends('layouts.admin')
@section('title', 'Meja restoran')
@section('breadcrumb', 'Meja')
@section('content')
<x-intro title="Meja restoran" description="Atur kapasitas dan meja yang dapat dipakai untuk reservasi." />
<a class="button" href="{{ route('admin.tables.create') }}">Tambah meja</a>
<div class="table-wrap"><table>
    <caption class="sr-only">Meja restoran</caption>
    <thead><tr><th scope="col">Kode meja</th><th scope="col">Kapasitas</th><th scope="col">Status</th><th scope="col">Riwayat reservasi</th><th scope="col">Tindakan</th></tr></thead>
    <tbody>@forelse($tables as $table)
        <tr><td>{{ $table->code }}</td><td>{{ $table->capacity }} orang</td><td>{{ $table->is_active ? 'Aktif' : 'Nonaktif' }}</td><td>{{ $table->reservations_count }}</td><td><a href="{{ route('admin.tables.edit', $table) }}">Edit<span class="sr-only"> {{ $table->code }}</span></a></td></tr>
    @empty<tr><td colspan="5" class="empty-cell">Belum ada meja. Tambahkan meja agar pelanggan dapat reservasi.</td></tr>@endforelse</tbody>
</table></div>
<x-pagination :items="$tables" />
@endsection

