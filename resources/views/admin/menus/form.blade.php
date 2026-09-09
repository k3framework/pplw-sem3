@extends('layouts.admin')
@section('title', $menu->exists ? 'Edit menu' : 'Tambah menu')
@section('breadcrumb', 'Menu')
@section('content')
<x-intro :title="$menu->exists ? 'Edit menu' : 'Tambah menu'" :description="$menu->exists ? 'Perubahan berlaku setelah disimpan.' : 'Isi hidangan, harga, dan foto untuk menambah menu.'" />
@if($categories->isEmpty())
    <div class="notice warning"><strong>Tambahkan kategori terlebih dahulu.</strong><p>Menu memerlukan satu kategori.</p><a href="{{ route('admin.categories.create') }}">Tambah kategori</a></div>
@else
<div class="panel master-panel">
    <form class="stack" action="{{ $menu->exists ? route('admin.menus.update', $menu) : route('admin.menus.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @if($menu->exists) @method('patch') @endif
        <x-field name="name" label="Nama menu" :value="$menu->name" maxlength="100" :required="true" />
        <x-field name="menu_category_id" label="Kategori" type="select" :required="true">
            <option value="">Pilih kategori</option>
            @foreach($categories as $category)<option value="{{ $category->id }}" @selected((int)old('menu_category_id', $menu->menu_category_id) === $category->id)>{{ $category->name }}{{ $category->is_active ? '' : ' (nonaktif)' }}</option>@endforeach
        </x-field>
        <x-field name="price" label="Harga (rupiah)" type="number" :value="$menu->price" min="0.01" max="9999999999.99" step="0.01" hint="Gunakan angka tanpa pemisah ribuan." :required="true" />
        <x-field name="description" label="Deskripsi" type="textarea" :value="$menu->description" maxlength="2000" />
        @if($menu->exists)<img class="edit-photo" src="{{ $menu->imageUrl() }}" alt="Foto saat ini untuk {{ $menu->name }}" width="220" height="144">@endif
        <x-field name="photo" label="Foto" type="file" accept="image/jpeg,image/png,image/webp" :required="!$menu->exists" :hint="$menu->exists ? 'JPG, PNG, atau WebP; maksimal 2 MB. Kosongkan untuk memakai foto saat ini.' : 'JPG, PNG, atau WebP; maksimal 2 MB.'" />
        @include('partials.active-field', ['value' => $menu->is_active ?? true])
        <div class="actions"><button class="button" data-progress="Menyimpan…">Simpan menu</button><a class="button secondary" href="{{ route('admin.menus.index') }}">Kembali</a></div>
    </form>
    @if($menu->exists)<x-confirm-form :action="route('admin.menus.destroy', $menu)" method="delete" label="Hapus menu" :message="'Hapus menu '.$menu->name.'? Foto unggahan juga akan dihapus.'" />@endif
</div>
@endif
@endsection

