{{--
    <x-ui.modal /> — Overlay dialog with focus trap, ESC-to-close, click-outside-to-close.

    Behavior is Alpine-driven (no Bootstrap modal JS). Communicate with it
    via Alpine custom events:
        Alpine.$dispatch('open-modal', 'my-modal-id')
        Alpine.$dispatch('close-modal', 'my-modal-id')
    Or attach @click="$dispatch('open-modal', 'my-modal-id')" to any element.

    Props
    -----
    id          (required string)  Unique identifier; trigger events target this.
    title       (string)            Header title.
    description (string)            Sub-text under title.
    size        (string)            "sm" 384px | "md" 480px (default) | "lg" 640px | "xl" 800px
    closeable   (bool)              Show the close (X) button. Default true.

    Slots
    -----
    default     Body content.
    footer      Right-aligned action buttons. Optional.

    Accessibility
    -------------
    * `role="dialog"` and `aria-modal="true"`.
    * `aria-labelledby` points to the header title.
    * Focus is trapped (via @alpinejs/focus) while open and returned to the
      element that opened it on close.
    * ESC closes (unless `:closeable="false"`).
    * Click on backdrop closes.

    Example
    -------
        <x-ui.button @click="$dispatch('open-modal', 'delete-confirm')">Delete</x-ui.button>

        <x-ui.modal id="delete-confirm" title="Delete tour?"
                    description="This cannot be undone.">
            <p class="text-sm text-slate-600">Tour "{{ $tour->name }}" will be removed.</p>
            <x-slot name="footer">
                <x-ui.button variant="secondary" @click="$dispatch('close-modal', 'delete-confirm')">Cancel</x-ui.button>
                <x-ui.button variant="danger" icon="trash-2">Delete</x-ui.button>
            </x-slot>
        </x-ui.modal>
--}}

@props([
    'id',
    'title' => null,
    'description' => null,
    'size' => 'md',
    'closeable' => true,
])

@php
    $sizes = [
        'sm' => 'max-w-sm',   // 384px
        'md' => 'max-w-md',   // 448px (close to spec)
        'lg' => 'max-w-2xl',  // 672px
        'xl' => 'max-w-4xl',  // 896px (used for forms with many fields)
    ];
    $widthClass = $sizes[$size] ?? $sizes['md'];
    $titleId = $id . '-title';
@endphp

<div
    x-data="{
        open: false,
        modalId: @js($id),
        openWith(detail) {
            if (detail !== this.modalId) return;
            this.open = true;
            this.$nextTick(() => {
                const focusables = this.$refs.panel.querySelectorAll('input,select,textarea,button,[tabindex]:not([tabindex=\'-1\'])');
                if (focusables.length) focusables[0].focus();
            });
        },
        close() {
            this.open = false;
        }
    }"
    x-on:open-modal.window="openWith($event.detail)"
    x-on:close-modal.window="if ($event.detail === modalId) close()"
    x-on:keydown.escape.window="open && {{ $closeable ? 'true' : 'false' }} && close()"
    x-show="open"
    x-cloak
    role="dialog"
    aria-modal="true"
    @if($title) aria-labelledby="{{ $titleId }}" @endif
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition.opacity.duration.150ms
        class="fixed inset-0 bg-slate-900/40"
        @if($closeable) x-on:click="close()" @endif
        aria-hidden="true"
    ></div>

    {{-- Panel --}}
    <div class="flex min-h-screen items-center justify-center p-4">
        <div
            x-show="open"
            x-ref="panel"
            x-trap.inert.noscroll="open"
            x-transition:enter="ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full {{ $widthClass }} bg-white rounded-md shadow-overlay border border-slate-200"
            x-on:click.stop
        >
            @if($title || $closeable)
                <header class="flex items-start gap-4 px-6 py-4 border-b border-slate-200">
                    <div class="flex-1 min-w-0">
                        @if($title)
                            <h3 id="{{ $titleId }}" class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                        @endif
                        @if($description)
                            <p class="text-sm text-slate-500 mt-0.5">{{ $description }}</p>
                        @endif
                    </div>
                    @if($closeable)
                        <button
                            type="button"
                            class="rounded p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-primary-600"
                            x-on:click="close()"
                            aria-label="Close"
                        >
                            <x-ui.icon name="x" />
                        </button>
                    @endif
                </header>
            @endif

            <div class="px-6 py-5">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <footer class="flex items-center justify-end gap-2 px-6 py-3 border-t border-slate-200 bg-slate-50/40 rounded-b-md">
                    {{ $footer }}
                </footer>
            @endif
        </div>
    </div>
</div>
