{{--
    <x-ui.textarea /> — Multi-line text input.

    Props mirror <x-ui.input /> except for `rows`. Type is implied.

    Example
    -------
        <x-ui.form-field label="Notes">
            <x-ui.textarea name="notes" rows="4" :value="old('notes')" />
        </x-ui.form-field>
--}}

@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => null,
    'rows' => 4,
    'invalid' => false,
    'disabled' => false,
    'required' => false,
])

@php
    $stateClasses = $invalid
        ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600/30'
        : 'border-slate-300 focus:border-primary-600 focus:ring-primary-600/30';

    $classes = trim(
        'block w-full rounded border bg-white text-sm text-slate-900 placeholder:text-slate-400 ' .
        'shadow-subtle px-3 py-2 focus:outline-none focus:ring-2 ' .
        'disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed ' . $stateClasses
    );

    $fieldId = $id ?? $name;
@endphp

<textarea
    {{ $attributes->class($classes)->merge([
        'name' => $name,
        'id'   => $fieldId,
        'rows' => $rows,
        'placeholder' => $placeholder,
    ]) }}
    @if($disabled) disabled @endif
    @if($required) required aria-required="true" @endif
    @if($invalid) aria-invalid="true" @endif
>{{ $value }}</textarea>
