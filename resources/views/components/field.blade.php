@props(['name', 'label', 'type' => 'text', 'value' => '', 'hint' => '', 'required' => false])
@php
    $fieldValue = old($name, $value);
    $fieldValue = is_scalar($fieldValue) ? $fieldValue : '';
@endphp
<div class="field">
    <label for="{{ $name }}">{{ $label }}</label>
    @if($type === 'select')
        <div class="select-wrap">
            <select id="{{ $name }}" name="{{ $name }}" {{ $attributes }} @required($required)
                @error($name) aria-invalid="true" @enderror aria-describedby="{{ $name }}-help">
                {{ $slot }}
            </select>
            <img src="{{ asset('icons/select-indicator.svg') }}" alt="" aria-hidden="true" width="16" height="16">
        </div>
    @elseif($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" {{ $attributes }} @required($required)
            @error($name) aria-invalid="true" @enderror aria-describedby="{{ $name }}-help">{{ $fieldValue }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" {{ $attributes }} @required($required)
            @if(!in_array($type, ['password', 'file'])) value="{{ $fieldValue }}" @endif
            @error($name) aria-invalid="true" @enderror aria-describedby="{{ $name }}-help">
    @endif
    <div id="{{ $name }}-help" class="small">
        @error($name)<p class="field-error">{{ $message }}</p>@enderror
        @if($hint)<p class="muted">{{ $hint }}</p>@endif
    </div>
</div>
