@extends('layouts.admin')
@section('title', $table->exists ? 'Edit meja' : 'Tambah meja')
@section('breadcrumb', 'Meja')
@section('content')
<x-intro :title="$table->exists ? 'Edit meja' : 'Tambah meja'" :description="$table->exists ? 'Perubahan berlaku setelah disimpan.' : 'Isi data untuk menambah meja.'" />
<div class="panel master-panel">
    @if($table->exists && $table->reservations()->exists())<div class="notice neutral"><strong>Meja memiliki riwayat reservasi.</strong><p>Kode dan kapasitas tidak dapat diubah. Meja dapat dinonaktifkan setelah tidak ada reservasi aktif.</p></div>@endif
    <form class="stack" action="{{ $table->exists ? route('admin.tables.update', $table) : route('admin.tables.store') }}" method="post">
        @csrf
        @if($table->exists) @method('patch') @endif
        <x-field name="code" label="Kode meja" :value="$table->code" maxlength="20" pattern="[a-z0-9_-]+" hint="Kode unik menggunakan huruf kecil, angka, garis bawah, atau tanda hubung." :required="true" />
        <x-field name="capacity" label="Kapasitas" type="number" :value="$table->capacity ?? 2" min="1" max="6" step="1" hint="1 sampai 6 orang." :required="true" />
        @include('partials.active-field', ['value' => $table->is_active ?? true])
        <div class="actions"><button class="button" data-progress="Menyimpan…">Simpan meja</button><a class="button secondary" href="{{ route('admin.tables.index') }}">Kembali</a></div>
    </form>
    @if($table->exists)<x-confirm-form :action="route('admin.tables.destroy', $table)" method="delete" label="Hapus meja" :message="'Hapus meja '.$table->code.'? Meja dengan riwayat reservasi tidak dapat dihapus.'" />@endif
</div>
@endsection

