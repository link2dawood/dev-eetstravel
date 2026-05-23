{{--
    <x-ui.toast /> — Transient corner-anchored notification.

    Architecture
    ------------
    A single Alpine store (`Alpine.store('toast')`) holds the queue. The view
    renders the container; any code can push a toast via:

        window.toast('Saved successfully', 'success')      // shorthand helper
        Alpine.store('toast').push({ message: '…', variant: 'success', timeout: 4000 })

    Also auto-reads Laravel flash messages on page load (success/error/warning).
    The helper is registered in resources/js/app.js (Alpine plugin).

    Drop one <x-ui.toast /> at the bottom of the staff layout. Don't repeat
    per page.

    Props
    -----
    position (string)   "top-right" (default) | "bottom-right" | "top-center"

    Example
    -------
        {{-- in tabler-app layout (once) --}}
        <x-ui.toast />

        {{-- anywhere in JS --}}
        toast('Tour deleted', 'success')

        {{-- after a Laravel redirect: --}}
        return redirect()->route('tour.index')->with('toast', ['variant' => 'success', 'message' => 'Saved']);
--}}

@props(['position' => 'top-right'])

@php
    $posMap = [
        'top-right'     => 'top-4 right-4 items-end',
        'top-center'    => 'top-4 left-1/2 -translate-x-1/2 items-center',
        'bottom-right'  => 'bottom-4 right-4 items-end',
    ];
    $posClass = $posMap[$position] ?? $posMap['top-right'];

    // Pull any Laravel flash data into the initial queue.
    $flashToasts = [];
    if (session()->has('toast')) {
        $flashToasts[] = session('toast');
    }
    foreach (['success', 'error', 'warning', 'info'] as $variant) {
        if (session()->has($variant)) {
            $flashToasts[] = ['message' => session($variant), 'variant' => $variant === 'error' ? 'danger' : $variant];
        }
    }
@endphp

<div
    x-data="{
        items: @js($flashToasts),
        nextId: 1,
        push(opts) {
            const item = Object.assign({ id: this.nextId++, variant: 'neutral', timeout: 4000 }, opts);
            this.items.push(item);
            if (item.timeout > 0) setTimeout(() => this.dismiss(item.id), item.timeout);
        },
        dismiss(id) {
            this.items = this.items.filter(i => i.id !== id);
        },
        init() {
            // Expose a global shorthand once Alpine has started.
            window.toast = (message, variant = 'neutral', timeout = 4000) => this.push({ message, variant, timeout });
            // Listen for custom events from anywhere on the page.
            window.addEventListener('toast', (e) => this.push(e.detail || {}));
        }
    }"
    class="fixed z-[60] flex flex-col gap-2 pointer-events-none {{ $posClass }}"
    aria-live="polite"
    aria-atomic="false"
>
    <template x-for="item in items" :key="item.id">
        <div
            x-transition:enter="transform ease-out duration-200 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto min-w-72 max-w-md rounded-md border bg-white shadow-overlay px-4 py-3 flex items-start gap-3"
            :class="{
                'border-success-600 ring-1 ring-success-600/10': item.variant === 'success',
                'border-warning-600 ring-1 ring-warning-600/10': item.variant === 'warning',
                'border-danger-600  ring-1 ring-danger-600/10':  item.variant === 'danger',
                'border-info-600    ring-1 ring-info-600/10':    item.variant === 'info',
                'border-slate-200':                              !['success','warning','danger','info'].includes(item.variant),
            }"
            role="status"
        >
            <div class="shrink-0 mt-0.5">
                <template x-if="item.variant === 'success'"><x-ui.icon name="check-circle-2" class="text-success-600" /></template>
                <template x-if="item.variant === 'warning'"><x-ui.icon name="alert-triangle" class="text-warning-600" /></template>
                <template x-if="item.variant === 'danger'"><x-ui.icon name="alert-circle" class="text-danger-600" /></template>
                <template x-if="item.variant === 'info'"><x-ui.icon name="info" class="text-info-600" /></template>
                <template x-if="!['success','warning','danger','info'].includes(item.variant)"><x-ui.icon name="bell" class="text-slate-500" /></template>
            </div>
            <div class="flex-1 text-sm text-slate-700" x-text="item.message"></div>
            <button
                type="button"
                class="-mr-1 rounded p-1 text-slate-400 hover:bg-slate-100"
                x-on:click="dismiss(item.id)"
                aria-label="Dismiss"
            >
                <x-ui.icon name="x" size="xs" />
            </button>
        </div>
    </template>
</div>
