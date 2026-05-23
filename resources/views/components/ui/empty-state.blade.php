{{--
    <x-ui.empty-state /> — Centered "no data" placeholder.

    Props
    -----
    icon    (string)  Lucide icon. Default "inbox".
    title   (string)  Heading. Default "No data".
    message (string)  Body explanation.

    Slot
    ----
    default   Optional CTA (e.g. button) shown below the message.

    Example
    -------
        <x-ui.empty-state
            icon="map"
            title="No tours yet"
            message="Start by creating your first tour.">
            <x-ui.button as="a" href="{{ route('tour.create') }}" icon="plus">New tour</x-ui.button>
        </x-ui.empty-state>
--}}

@props([
    'icon' => 'inbox',
    'title' => 'No data',
    'message' => null,
])

<div {{ $attributes->class('flex flex-col items-center justify-center text-center py-12 px-6') }}>
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-4">
        <x-ui.icon :name="$icon" size="lg" />
    </div>
    <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
    @if($message)
        <p class="mt-1 text-sm text-slate-500 max-w-sm">{{ $message }}</p>
    @endif
    @if(trim($slot) !== '')
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
