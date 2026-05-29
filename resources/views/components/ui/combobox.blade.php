{{--
    <x-ui.combobox /> — Searchable single-select. Select2 replacement.

    Two modes:
      1. Static options — pass `options` as a value→label array. Client-side filter.
      2. Async remote   — pass `searchUrl`; component fetches `?q=…` and expects
                          `[{ value, label }, …]` JSON. Debounced 200ms.

    Props
    -----
    name        (required string)   Form field name (writes a hidden input).
    id          (string)            Override auto id.
    value       (mixed)              Initially selected value.
    valueLabel  (string)             Pre-rendered label for the initial value
                                     (so async mode can show "Acme Hotels"
                                     without first round-tripping to fetch it).
    options     (array|null)         Static option map. Mutually exclusive with searchUrl.
    searchUrl   (string|null)        Endpoint returning [{value,label}, …].
    placeholder (string)             Shown when no value selected.
    invalid     (bool)
    disabled    (bool)
    required    (bool)

    Example — static
    ----------------
        <x-ui.form-field label="Country">
            <x-ui.combobox name="country" :options="$countries" :value="old('country')" placeholder="Choose…" />
        </x-ui.form-field>

    Example — remote (replaces Select2 AJAX)
    ----------------------------------------
        <x-ui.combobox
            name="hotel_id"
            searchUrl="{{ route('hotels.search') }}"
            :value="$tour->hotel_id"
            :valueLabel="optional($tour->hotel)->name"
            placeholder="Search hotels…"
        />

        // controller:
        public function search(Request $r) {
            return Hotel::where('name', 'like', '%' . $r->q . '%')
                ->limit(20)->get(['id as value', 'name as label']);
        }
--}}

@props([
    'name',
    'id' => null,
    'value' => null,
    'valueLabel' => null,
    'options' => null,
    'searchUrl' => null,
    'placeholder' => 'Select…',
    'invalid' => false,
    'disabled' => false,
    'required' => false,
])

@php
    $fieldId = $id ?? $name;

    // Materialize a label map for the initial value, so the trigger shows
    // the right label without any async work.
    $initialLabel = $valueLabel;
    if (is_null($initialLabel) && is_array($options) && !is_null($value) && isset($options[$value])) {
        $initialLabel = $options[$value];
    }

    $stateClasses = $invalid
        ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600/30'
        : 'border-slate-300 focus:border-primary-600 focus:ring-primary-600/30';

    // Pre-serialise static options as an array of {value, label}.
    $staticOptions = [];
    if (is_array($options)) {
        foreach ($options as $v => $l) {
            $staticOptions[] = ['value' => (string) $v, 'label' => (string) $l];
        }
    }
@endphp

<div
    x-data="{
        open: false,
        query: '',
        value: @js($value !== null ? (string) $value : null),
        label: @js($initialLabel),
        options: @js($staticOptions),
        async: @js((bool) $searchUrl),
        loading: false,
        async fetchAsync() {
            if (!this.async) return;
            this.loading = true;
            try {
                const url = new URL(@js($searchUrl), window.location.origin);
                url.searchParams.set('q', this.query);
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                this.options = res.ok ? await res.json() : [];
            } catch (e) {
                this.options = [];
            } finally {
                this.loading = false;
            }
        },
        filtered() {
            if (this.async) return this.options;
            const q = this.query.trim().toLowerCase();
            if (!q) return this.options;
            return this.options.filter(o => String(o.label).toLowerCase().includes(q));
        },
        select(opt) {
            this.value = String(opt.value);
            this.label = opt.label;
            this.open = false;
            this.query = '';
            this.$dispatch('combobox-change', { name: @js($name), value: this.value, label: this.label });
        },
        clear() {
            this.value = null;
            this.label = null;
            this.query = '';
            this.$refs.search?.focus();
        }
    }"
    x-on:click.outside="open = false"
    x-on:keydown.escape="open = false"
    class="relative"
>
    {{-- Hidden input that participates in the form submit. --}}
    <input type="hidden" name="{{ $name }}" id="{{ $fieldId }}" :value="value" @if($required) required @endif />

    {{-- Trigger --}}
    <button
        type="button"
        x-on:click="open = !open; if (open && async && options.length === 0) fetchAsync(); $nextTick(() => $refs.search?.focus())"
        :aria-expanded="open"
        aria-haspopup="listbox"
        {{ $attributes->class('flex items-center justify-between gap-2 h-9 w-full rounded border bg-white px-3 py-2 text-sm text-left shadow-subtle focus:outline-none focus:ring-2 ' . $stateClasses) }}
        @if($disabled) disabled @endif
    >
        <span :class="value ? 'text-slate-900' : 'text-slate-400'" x-text="label || @js($placeholder)" class="truncate"></span>
        <span class="flex items-center gap-1 shrink-0 text-slate-400">
            <template x-if="value">
                <span x-on:click.stop="clear()" class="p-0.5 rounded hover:bg-slate-100 cursor-pointer" role="button" aria-label="Clear">
                    <x-ui.icon name="x" size="xs" />
                </span>
            </template>
            <x-ui.icon name="chevron-down" />
        </span>
    </button>

    {{-- Popover --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.100ms
        class="absolute z-30 mt-1 w-full rounded-md bg-white shadow-overlay border border-slate-200 origin-top max-h-72 flex flex-col"
        role="listbox"
        style="display:none"
    >
        <div class="p-2 border-b border-slate-200">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2 text-slate-400">
                    <x-ui.icon name="search" size="xs" />
                </span>
                <input
                    x-ref="search"
                    x-model.debounce.200ms="query"
                    x-on:input="async && fetchAsync()"
                    type="text"
                    placeholder="Search…"
                    class="block w-full rounded border border-slate-200 bg-white pl-7 pr-2 h-7 text-xs focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                />
            </div>
        </div>

        <div class="overflow-y-auto py-1">
            <template x-if="loading">
                <div class="py-3 text-center text-xs text-slate-400">Searching…</div>
            </template>

            <template x-if="!loading && filtered().length === 0">
                <div class="py-3 text-center text-xs text-slate-400">No matches</div>
            </template>

            <template x-for="opt in filtered()" :key="opt.value">
                <button
                    type="button"
                    role="option"
                    :aria-selected="String(opt.value) === value"
                    x-on:click="select(opt)"
                    class="flex w-full items-center justify-between gap-2 px-3 py-1.5 text-sm text-left hover:bg-slate-100"
                    :class="String(opt.value) === value ? 'bg-primary-50 text-primary-700' : 'text-slate-700'"
                >
                    <span class="truncate" x-text="opt.label"></span>
                    <template x-if="String(opt.value) === value">
                        <x-ui.icon name="check" size="xs" />
                    </template>
                </button>
            </template>
        </div>
    </div>
</div>
