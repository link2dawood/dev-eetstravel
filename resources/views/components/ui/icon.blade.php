{{--
    <x-ui.icon /> — Lucide-backed icon.

    Props
    -----
    name (required string)   Lucide icon name, e.g. "edit", "trash-2", "arrow-left".
                             Full catalog: https://lucide.dev/icons/
    size (string)            "xs" 12px | "sm" 16px (default) | "md" 20px | "lg" 24px
    class (string)           Extra utility classes appended to the SVG.
    stroke-width (string)    Lucide stroke-width attr override (default 2).

    Behavior
    --------
    Renders a Lucide SVG via blade-ui-kit/blade-icons. SVGs ship as Blade
    components named <x-lucide-{name} />. We thin-wrap so callers always
    write <x-ui.icon name="foo" /> — if we ever swap icon libraries, only
    this file changes.

    Sizes are constant per app-wide convention. Don't pass arbitrary
    width/height; pick a size token. Aria-hidden by default — wrap with a
    <span class="sr-only"> if the icon is the only content of an
    interactive element.

    Example
    -------
        <x-ui.icon name="edit" />
        <x-ui.icon name="trash-2" size="md" class="text-danger-600" />
--}}

@props([
    'name',
    'size' => 'sm',
    'strokeWidth' => null,
])

@php
    $sizeMap = [
        'xs' => 'h-3 w-3',     // 12 px
        'sm' => 'h-4 w-4',     // 16 px — default
        'md' => 'h-5 w-5',     // 20 px
        'lg' => 'h-6 w-6',     // 24 px
    ];
    $sizeClass = $sizeMap[$size] ?? $sizeMap['sm'];

    $iconAttrs = ['class' => trim($sizeClass . ' ' . ($attributes->get('class') ?? '')), 'aria-hidden' => 'true'];
    if (!is_null($strokeWidth)) {
        $iconAttrs['stroke-width'] = $strokeWidth;
    }
@endphp

<x-dynamic-component
    :component="'lucide-' . $name"
    :attributes="new \Illuminate\View\ComponentAttributeBag($iconAttrs)"
/>
