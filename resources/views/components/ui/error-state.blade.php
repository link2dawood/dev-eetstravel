{{--
    <x-ui.error-state /> — "Something went wrong" panel with optional retry.

    Use this anywhere an async operation can fail (a list endpoint returned
    500, a fetch threw, a slow query timed out, etc.).

    Props
    -----
    title    (string)         Heading. Default "Something went wrong".
    message  (string)         The actual error string. Sanitize before passing.
    retryUrl (string|null)    If set, renders a "Try again" link button.

    Example
    -------
        <x-ui.error-state
            title="Couldn't load tours"
            message="The server returned an error. Refresh to try again."
            :retryUrl="request()->fullUrl()"
        />
--}}

@props([
    'title' => 'Something went wrong',
    'message' => null,
    'retryUrl' => null,
])

<div {{ $attributes->class('flex flex-col items-center justify-center text-center py-12 px-6') }} role="alert">
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-danger-50 text-danger-600 mb-4">
        <x-ui.icon name="alert-triangle" size="lg" />
    </div>
    <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
    @if($message)
        <p class="mt-1 text-sm text-slate-500 max-w-md">{{ $message }}</p>
    @endif
    @if($retryUrl)
        <div class="mt-4">
            <x-ui.button as="a" :href="$retryUrl" variant="secondary" icon="refresh-cw">Try again</x-ui.button>
        </div>
    @endif
</div>
