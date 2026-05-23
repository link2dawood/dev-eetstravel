{{--
    <x-ui.badge /> — Small inline status pill.

    Props
    -----
    variant (string)   "neutral" (default) | "success" | "warning" | "danger" | "info" | "primary"
    icon    (string)   Optional Lucide icon at the start.
    dot     (bool)     Render a leading colored dot instead of an icon.

    Example
    -------
        <x-ui.badge variant="success" dot>Active</x-ui.badge>
        <x-ui.badge variant="danger" icon="alert-circle">Overdue</x-ui.badge>
--}}

@props([
    'variant' => 'neutral',
    'icon' => null,
    'dot' => false,
])

@php
    $variants = [
        'neutral'  => ['bg' => 'bg-slate-100',   'text' => 'text-slate-700',  'dot' => 'bg-slate-400'],
        'primary'  => ['bg' => 'bg-primary-50',  'text' => 'text-primary-700','dot' => 'bg-primary-600'],
        'success'  => ['bg' => 'bg-success-50',  'text' => 'text-success-700','dot' => 'bg-success-600'],
        'warning'  => ['bg' => 'bg-warning-50',  'text' => 'text-warning-700','dot' => 'bg-warning-600'],
        'danger'   => ['bg' => 'bg-danger-50',   'text' => 'text-danger-700', 'dot' => 'bg-danger-600'],
        'info'     => ['bg' => 'bg-info-50',     'text' => 'text-info-700',   'dot' => 'bg-info-600'],
    ];
    $v = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->class('inline-flex items-center gap-1 rounded-sm px-2 py-0.5 text-xs font-medium ' . $v['bg'] . ' ' . $v['text']) }}>
    @if($dot)
        <span class="h-1.5 w-1.5 rounded-full {{ $v['dot'] }}" aria-hidden="true"></span>
    @elseif($icon)
        <x-ui.icon :name="$icon" size="xs" />
    @endif
    {{ $slot }}
</span>
