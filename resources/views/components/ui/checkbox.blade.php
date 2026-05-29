{{--
    <x-ui.checkbox /> — Single checkbox + label.

    Props
    -----
    name      (string)
    id        (string)
    value     (mixed)        Posted value when checked. Defaults to "1".
    checked   (bool)         Pre-checked.
    label     (string)       Label rendered to the right of the box.
    description (string)     Optional helper text below the label.
    disabled  (bool)
    required  (bool)

    Example
    -------
        <x-ui.checkbox name="agree" label="I agree to the terms" :checked="old('agree')" />
--}}

@props([
    'name' => null,
    'id' => null,
    'value' => '1',
    'checked' => false,
    'label' => null,
    'description' => null,
    'disabled' => false,
    'required' => false,
])

@php
    $fieldId = $id ?? $name;
@endphp

<label for="{{ $fieldId }}" class="flex items-start gap-2 cursor-pointer select-none {{ $disabled ? 'opacity-60 cursor-not-allowed' : '' }}">
    <input
        type="checkbox"
        id="{{ $fieldId }}"
        name="{{ $name }}"
        value="{{ $value }}"
        @if($checked) checked @endif
        @if($disabled) disabled @endif
        @if($required) required @endif
        {{ $attributes->class('mt-0.5 h-4 w-4 rounded border-slate-300 text-primary-600 shadow-subtle focus:ring-primary-600/30 focus:ring-2 focus:ring-offset-0 disabled:cursor-not-allowed') }}
    />
    <span class="text-sm leading-5">
        @if($label)<span class="text-slate-900">{{ $label }}</span>@endif
        @if($description)<span class="block text-xs text-slate-500 mt-0.5">{{ $description }}</span>@endif
        {{ $slot }}
    </span>
</label>
