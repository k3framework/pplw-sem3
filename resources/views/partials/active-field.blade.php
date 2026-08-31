@php
    $current = old('is_active', (int)$value);
    $current = is_scalar($current) ? (string)$current : '';
@endphp
<x-field name="is_active" label="Status" type="select" hint="Nonaktif menyembunyikan data dari pelanggan." :required="true">
    <option value="1" @selected($current === '1')>Aktif</option>
    <option value="0" @selected($current === '0')>Nonaktif</option>
</x-field>
