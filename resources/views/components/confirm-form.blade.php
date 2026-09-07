@props(['action', 'method' => 'post', 'label', 'message', 'title' => 'Konfirmasi tindakan', 'confirmLabel' => 'Lanjutkan', 'buttonClass' => 'button text danger', 'name' => null, 'value' => null])
<form action="{{ $action }}" method="post" data-confirm="{{ $message }}" data-confirm-heading="{{ $title }}" data-confirm-label="{{ $confirmLabel }}">
    @csrf
    @if($method !== 'post') @method($method) @endif
    @if($name)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endif
    <button class="{{ $buttonClass }}" type="submit" data-progress="Memproses…">{{ $label }}</button>
</form>
