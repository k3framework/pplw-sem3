@extends('layouts.admin')
@section('title', 'Menu restoran')
@section('breadcrumb', 'Menu')
@section('content')
<x-intro title="Menu restoran" description="Atur hidangan, harga, foto, dan menu yang ditampilkan." />
<a class="button" href="{{ route('admin.menus.create') }}">Tambah menu</a>
<div class="table-wrap"><table>
    <caption class="sr-only">Menu restoran</caption>
    <thead><tr><th scope="col">Nama menu</th><th scope="col">Kategori</th><th scope="col">Harga</th><th scope="col">Status</th><th scope="col">Tindakan</th></tr></thead>
    <tbody>@forelse($menus as $menu)
        <tr><td>{{ $menu->name }}</td><td>{{ $menu->category->name }}</td><td>@rupiah($menu->price)</td><td>{{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}</td><td><a href="{{ route('admin.menus.edit', $menu) }}">Edit<span class="sr-only"> {{ $menu->name }}</span></a></td></tr>
    @empty<tr><td colspan="5" class="empty-cell">Belum ada menu. Tambahkan hidangan agar dapat dilihat pelanggan.</td></tr>@endforelse</tbody>
</table></div>
<x-pagination :items="$menus" />
@endsection

