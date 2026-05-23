{{--
    <x-ui.dropdown /> — Click-to-open menu, keyboard navigable.

    Props
    -----
    align    (string)   "left" (default) | "right" — menu alignment relative to trigger.
    width    (string)   Tailwind width class. Default "w-56".

    Slots
    -----
    trigger     The clickable element (button, link, icon-button). Required.
    default     <x-ui.dropdown-item /> children.

    Accessibility
    -------------
    * `aria-haspopup`, `aria-expanded` on trigger.
    * `role="menu"` on the panel; items render with `role="menuitem"`.
    * ESC closes; click outside closes; tab navigates through items.

    Example
    -------
        <x-ui.dropdown align="right">
            <x-slot name="trigger">
                <x-ui.button variant="secondary" size="sm" iconEnd="chevron-down">Actions</x-ui.button>
            </x-slot>

            <x-ui.dropdown-item icon="edit" href="{{ route('tour.edit', $tour) }}">Edit</x-ui.dropdown-item>
            <x-ui.dropdown-item icon="copy" @click="$dispatch('open-modal', 'clone-tour')">Clone</x-ui.dropdown-item>
            <x-ui.dropdown-divider />
            <x-ui.dropdown-item icon="trash-2" danger @click="$dispatch('open-modal', 'delete-tour')">Delete</x-ui.dropdown-item>
        </x-ui.dropdown>
--}}

@props([
    'align' => 'left',
    'width' => 'w-56',
])

@php
    $alignClass = $align === 'right' ? 'right-0' : 'left-0';
@endphp

<div
    x-data="{ open: false }"
    x-on:keydown.escape.window="open = false"
    x-on:click.outside="open = false"
    class="relative inline-block"
>
    <div
        x-on:click="open = !open"
        :aria-expanded="open"
        aria-haspopup="true"
    >
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-30 mt-1 {{ $alignClass }} {{ $width }} rounded-md bg-white shadow-overlay border border-slate-200 py-1 origin-top"
        role="menu"
        style="display: none"
    >
        {{ $slot }}
    </div>
</div>
