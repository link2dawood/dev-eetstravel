{{--
    <x-ui.select /> — Native <select> styled to match Input.

    Use this for short, fixed lists (≤ 20 options). For searchable / dynamic
    lists use <x-ui.combobox /> (Select2 replacement) once it exists.

    Props
    -----
    name       (string)
    id         (string)        Override auto id (defaults to name).
    value      (mixed)         Selected option value.
    options    (array)         Map of value => label. Optional — alternative
                               to passing <option> children in the slot.
    placeholder (string)       Adds a disabled empty first option.
    size       (string)        "sm" | "md" (default)
    invalid    (bool)
    disabled   (bool)
    required   (bool)

    Example
    -------
        <x-ui.form-field label="Status" :error="$errors->first('status')">
            <x-ui.select name="status" :options="['draft' => 'Draft', 'active' => 'Active']"
                         :value="old('status', $tour->status)" placeholder="Pick a status" />
        </x-ui.form-field>
--}}

@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'options' => null,
    'placeholder' => null,
    'size' => 'md',
    'invalid' => false,
    'disabled' => false,
    'required' => false,
])

@php
    $sizes = [
        'sm' => 'h-7 text-xs pr-8',
        'md' => 'h-9 text-sm pr-9',
    ];

    $stateClasses = $invalid
        ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600/30'
        : 'border-slate-300 focus:border-primary-600 focus:ring-primary-600/30';

    // bg-[url] embeds the chevron SVG inline so we don't depend on Lucide
    // for a non-interactive ornament. Could be swapped to <x-ui.icon /> if
    // we want consistency, but native <select> can't have JSX children.
    $classes = trim(
        'block w-full rounded border bg-white text-slate-900 pl-3 ' .
        'shadow-subtle appearance-none focus:outline-none focus:ring-2 ' .
        'disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed ' .
        ($sizes[$size] ?? $sizes['md']) . ' ' . $stateClasses
    );

    $fieldId = $id ?? $name;
@endphp

<div class="relative">
    <select
        {{ $attributes->class($classes)->merge([
            'name' => $name,
            'id'   => $fieldId,
        ]) }}
        @if($disabled) disabled @endif
        @if($required) required aria-required="true" @endif
        @if($invalid) aria-invalid="true" @endif
    >
        @if($placeholder)
            <option value="" disabled @if(is_null($value) || $value === '') selected @endif>{{ $placeholder }}</option>
        @endif

        @if(is_array($options))
            @foreach($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @if((string) $optValue === (string) $value) selected @endif>{{ $optLabel }}</option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>

    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
        <x-ui.icon name="chevron-down" />
    </span>
</div>
