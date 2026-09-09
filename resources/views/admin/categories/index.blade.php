@extends('layouts.admin')
@section('title', 'Kategori menu')
@section('breadcrumb', 'Kategori')
@section('content')
<x-intro title="Kategori menu" description="Kelompokkan hidangan dan atur kategori yang ditampilkan." />
<a class="button" href="{{ route('admin.categories.create') }}">Tambah kategori</a>
<div class="table-wrap"><table>
    <caption class="sr-only">Kategori menu restoran</caption>
    <thead><tr><th scope="col">Nama kategori</th><th scope="col">Jumlah menu</th><th scope="col">Status</th><th scope="col">Tindakan</th></tr></thead>
    <tbody>@forelse($categories as $category)
        <tr><td>{{ $category->name }}</td><td>{{ $category->menu_items_count }}</td><td>{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</td><td><a href="{{ route('admin.categories.edit', $category) }}">Edit<span class="sr-only"> {{ $category->name }}</span></a></td></tr>
    @empty<tr><td colspan="4" class="empty-cell">Belum ada kategori. Tambahkan kategori untuk mengelompokkan menu.</td></tr>@endforelse</tbody>
</table></div>
<x-pagination :items="$categories" />
@endsection

