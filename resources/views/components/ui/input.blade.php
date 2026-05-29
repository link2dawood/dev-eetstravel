{{--
    <x-ui.input /> — Text input.

    Props
    -----
    name      (string)        Form field name. Also default `id`.
    id        (string)        Override the auto-generated id.
    type      (string)        HTML input type. Default "text".
    value     (mixed)         Initial value (use `old($name, $value)` upstream).
    placeholder (string)
    size      (string)        "sm" 28px | "md" 36px (default)
    invalid   (bool)          Render red border + danger ring. Pair with FormField error.
    leadingIcon  (string)     Lucide icon name shown inside the start of the input.
    trailingIcon (string)     Lucide icon name shown inside the end of the input.
    disabled  (bool)
    required  (bool)

    Behavior
    --------
    Native <input> styled via Tailwind only. No JS. Use inside <x-ui.form-field>
    when you need a label/error/hint wrapper.

    Example
    -------
        <x-ui.form-field label="Email" :error="$errors->first('email')">
            <x-ui.input name="email" type="email" :value="old('email')" required />
        </x-ui.form-field>
--}}

@props([
    'name' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'size' => 'md',
    'invalid' => false,
    'leadingIcon' => null,
    'trailingIcon' => null,
    'disabled' => false,
    'required' => false,
])

@php
    $sizes = [
        'sm' => 'h-7 text-xs',
        'md' => 'h-9 text-sm',
    ];

    $padLeft  = $leadingIcon  ? 'pl-9' : 'pl-3';
    $padRight = $trailingIcon ? 'pr-9' : 'pr-3';

    $stateClasses = $invalid
        ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600/30'
        : 'border-slate-300 focus:border-primary-600 focus:ring-primary-600/30';

    $classes = trim(
        'block w-full rounded border bg-white text-slate-900 placeholder:text-slate-400 ' .
        'shadow-subtle focus:outline-none focus:ring-2 ' .
        'disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed ' .
        ($sizes[$size] ?? $sizes['md']) . ' ' . $padLeft . ' ' . $padRight . ' ' . $stateClasses
    );

    $fieldId = $id ?? $name;
@endphp

<div class="relative">
    @if($leadingIcon)
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <x-ui.icon :name="$leadingIcon" />
        </span>
    @endif

    <input
        {{ $attributes->class($classes)->merge([
            'name' => $name,
            'id'   => $fieldId,
            'type' => $type,
            'value' => $value,
            'placeholder' => $placeholder,
        ]) }}
        @if($disabled) disabled @endif
        @if($required) required aria-required="true" @endif
        @if($invalid) aria-invalid="true" @endif
    />

    @if($trailingIcon)
        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
            <x-ui.icon :name="$trailingIcon" />
        </span>
    @endif
</div>
