{{--
    <x-ui.card /> — Bordered panel with optional header, body, footer.

    Props
    -----
    title      (string)        Header title. Omits the header block if null.
    description (string)       Sub-text under the title.
    padding    (string)        "none" | "sm" (p-4) | "md" (p-6, default) | "lg" (p-8).
                              Applied to body and footer.

    Slots
    -----
    default    Body content. Wrapped in `card-body` padding.
    actions    Right-aligned content in the header (action buttons, dropdown).
    footer     Footer block, separated by a top border.

    Example
    -------
        <x-ui.card title="Tour details">
            <x-slot name="actions">
                <x-ui.button variant="secondary" size="sm" icon="edit">Edit</x-ui.button>
            </x-slot>

            <dl class="grid grid-cols-2 gap-4">…</dl>

            <x-slot name="footer">
                <x-ui.button variant="primary">Save</x-ui.button>
            </x-slot>
        </x-ui.card>
--}}

@props([
    'title' => null,
    'description' => null,
    'padding' => 'md',
])

@php
    $padMap = [
        'none' => '',
        'sm' => 'p-4',
        'md' => 'p-6',
        'lg' => 'p-8',
    ];
    $bodyPad = $padMap[$padding] ?? $padMap['md'];
@endphp

<div {{ $attributes->class('bg-white border border-slate-200 rounded shadow-card') }}>
    @if($title || isset($actions))
        <header class="flex items-start gap-4 px-6 py-4 border-b border-slate-200">
            <div class="flex-1 min-w-0">
                @if($title)
                    <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
                @endif
                @if($description)
                    <p class="text-xs text-slate-500 mt-0.5">{{ $description }}</p>
                @endif
            </div>
            @if(isset($actions))
                <div class="flex items-center gap-2 shrink-0">{{ $actions }}</div>
            @endif
        </header>
    @endif

    <div class="{{ $bodyPad }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <footer class="flex items-center justify-end gap-2 px-6 py-3 border-t border-slate-200 bg-slate-50/40">
            {{ $footer }}
        </footer>
    @endif
</div>
