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
    write <x-ui.icon name="bar" /> — if we ever swap icon libraries, only
    this file changes.

    Defensive fallback: if a Lucide icon with that name doesn't exist
    (typo, Tabler/Heroicon name accidentally used, renamed in Lucide,
    etc.) we render a small placeholder SVG instead of throwing
    "Unable to locate a class or view for component" and 500'ing the
    whole page.

    Sizes are constant per app-wide convention. Don't pass arbitrary
    width/height; pick a size token. Aria-hidden by default — wrap with
    a <span class="sr-only"> if the icon is the only content of an
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

    // Resolve via the on-disk Lucide SVG catalog. Cheap file_exists check
    // so a missing icon doesn't blow up Blade's component compiler.
    $iconExists = file_exists(base_path("vendor/mallardduck/blade-lucide-icons/resources/svg/{$name}.svg"));
@endphp

@if($iconExists)
    <x-dynamic-component
        :component="'lucide-' . $name"
        :attributes="new \Illuminate\View\ComponentAttributeBag($iconAttrs)"
    />
@else
    {{-- Fallback: dashed circle. Logs the missing name so a future cleanup
         pass can find every stale reference. --}}
    @php
        \Log::warning("Unknown Lucide icon: {$name}");
    @endphp
    <svg xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round"
         {!! (new \Illuminate\View\ComponentAttributeBag($iconAttrs))->toHtml() !!}>
        <circle cx="12" cy="12" r="10" stroke-dasharray="2 2" />
        <line x1="12" y1="8" x2="12" y2="12" />
        <line x1="12" y1="16" x2="12.01" y2="16" />
    </svg>
@endif
