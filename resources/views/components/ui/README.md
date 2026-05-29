# TMS Widget Library

Phase 2 of the Bootstrap → Tailwind migration. These are the **only** UI primitives you should reach for in new code. If you find yourself writing custom Bootstrap markup, stop and ask whether a widget needs to gain a new prop.

- **Path:** `resources/views/components/ui/`
- **Invocation:** `<x-ui.button />`, `<x-ui.modal id="…" />`, etc.
- **Styling:** Tailwind tokens only. Zero hex literals in widgets. Brand colour is `primary-600` (= `#0d9488`, teal).
- **Behavior layer:** Alpine.js (loaded via `resources/js/app.js`).
- **Icons:** Lucide via `blade-ui-kit/blade-icons` + `mallardduck/blade-lucide-icons`. Always go through `<x-ui.icon />`.
- **Forms:** Wrap every input in `<x-ui.form-field />` for consistent label/hint/error rendering.

## Conventions

1. **Every interactive element does something.** No `href="#"` placeholders, no buttons without handlers. Wire it or delete it.
2. **Server-rendered first.** Widgets emit static HTML; Alpine layers interactivity on top via `x-data` / `x-on:` directives.
3. **Tokens, not values.** Never write `style="color: #0d9488"`. Use `text-primary-600`. If the token doesn't exist, add it to `tailwind.config.js` first.
4. **Slot conventions.** `default` slot for body content, named slots for header actions / footers (`actions`, `footer`, `head`, `foot`).
5. **No JavaScript libraries beyond Alpine + Lucide.** Anything else needs explicit approval (e.g. a charting library).

## Index

| Widget | Purpose |
|---|---|
| [`<x-ui.icon>`](#icon) | One icon component (Lucide) — the only way to render an icon. |
| [`<x-ui.button>`](#button) | All buttons. Five variants × two sizes. |
| [`<x-ui.input>`](#input) | Single-line text input. |
| [`<x-ui.textarea>`](#textarea) | Multi-line text input. |
| [`<x-ui.select>`](#select) | Native styled `<select>` (≤ 20 options). |
| [`<x-ui.combobox>`](#combobox) | Searchable select with static or async options. **Select2 replacement.** |
| [`<x-ui.checkbox>`](#checkbox) | Single checkbox + label. |
| [`<x-ui.radio>`](#radio) | Radio button + label. |
| [`<x-ui.form-field>`](#form-field) | Label + control + hint + error wrapper. |
| [`<x-ui.card>`](#card) | Bordered panel with header / body / footer. |
| [`<x-ui.modal>`](#modal) | Overlay dialog with focus trap. |
| [`<x-ui.table>`](#table) + `<x-ui.th>` / `<x-ui.td>` | Static styled table. |
| [`<x-ui.data-table>`](#data-table) | Paginated, sortable, searchable data table with empty/loading/error states. **DataTables.net replacement.** |
| [`<x-ui.badge>`](#badge) | Inline status pill. |
| [`<x-ui.tag>`](#tag) | Like Badge but dismissable. |
| [`<x-ui.avatar>`](#avatar) | Circular user avatar (image or initials). |
| [`<x-ui.dropdown>`](#dropdown) + `<x-ui.dropdown-item>` / `<x-ui.dropdown-divider>` | Click-to-open menu. |
| [`<x-ui.toast>`](#toast) | Transient corner notification. One per layout. |
| [`<x-ui.tabs>`](#tabs) + `<x-ui.tab-panel>` | Horizontal tab bar. |
| [`<x-ui.page-header>`](#page-header) | Title + breadcrumbs + actions block. |
| [`<x-ui.empty-state>`](#empty-state) | "No data" placeholder. |
| [`<x-ui.loading-state>`](#loading-state) | Centered spinner + label. |
| [`<x-ui.error-state>`](#error-state) | "Something went wrong" panel with retry. |

---

### Icon

```blade
<x-ui.icon name="edit" />                       {{-- 16px Lucide edit icon --}}
<x-ui.icon name="trash-2" size="md" class="text-danger-600" />
```

### Button

```blade
<x-ui.button variant="primary" icon="save">Save changes</x-ui.button>
<x-ui.button variant="secondary" size="sm">Cancel</x-ui.button>
<x-ui.button variant="danger" icon="trash-2" size="sm">Delete</x-ui.button>
<x-ui.button variant="ghost" icon="more-horizontal" />
<x-ui.button as="a" href="{{ route('tour.create') }}" icon="plus">New tour</x-ui.button>
<x-ui.button loading>Saving…</x-ui.button>
```

### Input

```blade
<x-ui.input name="email" type="email" :value="old('email')" leadingIcon="mail" required />
```

### Textarea

```blade
<x-ui.textarea name="notes" rows="4" :value="old('notes')" />
```

### Select

```blade
<x-ui.select
    name="status"
    :options="['draft' => 'Draft', 'active' => 'Active']"
    :value="old('status', $tour->status)"
    placeholder="Pick a status"
/>
```

### Combobox

```blade
{{-- Static (small list, client-side filter) --}}
<x-ui.combobox
    name="country"
    :options="$countries"
    :value="old('country')"
    placeholder="Choose a country"
/>

{{-- Remote (async fetch, replaces Select2 AJAX) --}}
<x-ui.combobox
    name="hotel_id"
    searchUrl="{{ route('hotels.search') }}"
    :value="$tour->hotel_id"
    :valueLabel="optional($tour->hotel)->name"
    placeholder="Search hotels…"
/>
```

The async endpoint should return `[{ "value": 12, "label": "Acme Hotels" }, …]`.

### Checkbox

```blade
<x-ui.checkbox name="agree" label="I agree to the terms" :checked="old('agree')" required />
```

### Radio

```blade
<fieldset class="space-y-1">
    <legend class="text-sm font-medium text-slate-700">Status</legend>
    <x-ui.radio name="status" value="draft"  label="Draft"  :checked="$current === 'draft'" />
    <x-ui.radio name="status" value="active" label="Active" :checked="$current === 'active'" />
</fieldset>
```

### FormField

```blade
<x-ui.form-field
    label="Email"
    for="email"
    required
    hint="We'll never share your email."
    :error="$errors->first('email')"
>
    <x-ui.input name="email" id="email" type="email" :invalid="$errors->has('email')" required />
</x-ui.form-field>
```

### Card

```blade
<x-ui.card title="Tour details" description="Confirmed bookings only.">
    <x-slot name="actions">
        <x-ui.button variant="secondary" size="sm" icon="edit">Edit</x-ui.button>
    </x-slot>

    <dl class="grid grid-cols-2 gap-4">…</dl>

    <x-slot name="footer">
        <x-ui.button variant="secondary">Cancel</x-ui.button>
        <x-ui.button variant="primary">Save</x-ui.button>
    </x-slot>
</x-ui.card>
```

### Modal

```blade
<x-ui.button @click="$dispatch('open-modal', 'delete-tour')">Delete</x-ui.button>

<x-ui.modal id="delete-tour" title="Delete tour?" description="This cannot be undone.">
    <p class="text-sm text-slate-600">Tour "{{ $tour->name }}" will be removed.</p>
    <x-slot name="footer">
        <x-ui.button variant="secondary" @click="$dispatch('close-modal', 'delete-tour')">Cancel</x-ui.button>
        <form method="POST" action="{{ route('tour.destroy', $tour) }}" class="inline">
            @csrf @method('DELETE')
            <x-ui.button variant="danger" type="submit" icon="trash-2">Delete</x-ui.button>
        </form>
    </x-slot>
</x-ui.modal>
```

### Table

```blade
<x-ui.table>
    <x-slot name="head">
        <tr>
            <x-ui.th>Name</x-ui.th>
            <x-ui.th>Dates</x-ui.th>
            <x-ui.th align="right">Pax</x-ui.th>
        </tr>
    </x-slot>
    @foreach($tours as $tour)
        <tr class="hover:bg-slate-50">
            <x-ui.td>{{ $tour->name }}</x-ui.td>
            <x-ui.td>{{ $tour->departure_date }}</x-ui.td>
            <x-ui.td align="right">{{ $tour->pax }}</x-ui.td>
        </tr>
    @endforeach
</x-ui.table>
```

### Data Table

```blade
<x-ui.data-table
    :paginator="$tours"
    :columns="[
        ['key' => 'name',           'label' => 'Name',      'sortable' => true],
        ['key' => 'departure_date', 'label' => 'Departure', 'sortable' => true],
        ['key' => 'pax',            'label' => 'Pax',       'align' => 'right'],
    ]"
    searchable
    empty-title="No tours yet"
    empty-message="Create your first tour to get started."
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('tour.create') }}" icon="plus">New tour</x-ui.button>
    </x-slot>
</x-ui.data-table>
```

`$tours` is a `LengthAwarePaginator` from the controller. Sort and search are URL query params; the controller reads them via `$request->query('sort')` etc.

### Badge

```blade
<x-ui.badge variant="success" dot>Active</x-ui.badge>
<x-ui.badge variant="danger" icon="alert-circle">Overdue</x-ui.badge>
<x-ui.badge variant="warning">Pending review</x-ui.badge>
```

### Tag

```blade
<x-ui.tag variant="primary" icon="filter" dismissUrl="{{ request()->fullUrlWithQuery(['status' => null]) }}">
    Status: Active
</x-ui.tag>
```

### Avatar

```blade
<x-ui.avatar :name="$user->name" :src="$user->avatar_url" />
<x-ui.avatar name="Dawood Zafar" size="lg" />
```

### Dropdown

```blade
<x-ui.dropdown align="right">
    <x-slot name="trigger">
        <x-ui.button variant="secondary" size="sm" iconEnd="chevron-down">Actions</x-ui.button>
    </x-slot>

    <x-ui.dropdown-item icon="edit" href="{{ route('tour.edit', $tour) }}">Edit</x-ui.dropdown-item>
    <x-ui.dropdown-item icon="copy" @click="$dispatch('open-modal', 'clone-tour')">Clone</x-ui.dropdown-item>
    <x-ui.dropdown-divider />
    <x-ui.dropdown-item icon="trash-2" danger @click="$dispatch('open-modal', 'delete-tour')">Delete</x-ui.dropdown-item>
</x-ui.dropdown>
```

### Toast

Drop one `<x-ui.toast />` at the bottom of the staff layout. Then fire toasts from anywhere:

```blade
{{-- in tabler-app.blade.php (already added) --}}
<x-ui.toast />
```

```js
// From any inline <script> or compiled JS
toast('Tour saved successfully', 'success');
toast('Failed to save', 'danger');
window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Hi', variant: 'info' } }));
```

From PHP after a redirect:

```php
return redirect()->route('tour.index')->with('toast', [
    'message' => 'Tour deleted',
    'variant' => 'success',
]);
```

Laravel's `success` / `error` / `warning` flash keys are also auto-picked-up.

### Tabs

```blade
<x-ui.tabs default="frontsheet" persist>
    <x-ui.tab-panel id="frontsheet" label="Front sheet" icon="file-text">
        {{-- frontsheet contents --}}
    </x-ui.tab-panel>

    <x-ui.tab-panel id="billing" label="Billing" icon="receipt">
        {{-- billing contents --}}
    </x-ui.tab-panel>
</x-ui.tabs>
```

### Page Header

```blade
<x-ui.page-header
    title="Tours"
    description="All confirmed and draft tours."
    :breadcrumbs="[
        ['label' => 'Home',  'href' => url('/home')],
        ['label' => 'Tours'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('tour.create') }}" icon="plus">New tour</x-ui.button>
    </x-slot>
</x-ui.page-header>
```

### Empty State

```blade
<x-ui.empty-state
    icon="map"
    title="No tours yet"
    message="Start by creating your first tour."
>
    <x-ui.button as="a" href="{{ route('tour.create') }}" icon="plus">New tour</x-ui.button>
</x-ui.empty-state>
```

### Loading State

```blade
<x-ui.loading-state message="Fetching tours…" />
```

### Error State

```blade
<x-ui.error-state
    title="Couldn't load tours"
    message="The server returned an error."
    :retryUrl="request()->fullUrl()"
/>
```

---

## Design tokens reference

These live in [tailwind.config.js](../../../tailwind.config.js). Don't introduce ad-hoc values.

### Colors

- **Brand:** `primary-50 … primary-950` (teal scale, `primary-600` = `#0d9488`).
- **Neutral:** Tailwind's default `slate-50 … slate-950`.
- **Semantic:** `success`, `warning`, `danger`, `info` — each has `50` (subtle bg), `600` (default), `700` (hover).

### Typography

- **Sans:** Inter (variable). Self-hosted by `inter.css`.
- **Mono:** JetBrains Mono / IBM Plex Mono / Consolas fallback.
- **Sizes:** `text-xs` 12 / `text-sm` 14 / `text-base` 16 / `text-lg` 20 / `text-xl` 24 / `text-2xl` 30. No others.
- **Weights:** 400, 500, 600. No 700.

### Spacing

- **Baseline:** 8 px. Most spacing comes from `2`/`3`/`4`/`6` (= 8/12/16/24 px).
- **Component heights:** `h-7` (sm form/button = 28 px) / `h-9` (md = 36 px) / `h-12` (lg = 48 px).

### Radii

- `rounded-sm` 4 px — inputs, badges.
- `rounded` 6 px — buttons, cards.
- `rounded-md` 8 px — modals.

### Shadows

- `shadow-subtle` — inputs, light surface elevation.
- `shadow-card` — cards.
- `shadow-overlay` — modals, dropdowns.

No double-layered shadows. No `shadow-2xl`. If a UI element needs more shadow than `shadow-overlay`, it's probably the wrong widget.

---

## Migration cookbook

When you migrate a legacy Bootstrap view to widgets, run through this list per element.

| Bootstrap pattern | Widget replacement |
|---|---|
| `<button class="btn btn-primary">Save</button>` | `<x-ui.button>Save</x-ui.button>` |
| `<button class="btn btn-default btn-sm">Cancel</button>` | `<x-ui.button variant="secondary" size="sm">Cancel</x-ui.button>` |
| `<button class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>` | `<x-ui.button variant="danger" icon="trash-2">Delete</x-ui.button>` |
| `<div class="form-group"><label>Email</label><input class="form-control" /></div>` | `<x-ui.form-field label="Email"><x-ui.input name="email" /></x-ui.form-field>` |
| `<select class="form-control">…</select>` + Select2 init | `<x-ui.combobox name="…" :options="…" />` |
| `<div class="card"><div class="card-header"><h3 class="card-title">…</h3></div><div class="card-body">…</div></div>` | `<x-ui.card title="…">…</x-ui.card>` |
| `<div class="modal" id="x">…</div>` + `$('#x').modal('show')` | `<x-ui.modal id="x">…</x-ui.modal>` + `$dispatch('open-modal','x')` |
| `<table class="table table-striped table-hover"><thead><tr><th>…</th></tr></thead><tbody>…</tbody></table>` + `.DataTable()` | `<x-ui.data-table :paginator="…" :columns="…" />` |
| `<span class="badge badge-success">…</span>` | `<x-ui.badge variant="success">…</x-ui.badge>` |
| `<ul class="nav nav-tabs">…</ul>` + `<div class="tab-content">…</div>` | `<x-ui.tabs><x-ui.tab-panel id="…">…</x-ui.tab-panel></x-ui.tabs>` |
| `<i class="fa fa-pencil"></i>` | `<x-ui.icon name="edit" />` |
| `<i class="ti ti-mail"></i>` | `<x-ui.icon name="mail" />` |
| `<i class="glyphicon glyphicon-ok"></i>` | `<x-ui.icon name="check" />` |

When in doubt, search the existing components/ui/ directory before inventing markup.
