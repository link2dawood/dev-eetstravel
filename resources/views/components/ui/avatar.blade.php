{{--
    <x-ui.avatar /> — Circular user avatar (image or initials).

    Props
    -----
    src     (string)        Image URL. If absent, renders initials block.
    alt     (string)        Image alt text.
    name    (string)        Person's name (used to derive initials + bg color).
    size    (string)        "xs" 20px | "sm" 24px | "md" 32px (default) | "lg" 40px
    shape   (string)        "circle" (default) | "square"

    Example
    -------
        <x-ui.avatar :name="$user->name" :src="$user->avatar_url" />
        <x-ui.avatar name="Dawood Zafar" size="lg" />
--}}

@props([
    'src' => null,
    'alt' => null,
    'name' => null,
    'size' => 'md',
    'shape' => 'circle',
])

@php
    $sizes = [
        'xs' => 'h-5 w-5 text-[10px]',
        'sm' => 'h-6 w-6 text-xs',
        'md' => 'h-8 w-8 text-sm',
        'lg' => 'h-10 w-10 text-base',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $shapeClass = $shape === 'square' ? 'rounded' : 'rounded-full';

    $initials = '';
    if ($name) {
        $words = preg_split('/\s+/', trim($name));
        $first = $words[0] ?? '';
        $last  = $words[count($words) - 1] ?? '';
        $initials = strtoupper(mb_substr($first, 0, 1) . (count($words) > 1 ? mb_substr($last, 0, 1) : ''));
    }

    // Deterministic background color from name. Six options.
    $palette = ['bg-primary-600', 'bg-info-600', 'bg-success-600', 'bg-warning-600', 'bg-danger-600', 'bg-slate-600'];
    $bg = $name ? $palette[abs(crc32($name)) % count($palette)] : 'bg-slate-400';
@endphp

@if($src)
    <img
        src="{{ $src }}"
        alt="{{ $alt ?? $name ?? '' }}"
        {{ $attributes->class('inline-block object-cover ' . $sizeClass . ' ' . $shapeClass) }}
    />
@else
    <span
        {{ $attributes->class('inline-flex items-center justify-center font-medium text-white select-none ' . $sizeClass . ' ' . $shapeClass . ' ' . $bg) }}
        aria-label="{{ $alt ?? $name ?? '' }}"
    >{{ $initials ?: '?' }}</span>
@endif
