{{--
    <x-ui.tabs /> — Horizontal tab bar with underline indicator.

    Props
    -----
    default (string)   ID of the tab that should be active on load.
    persist (bool)     Persist the active tab in the URL hash so deep-links work.

    Slot
    ----
    default     One or more <x-ui.tabs.panel id="..." label="..."> children.
                Use the helper component <x-ui.tab-panel>.

    Accessibility
    -------------
    * Roles: `tablist`, `tab`, `tabpanel`.
    * Arrow-Left/Right cycle focus through tabs.
    * Active tab gets `aria-selected="true"`, others `tabindex="-1"`.

    Example
    -------
        <x-ui.tabs default="frontsheet">
            <x-ui.tab-panel id="frontsheet" label="Front sheet" icon="file-text">
                {{-- frontsheet contents --}}
            </x-ui.tab-panel>

            <x-ui.tab-panel id="billing" label="Billing" icon="receipt">
                {{-- billing contents --}}
            </x-ui.tab-panel>
        </x-ui.tabs>
--}}

@props([
    'default' => null,
    'persist' => false,
])

@php
    // First render collects panel metadata from $slot via a thin convention:
    // each <x-ui.tab-panel> renders <template data-tab-panel=…>.
    // The tabs component reads them at runtime via Alpine.
@endphp

<div
    x-data="{
        active: '{{ $default }}',
        tabs: [],
        register(t) { this.tabs.push(t); if (!this.active) this.active = t.id; },
        select(id, focus = true) {
            this.active = id;
            if ({{ $persist ? 'true' : 'false' }}) history.replaceState(null, '', '#' + id);
            if (focus) this.$nextTick(() => this.$refs[`tab-${id}`]?.focus());
        },
        init() {
            if ({{ $persist ? 'true' : 'false' }} && location.hash) {
                const id = location.hash.slice(1);
                if (this.tabs.some(t => t.id === id)) this.active = id;
            }
        },
        move(dir) {
            const idx = this.tabs.findIndex(t => t.id === this.active);
            const next = (idx + dir + this.tabs.length) % this.tabs.length;
            this.select(this.tabs[next].id);
        }
    }"
    {{ $attributes }}
>
    {{-- Tablist (rendered from registered panels) --}}
    <div
        role="tablist"
        class="flex items-center gap-1 border-b border-slate-200 mb-4"
        x-on:keydown.right.prevent="move(1)"
        x-on:keydown.left.prevent="move(-1)"
    >
        <template x-for="t in tabs" :key="t.id">
            <button
                type="button"
                role="tab"
                :id="`tab-${t.id}`"
                :aria-selected="active === t.id"
                :aria-controls="`panel-${t.id}`"
                :tabindex="active === t.id ? 0 : -1"
                :ref="`tab-${t.id}`"
                x-on:click="select(t.id, false)"
                class="relative inline-flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors"
                :class="active === t.id
                    ? 'text-primary-700'
                    : 'text-slate-500 hover:text-slate-700'"
            >
                <template x-if="t.icon">
                    <span x-html="t.icon"></span>
                </template>
                <span x-text="t.label"></span>
                <span
                    class="absolute inset-x-0 -bottom-px h-0.5 bg-primary-600 transition-opacity"
                    :class="active === t.id ? 'opacity-100' : 'opacity-0'"
                    aria-hidden="true"
                ></span>
            </button>
        </template>
    </div>

    {{-- Panels (rendered as-is; children register themselves on init) --}}
    {{ $slot }}
</div>
