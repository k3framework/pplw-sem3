@extends('layouts.public')
@section('title', 'Menu')
@section('content')
<x-intro title="Menu" description="Pilih hidangan untuk kunjunganmu. Harga dan menu berikut adalah data demo." />
<nav class="filters" aria-label="Kategori menu">
    <a class="button {{ request('category') ? 'secondary' : '' }}" href="{{ route('menu') }}" @if(!request('category')) aria-current="page" @endif>Semua</a>
    @foreach($categories as $category)
        <a class="button {{ (int)request('category') === $category->id ? '' : 'secondary' }}" href="{{ route('menu', ['category' => $category->id]) }}"
            @if((int)request('category') === $category->id) aria-current="page" @endif>{{ $category->name }}</a>
    @endforeach
</nav>
<div class="menu-list">
    @forelse($items as $item)
        <article class="menu-row">
            <img src="{{ $item->imageUrl() }}" alt="Foto ilustrasi {{ $item->name }}" width="220" height="160">
            <div class="stack compact">
                <p class="small muted">{{ $item->category->name }}</p>
                <h2>{{ $item->name }}</h2>
                <p class="muted">{{ $item->description }}</p>
            </div>
            <strong>@rupiah($item->price)</strong>
        </article>
    @empty
        <div class="empty"><h2>Belum ada menu untuk pilihan ini.</h2><p>Coba kategori lain atau lihat semua menu.</p><a href="{{ route('menu') }}">Lihat semua menu</a></div>
    @endforelse
</div>
@endsection

