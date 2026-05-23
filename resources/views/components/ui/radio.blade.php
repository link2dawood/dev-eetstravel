{{--
    <x-ui.radio /> — Single radio button + label.

    Props are the same as <x-ui.checkbox /> except `checked` is the equality
    test against the group's selected value:
        :checked="$current === 'option_a'"

    Multiple radios with the same `name` form a group.

    Example
    -------
        <fieldset>
            <legend class="text-sm font-medium text-slate-700">Status</legend>
            <div class="space-y-1 mt-1">
                <x-ui.radio name="status" value="draft"  label="Draft"  :checked="$current === 'draft'" />
                <x-ui.radio name="status" value="active" label="Active" :checked="$current === 'active'" />
            </div>
        </fieldset>
--}}

@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'checked' => false,
    'label' => null,
    'description' => null,
    'disabled' => false,
])

@php
    $fieldId = $id ?? ($name . '-' . preg_replace('/[^a-z0-9]/i', '', (string) $value));
@endphp

<label for="{{ $fieldId }}" class="flex items-start gap-2 cursor-pointer select-none {{ $disabled ? 'opacity-60 cursor-not-allowed' : '' }}">
    <input
        type="radio"
        id="{{ $fieldId }}"
        name="{{ $name }}"
        value="{{ $value }}"
        @if($checked) checked @endif
        @if($disabled) disabled @endif
        {{ $attributes->class('mt-0.5 h-4 w-4 border-slate-300 text-primary-600 shadow-subtle focus:ring-primary-600/30 focus:ring-2 focus:ring-offset-0 disabled:cursor-not-allowed') }}
    />
    <span class="text-sm leading-5">
        @if($label)<span class="text-slate-900">{{ $label }}</span>@endif
        @if($description)<span class="block text-xs text-slate-500 mt-0.5">{{ $description }}</span>@endif
        {{ $slot }}
    </span>
</label>
