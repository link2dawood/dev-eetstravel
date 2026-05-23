{{--
    <x-ui.dropdown-item /> — Single menu item inside <x-ui.dropdown />.

    Props
    -----
    href   (string)   Link target. Renders as <a>. Omit for button.
    icon   (string)   Lucide icon name.
    danger (bool)     Red text for destructive actions.
    disabled (bool)
--}}

@props([
    'href' => null,
    'icon' => null,
    'danger' => false,
    'disabled' => false,
])

@php
    $tag = $href ? 'a' : 'button';
    $base = 'flex w-full items-center gap-2 px-3 py-1.5 text-sm rounded-sm transition-colors';
    $color = $danger
        ? 'text-danger-700 hover:bg-danger-50'
        : 'text-slate-700 hover:bg-slate-100';
    $disabledClass = $disabled ? 'opacity-50 cursor-not-allowed' : '';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @else type="button" @endif
    @if($disabled) disabled aria-disabled="true" @endif
    role="menuitem"
    {{ $attributes->class(trim($base . ' ' . $color . ' ' . $disabledClass)) }}
>
    @if($icon)<x-ui.icon :name="$icon" />@endif
    <span class="flex-1 text-left">{{ $slot }}</span>
</{{ $tag }}>
