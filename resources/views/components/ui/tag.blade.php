{{--
    <x-ui.tag /> — Like Badge but interactive: optionally dismissable.

    Props
    -----
    variant (string)     Same palette as Badge. Default "neutral".
    icon    (string)     Leading Lucide icon.
    dismissUrl (string)  If set, renders an "x" button that links to this URL
                         (use for filter-chip removal). Use GET-safe URLs only.
    dismissOn  (string)  Alpine event to fire when "x" is clicked instead of
                         navigating. Mutually exclusive with dismissUrl.

    Example
    -------
        <x-ui.tag variant="primary" icon="filter" dismissUrl="{{ request()->fullUrlWithQuery(['status' => null]) }}">
            Status: Active
        </x-ui.tag>
--}}

@props([
    'variant' => 'neutral',
    'icon' => null,
    'dismissUrl' => null,
    'dismissOn' => null,
])

@php
    $variants = [
        'neutral'  => 'bg-slate-100 text-slate-700 hover:bg-slate-200',
        'primary'  => 'bg-primary-50 text-primary-700 hover:bg-primary-100',
        'success'  => 'bg-success-50 text-success-700 hover:bg-success-100',
        'warning'  => 'bg-warning-50 text-warning-700 hover:bg-warning-100',
        'danger'   => 'bg-danger-50 text-danger-700 hover:bg-danger-100',
        'info'     => 'bg-info-50 text-info-700 hover:bg-info-100',
    ];
    $v = $variants[$variant] ?? $variants['neutral'];
    $dismissable = $dismissUrl || $dismissOn;
@endphp

<span {{ $attributes->class('inline-flex items-center gap-1 rounded px-2 py-0.5 text-xs font-medium transition-colors ' . $v) }}>
    @if($icon)<x-ui.icon :name="$icon" size="xs" />@endif
    <span>{{ $slot }}</span>
    @if($dismissable)
        @if($dismissUrl)
            <a href="{{ $dismissUrl }}" class="ml-1 -mr-0.5 rounded hover:bg-black/10 p-0.5" aria-label="Remove filter">
                <x-ui.icon name="x" size="xs" />
            </a>
        @else
            <button
                type="button"
                class="ml-1 -mr-0.5 rounded hover:bg-black/10 p-0.5"
                x-on:click="$dispatch('{{ $dismissOn }}')"
                aria-label="Remove"
            >
                <x-ui.icon name="x" size="xs" />
            </button>
        @endif
    @endif
</span>
