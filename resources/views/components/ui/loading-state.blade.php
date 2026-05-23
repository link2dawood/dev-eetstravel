{{--
    <x-ui.loading-state /> — Centered spinner + label.

    Props
    -----
    message  (string)   Label text. Default "Loading…"
    inline   (bool)     If true, renders without min-height padding (use inside
                        existing constrained containers).

    Example
    -------
        <x-ui.loading-state message="Fetching tours…" />
--}}

@props([
    'message' => 'Loading…',
    'inline' => false,
])

<div {{ $attributes->class('flex items-center justify-center gap-3 text-sm text-slate-500 ' . ($inline ? 'py-4' : 'py-12')) }} role="status">
    <svg class="animate-spin h-5 w-5 text-primary-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
    </svg>
    <span>{{ $message }}</span>
</div>
