@extends('layouts.admin')
@section('title', $category->exists ? 'Edit kategori' : 'Tambah kategori')
@section('breadcrumb', 'Kategori')
@section('content')
<x-intro :title="$category->exists ? 'Edit kategori' : 'Tambah kategori'" :description="$category->exists ? 'Perubahan berlaku setelah disimpan.' : 'Isi data untuk menambah kategori.'" />
<div class="panel master-panel">
    <form class="stack" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="post">
        @csrf
        @if($category->exists) @method('patch') @endif
        <x-field name="name" label="Nama kategori" :value="$category->name" maxlength="100" :required="true" />
        @include('partials.active-field', ['value' => $category->is_active ?? true])
        <div class="actions"><button class="button" data-progress="Menyimpan…">Simpan kategori</button><a class="button secondary" href="{{ route('admin.categories.index') }}">Kembali</a></div>
    </form>
    @if($category->exists)<x-confirm-form :action="route('admin.categories.destroy', $category)" method="delete" label="Hapus kategori" :message="'Hapus kategori '.$category->name.'? Kategori yang masih memiliki menu tidak dapat dihapus.'" />@endif
</div>
@endsection

