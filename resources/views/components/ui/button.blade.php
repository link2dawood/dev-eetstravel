{{--
    <x-ui.button /> — Primary interactive control.

    Props
    -----
    variant (string)   "primary" (default, teal accent) | "secondary" (neutral border)
                       | "ghost" (no border, hover bg) | "danger" (red) | "link" (text-only)
    size    (string)   "sm" 28px | "md" 36px (default)
    type    (string)   HTML button type. Default "button" — explicit so a
                       widget inside a <form> never accidentally submits.
    as      (string)   "button" (default) | "a" — render as anchor when href is set.
    href    (string)   If set, renders as <a>. Implies as="a".
    icon    (string)   Optional Lucide icon name to render at the start.
    iconEnd (string)   Optional Lucide icon name to render at the end.
    loading (bool)     Shows a spinner and disables interaction.
    disabled (bool)    Standard disabled state.
    block   (bool)     Full-width.

    Slots
    -----
    default            Label text.

    Behavior
    --------
    Focus ring uses tailwind.css :focus-visible (no ring on mouse click).
    Loading state swaps the start-icon for a spinner and sets aria-busy.
    Disabled is enforced server-side via attribute and visually via the
    `disabled:` Tailwind variants.

    Example
    -------
        <x-ui.button variant="primary" icon="save">Save changes</x-ui.button>
        <x-ui.button variant="danger" icon="trash-2" size="sm">Delete</x-ui.button>
        <x-ui.button as="a" href="{{ route('tour.create') }}" icon="plus">New tour</x-ui.button>
--}}

@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'as' => null,
    'href' => null,
    'icon' => null,
    'iconEnd' => null,
    'loading' => false,
    'disabled' => false,
    'block' => false,
])

@php
    $tag = $as ?? ($href ? 'a' : 'button');

    // Per-variant color tokens. No hex literals anywhere.
    $variants = [
        'primary'   => 'bg-primary-600 text-white border border-transparent hover:bg-primary-700 active:bg-primary-700 disabled:bg-primary-600/50',
        'secondary' => 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 active:bg-slate-100 disabled:text-slate-400 disabled:bg-white',
        'ghost'     => 'bg-transparent text-slate-700 border border-transparent hover:bg-slate-100 active:bg-slate-200 disabled:text-slate-400',
        'danger'    => 'bg-danger-600 text-white border border-transparent hover:bg-danger-700 active:bg-danger-700 disabled:bg-danger-600/50',
        'link'      => 'bg-transparent text-primary-600 border border-transparent hover:text-primary-700 hover:underline disabled:text-slate-400 underline-offset-2',
    ];

    // Sizes: defines height, padding, text size, icon gap.
    $sizes = [
        'sm' => 'h-7 px-2.5 text-xs gap-1.5',
        'md' => 'h-9 px-3 text-sm gap-2',
    ];

    $base = 'inline-flex items-center justify-center font-medium rounded transition-colors select-none whitespace-nowrap disabled:cursor-not-allowed';

    $classes = trim(
        $base . ' '
        . ($variants[$variant] ?? $variants['primary']) . ' '
        . ($sizes[$size] ?? $sizes['md'])
        . ($block ? ' w-full' : '')
    );

    $isInteractive = !$loading && !$disabled;
@endphp

<{{ $tag }}
    {{ $attributes
        ->class($classes)
        ->merge($tag === 'a'
            ? ($href ? ['href' => $href] : [])
            : ['type' => $type]
        ) }}
    @if(!$isInteractive && $tag === 'button') disabled @endif
    @if($loading) aria-busy="true" @endif
    @if($disabled) aria-disabled="true" @endif
>
    @if($loading)
        {{-- Spinner replaces the start icon while loading. --}}
        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
    @elseif($icon)
        <x-ui.icon :name="$icon" />
    @endif

    {{ $slot }}

    @if($iconEnd && !$loading)
        <x-ui.icon :name="$iconEnd" />
    @endif
</{{ $tag }}>
