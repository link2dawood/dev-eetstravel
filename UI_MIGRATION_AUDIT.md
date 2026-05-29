# UI Migration Audit — Bootstrap → Tailwind

**Repo:** `/var/www/html` · **Phase:** 1 (audit only, no code changes) · **Sister doc:** [AUDIT.md](AUDIT.md) (backend audit produced in the same session).

> **Stop point:** Per the brief, this document is a deliverable for review. **No Tailwind has been installed, no widgets have been created, no Bootstrap has been removed.** Phase 2 starts only after you approve.

---

## Executive snapshot

| Metric | Value |
|---|---|
| Total Blade files | **434** |
| Files extending the main staff layout `scaffold-interface.layouts.tabler-app` | **181** |
| Other layouts in use | `layouts.app` (7), `scaffold-interface.layouts.modernapp` (3), `adminlte::layouts.*` (9), `email.layout` (3) |
| Inline `<style>` blocks | **64 files** |
| Inline `style="…"` attributes | **239 files** |
| Custom CSS files in `public/css/` | **27** (4 are >100KB) |
| `<form>` blocks | 169 files |
| Files using Bootstrap 5 `data-bs-*` | 45 |
| Files using legacy Bootstrap 4 `data-toggle` / `data-dismiss` / `data-target` | **55** (more than BS5 — mixed era) |
| Files using AdminLTE `data-widget="collapse|remove"` | 19 |

The codebase currently runs **three CSS frameworks simultaneously**: Tabler (which bundles Bootstrap 5), an inline AdminLTE-compatibility shim (in [tabler-app.blade.php](resources/views/scaffold-interface/layouts/tabler-app.blade.php)), and pieces of raw Bootstrap 4 (`pull-right`, `nav-stacked`, `input-group-btn`, `data-dismiss`). The Sass entry point at [resources/sass/app.scss](resources/sass/app.scss) imports `~bootstrap/scss/bootstrap` wholesale; the layout also includes Tabler's bundled CSS+JS from `public/tabler/`. Multiple legacy AdminLTE skin files (`_all-skins.css`, `skin-black.css`, etc.) still ship even though no view references them.

A Tailwind migration here is mostly a *deletion* exercise — strip legacy CSS, replace the framework-soup with one consistent system, and consolidate ~50 repeated UI patterns into ~15 widgets.

---

## 1. Bootstrap usage by class category

Single-pass `grep -rho 'class="…"' resources/views --include='*.blade.php'` against the whole view tree, then class-frequency extraction. **The numbers below are usage occurrences, not unique files.**

### 1.1 Buttons (10 distinct variants — too many)

| Class | Occurrences |
|---|---:|
| `btn` (any) | **996** |
| `btn-primary` | 175 |
| `btn-success` | 175 |
| `btn-sm` | 110 |
| `btn-default` (Bootstrap 3 legacy) | 53 |
| `btn-danger` | 50 |
| `btn-box-tool` (AdminLTE) | 47 |
| `btn-flat` (AdminLTE) | 38 |
| `btn-warning` | 23 |
| `btn-block` (BS4 deprecated) | 19 |
| `btn-group` | 18 |
| `btn-secondary` | 17 |
| `btn-icon` (Tabler) | 17 |
| `btn-outline-primary` / `btn-outline-secondary` | 12 / 11 |
| `btn-lg` | 9 |
| `btn-info` | 9 |
| `btn-link` | 3 |
| `btn-outline-danger` | 2 |

**Observation:** `btn-success` (175) is used as heavily as `btn-primary` (175) — the design system doesn't distinguish them by purpose, they're chosen by feel. `btn-default` (53) and `btn-info` (9) are Bootstrap 3 leftovers. `btn-flat` and `btn-box-tool` are AdminLTE. Five different "secondary-ish" buttons exist (`btn-default`, `btn-secondary`, `btn-outline-secondary`, `btn-flat`, `btn-box-tool`).

### 1.2 Forms

| Class | Occurrences |
|---|---:|
| `form-control` | **772** |
| `form-group` (BS4 deprecated) | **661** |
| `form-label` | 285 |
| `input-group` | 214 |
| `form-check` | 60 |
| `form-select` | 44 |
| `is-invalid` | 19 |
| `invalid-feedback` | 18 |
| `input-group-text` | 10 |
| `input-group-btn` (BS4 removed) | 9 |
| `input-group-append` (BS4 removed) | 5 |

**Observation:** `form-group` (BS4) outnumbers `form-label` (BS5). Validation classes (`is-invalid`/`invalid-feedback`) are rare relative to form count — most forms don't render validation state at all in the markup; they either rely on Laravel's session-flash redirect or just don't show errors.

### 1.3 Grid / layout

| Class | Occurrences |
|---|---:|
| `row` | **644** |
| `col-md-6` | 358 |
| `d-flex` | 188 |
| `align-items-center` | 155 |
| `col-md-12` | 148 |
| `container` | 98 |
| `col-lg-6` | 90 |
| `col-12` | 76 |
| `col-md-4` | 67 |
| `justify-content-between` | 62 |
| `col-sm-5` / `col-sm-7` / `col-sm-4` | 53 / 52 / 46 |
| `g-2` (BS5 gutter) | 44 |
| `container-fluid` | 41 |

**Observation:** Grids are deeply nested, with `col-md-*` dominating over `col-lg-*` and `col-12`. The 644 `row`s + 358 `col-md-6`s mean nearly every page is a 2-column grid built with `row > col-md-6`. Direct Tailwind equivalent: `grid grid-cols-2 gap-4` (~12 chars vs the BS pattern's `row > col-md-6 + col-md-6`).

### 1.4 Card / Modal / Table / Nav / Alert / Badge / Dropdown

| Class | Occurrences |
|---|---:|
| `table` | **496** |
| `card` | 176 |
| `alert` | 176 |
| `modal` | 147 |
| `card-body` | 132 |
| `breadcrumb` | 112 |
| `alert-danger` | 110 |
| `card-header` | 101 |
| `card-title` | 98 |
| `modal-dialog` | 95 |
| `nav-link` | 92 |
| `table-bordered` | 90 |
| `table-hover` | 87 |
| `table-striped` | 84 |
| `tab-pane` | 76 |
| `modal-content` | 72 |
| `nav-item` | 70 |
| `dropdown-item` | 69 |
| `badge` | 68 |
| `dropdown-menu` | 65 |
| `modal-header` | 57 |
| `nav` | 56 |
| `modal-title` | 54 |
| `modal-body` | 50 |
| `dropdown` | 47 |

### 1.5 Legacy classes still in the wild

| Class | Occurrences | Origin | Status |
|---|---:|---|---|
| `box` | **1018** | AdminLTE | covered by inline shim in tabler-app |
| `pull-right` / `pull-left` | 139 / 10 | Bootstrap 3 | should be `ms-auto` / `me-auto` (BS5) or `ml-auto` (Tailwind) |
| `panel` | 55 | Bootstrap 3 | unstyled, broken visuals |
| `page-header` | 42 | Bootstrap 3 | unstyled |
| `btn-flat` | 38 | AdminLTE | unstyled |
| `box-body` / `box-tools` | 18 / 16 | AdminLTE | covered by shim |
| `nav-stacked` | 6 | Bootstrap 3 (removed in BS5) | already broke folder list before recent fix |
| `input-group-btn` | 9 | Bootstrap 4 (removed in BS5) | broken |
| `input-group-append` | 5 | Bootstrap 4 (removed in BS5) | broken |

> **Practical implication:** ~1300 occurrences of dead legacy classes. They render today only because the inline AdminLTE compatibility shim in [tabler-app.blade.php:75-200+](resources/views/scaffold-interface/layouts/tabler-app.blade.php#L75) re-implements them. That shim can go away once these are replaced.

### 1.6 Bootstrap density by module

These are absolute counts of canonical Bootstrap classes (`btn`, `card`, `modal`, `table`, `form-control`, `form-group`, `col-md-N`, `row`, `nav`, `alert`, `badge`, `dropdown`, `breadcrumb`, `input-group`, `tab-pane`, `box`, `pull-right`) per module — useful for sizing the migration.

| Module / Folder | Blade files | Bootstrap class hits |
|---|---:|---:|
| `scaffold-interface/` (layouts + dashboard) | 37 | **960** |
| `tour/` (the big one) | 6 | **821** |
| `TMSClient/` (client portal) | 16 | 431 |
| `email/` | 21 | 376 |
| `office/` | 16 | 334 |
| `tour_package/` | 8 | 322 |
| `task/` | 5 | 268 |
| `transfer/` | 4 | 215 |
| `flight/` | 4 | 202 |
| `accounting/` | 6 | 200 |
| `restaurant/` | 4 | 200 |
| `quotation/` | 6 | 191 |
| `guide/` | 4 | 186 |
| `cruises/` | 4 | 185 |
| `event/` | 4 | 181 |
| `hotel/` | 4 | 176 |
| `clients/` | 4 | 167 |
| `invoices/` | 5 | 133 |
| `comparison/` | 3 | 114 |
| `bus/` | 5 | 81 |
| `driver/` | 4 | 74 |
| `guest_list/` | 2 | 56 |
| `export/` (PDF/Word templates) | 15 | 53 |
| `comments/` | 5 | 39 |
| `dashboard/` | 1 | 29 |
| `TMSSupplier/` | 2 | 13 |
| `chat/` | 0 | 0 |

**Worst single-page offender:** [resources/views/tour/show.blade.php](resources/views/tour/show.blade.php) (single 2700-line file, ~225 Bootstrap class hits on a quick grep). This is the page to migrate last — it's both the biggest and the one users hit most often.

**Smallest realistic starting point:** `dashboard/`, `guest_list/`, `comments/`, `TMSSupplier/`, `chat/` (which has zero Blade files — just routes).

### 1.7 Layouts in use

| Layout | `@extends` count | Notes |
|---|---:|---|
| `scaffold-interface.layouts.tabler-app` | 181 | The main staff layout. Contains the AdminLTE-compatibility shim (~150 lines of inline CSS) and the Bootstrap 5 polyfill JS. Load order: jQuery → tabler.min.js → polyfill shim → app JS. |
| `scaffold-interface.layouts.modernapp` | 3 | Near-identical to tabler-app — orphan candidate. |
| `email.layout` | 3 | Email inbox shell, extends `tabler-app`. |
| `layouts.app` | 7 | Default Laravel scaffolding layout, mostly auth pages. |
| `adminlte::layouts.{auth,errors,landing,app}` | 9 | Acacha AdminLTE vendor package — used for auth + error pages only. |

Standalone HTML pages (no `@extends`): `booking_request/booking_request.blade.php`, `TMSClient/home/tour/index.blade.php`, the entire `export/*` PDF/Word template tree. These have their own `<head>` and link external Bootstrap 4 from CDN.

### 1.8 Inline CSS exposure

- **64 Blade files** contain a `<style>` block (most are page-specific override scripts).
- **239 Blade files** contain at least one inline `style="…"` attribute (`pull-right` replacement, ad-hoc spacing, color overrides).
- **15 Blade files** use Vue / Alpine reactive directives (`v-model`, `@click`, `x-data`). The rest are static server-rendered with jQuery sprinkles.

### 1.9 Icon systems in use (multiple, no consistency)

| System | Class occurrences |
|---|---:|
| Font Awesome 4.7 (`fa-*`) | **667** |
| Tabler Icons (`ti-*`) | 638 |
| Bootstrap 3 Glyphicons (`glyphicon-*`) | 39 |
| Misc custom (`icon-*`) | 72 |
| Inline `<svg>` | 11 files |

**Three icon libraries shipped concurrently.** Font Awesome 4.7 (released 2016, no longer maintained) and Tabler Icons (used as the modern set) appear in roughly equal numbers — same icons drawn from both libraries in different views.

### 1.10 JS libraries that drive UI today

| Library | Files |
|---|---:|
| Select2 | 27 |
| DataTables | 19 |
| Bootstrap-tables custom plugin (`bootstrap-tables.js`) | 35 |
| Vue (sprinkled, not a full SPA) | 15 |
| jQuery (loaded globally) | basically all |

---

## 2. Inventory of CSS files in `public/css/`

27 CSS files totalling ~3.5 MB. Listed by size:

| File | Size | Purpose | Migration disposition |
|---|---:|---|---|
| `all.css` | **1.34 MB** | Catch-all bundle (Bootstrap + AdminLTE + plugins concatenated) | **DELETE** in Phase 4 |
| `app.css` | **1.17 MB** | Compiled output of `resources/sass/app.scss` | replaced by `tailwind.css` |
| `all-landing.css` | 137 KB | Landing-page bundle (Bootstrap 4 + theme) | **DELETE** in Phase 4 |
| `bootstrap.css` / `bootstrap.min.css` | 134 / 121 KB | Bootstrap 4.x source | **DELETE** in Phase 4 |
| `adminlte-app.css` / `adminlte-app2.css` | 121 / 119 KB | AdminLTE 2.x theme | **DELETE** in Phase 4 |
| `ionicons.min.css` | 51 KB | Ionicon font (referenced in 1 view) | **DELETE** in Phase 4 |
| `tour-shopify.css` | 18 KB | Custom styles for tour show page | merge into Tailwind component layer or `widgets/Tour*.css` |
| `calendar-enhancements.css` | 17 KB | Dashboard calendar gradient header | adapt selectors to Tailwind |
| `monday-style.css` | ? | Tasks "Monday-board" styling | candidate for Tasks-module redesign |
| `modern-forms.css` | ? | Recent ad-hoc form polish | superseded by `widgets/FormField` |
| `modern-tables.css` | ? | Recent ad-hoc table polish | superseded by `widgets/DataTable` |
| `magnific.css` | ? | Magnific Popup (image lightbox) | keep if lightbox used; check |
| `responsive-global.css` | ? | Mobile breakpoint overrides | candidate for deletion (Tailwind responsive utilities replace) |
| `style.css` | ? | Misc page styles | inspect; mostly dead weight |
| `bootstrap-datepicker.min.css` / `bootstrap-datetimepicker.min.css` | small | Bootstrap-style date pickers | replace with `<input type="date">` or a Tailwind-compatible picker |
| `colorpicker.css`, `fileinput.min.css`, `select2.min.css`, `fullcalendar.css`, `toastr.css`, `jquery.toast.css`, `bootstrap-tables.css`, `jquery-jvectormap-*.css` | small | jQuery-plugin styling | keep until each plugin is replaced |
| `font-awesome-4.7.0/` (folder) | large | FA4 font + CSS | **DELETE** after icon consolidation |
| `plugins/export.css` | small | export PDF page CSS | retain for now |
| `scaffold-interface-css/main.css` | small | scaffold theme | inspect; likely dead |
| `skins/_all-skins*.css`, `skins/skin-{black,blue}*.css` | medium | AdminLTE colour skins | **DELETE** — no view references them |
| `util.css` | small | misc utility classes | check overlap with Tailwind |

**Total CSS to remove in Phase 4:** ~3.0 MB of the 3.5 MB. The replacement Tailwind output (with PurgeCSS) should be ~30-50 KB compressed for the entire app.

The Sass tree at [resources/sass/](resources/sass/) contains only `app.scss` + `_variables.scss`. The variables file sets `$body-bg: #f8fafc;`, `$font-family-sans-serif: 'Nunito', sans-serif;`, `$font-size-base: 0.9rem;` — these are the closest thing to a brand spec the project has.

---

## 3. Proposed widget library

Names follow common conventions (Headless UI / Radix / Notion-style internals). Each widget below maps to a recurring pattern found in views. Where a widget is broken or inconsistent today, that's flagged.

### Primitives

| Widget | Purpose | Replaces today | Used by (sample views) |
|---|---|---|---|
| **`Button`** | All buttons. Variants: `primary` (single accent), `secondary` (neutral border), `ghost` (no border, hover bg), `danger` (red), `link`. Sizes: `sm`, `md`. Icon slot. | 996 `btn`s split across 10 variants — `btn-default`/`btn-success`/`btn-flat`/`btn-box-tool` all collapse to `secondary` or `ghost` | Everywhere |
| **`IconButton`** | Square 32-/36-px button with single icon. | `btn btn-icon`, `btn btn-sm` + icon-only patterns, `btn-box-tool` collapse/close | Card headers, table rows |
| **`Icon`** | Single icon component fed by a single library — recommend **Lucide React-port (Lucide icons)** or **Tabler Icons (already in repo)**. Pick one and rip out Font Awesome 4 and Glyphicons. | 638 `ti-*` + 667 `fa-*` + 39 `glyphicon-*` + 72 `icon-*` + 11 inline SVGs | Everywhere |
| **`Input`** | Single-line text input. Sizes `sm`, `md`. Validation state. | 772 `form-control` instances on `<input>` | Every form |
| **`Textarea`** | Multi-line text input. | `form-control` on `<textarea>` | Notes, descriptions |
| **`Select`** | Native `<select>` styled to match. **Note:** 27 views currently use Select2; decision needed on whether to keep Select2 or replace with a headless combobox. | 44 `form-select` + 27 Select2 instances | Tour edit, supplier filters |
| **`Checkbox`** | Square check input. | 60 `form-check` instances | Permissions, filters, vouchers |
| **`Radio`** | Round radio input. | `form-check` variants | Small number of forms |
| **`FormField`** | Wrapper: label, input/select/etc., hint text below, error text below, required asterisk. Replaces `form-group` + `form-label` + manual error placement. | 661 `form-group` + 285 `form-label` + 18 `invalid-feedback` (rarely paired correctly) | Every form |
| **`Card`** | Bordered panel with optional header (title + action slot) and body. | 176 `card` + 1018 `box` legacy + 55 `panel` legacy | Forms, sections, list containers |
| **`Modal`** | Overlay dialog. Header with title + close, body, footer with action buttons. Focus trap, ESC to close, click-outside to close. | 147 `modal` + assorted `data-bs-toggle="modal"` and legacy `data-toggle="modal"` | Delete confirmations, voucher selector, email compose, etc. |
| **`Drawer`** | Side panel (alternative to modal for forms). | none today; pattern repeated as ad-hoc fixed-position divs | Detail panels |
| **`Table`** | Static (non-paginated) `<table>` with consistent borders/zebra/hover. | 496 `<table>` + 87 `table-hover` + 84 `table-striped` | Read-only data displays |
| **`DataTable`** | Paginated, sortable, searchable table with empty/loading/error states. **Replaces the current `$().DataTable(...)` + central CDN partial pattern.** | 19 views calling `.DataTable()` (mostly via DataTables.net JS) | All `*/index.blade.php` |
| **`Badge`** | Small pill for status. Variants: `neutral`, `success`, `warning`, `danger`, `info`. | 68 `badge` + ad-hoc `<span style="background:…">` | Tour status, task priority, invoice paid/unpaid |
| **`Tag`** | Like Badge but interactive (dismissable). | none today; manual constructions | Filters, multi-select chips |
| **`Avatar`** | Circle with initials or photo. | 1 ad-hoc CSS class in `inbox_emails.blade.php` | Email rows, task assignees |
| **`Dropdown` / `Menu`** | Click-to-open menu, keyboard navigable. | 47 `dropdown` + 65 `dropdown-menu` + 69 `dropdown-item` | Tour show actions, sidebar |
| **`Toast` / `Notification`** | Transient corner alert. | `jquery.toast.js` (1.6 KB CSS file dedicated to it) + 110 `alert-danger` blocks used as flash messages | Flash messages after save |
| **`Tabs`** | Horizontal tab bar + animated underline + panels. | 76 `tab-pane` + 56 `nav` + 92 `nav-link` + `nav-tabs-custom` (AdminLTE) | Tour show (frontsheet/billing/etc.) |
| **`Breadcrumb`** | Title hierarchy at top of page. | 112 `breadcrumb` instances + the `layouts.title` partial | Every staff page |
| **`PageHeader`** | Title + breadcrumb + actions slot. | `layouts.title` partial (used by ~150 views) + ad-hoc `<h1>` headings | Every staff page |
| **`EmptyState`** | Centered icon + headline + sub-text + optional CTA when a list is empty. | Inconsistent: sometimes "No data available" plain text, sometimes a hand-rolled SVG block (`inbox_emails.blade.php` has one) | Every list |
| **`LoadingState`** | Spinner + descriptive text. | 5+ ad-hoc patterns; jQuery Toast spinner, inline `<div class="loader">`, `spinner-border`, etc. | All async UI |
| **`ErrorState`** | "Something went wrong" panel with retry button. | none — errors today are either a `\Log::warning` swallow or a flash-redirect. **This is the biggest UX gap.** | Every async UI |
| **`StatCard`** | Number + label + small delta indicator. Used on dashboards. | not present today — dashboard widgets use plain card. | Dashboard, office show |
| **`Alert`** | Static info/warning/error panel. | 176 `alert` (heavily used for flash messages — should move to Toast) | Form errors, banners |

### Compound widgets

| Widget | Purpose | Replaces today |
|---|---|---|
| **`SearchableList`** | Search input + filter chips + paginated DataTable. The pattern most index pages should use. | Per-page ad-hoc Vue/JS in `supplier_search/index`, `tour/index`, etc. |
| **`FilterBar`** | Date range, status select, search input, "Reset" link. | Inline per page; today every index reimplements filtering |
| **`DateRangePicker`** | Two-input picker. | `bootstrap-datepicker` + `bootstrap-datetimepicker` |
| **`FileUpload`** | Drop zone + file list + progress. | `fileinput.min.css` plugin |
| **`RichTextEditor`** | CKEditor or replacement. | CKEditor 4 (legacy) + Tagify |

---

## 4. Dead UI inventory

Anything below is either non-functional, orphan markup, or commented-out junk a fix author has to wade through.

### 4.1 Layouts that are likely dead

- [resources/views/scaffold-interface/layouts/modernapp.blade.php](resources/views/scaffold-interface/layouts/modernapp.blade.php) — 3 references, near-identical to tabler-app.
- [resources/views/scaffold-interface/layouts/defaultMaterialize.blade.php](resources/views/scaffold-interface/layouts/defaultMaterialize.blade.php) — 0 `@extends` references.
- [resources/views/scaffold-interface/layouts/sidebar.blade.php](resources/views/scaffold-interface/layouts/sidebar.blade.php) — superseded by `tabler-sidebar.blade.php`; double-check.

### 4.2 CSS files referenced by zero views

- `public/css/adminlte-app2.css` — second copy of `adminlte-app.css`, unreferenced.
- `public/css/skins/*.css` — AdminLTE colour skins, unreferenced.
- `public/css/all.css` and `public/css/all-landing.css` — fat catch-all bundles, unreferenced in source (only loaded by some standalone HTML pages outside the staff app; verify before deleting).
- `public/css/plugins/export.css` — likely used only by PDF templates.

> Each candidate above should be verified with a final `grep -r filename` in Phase 4 before deletion.

### 4.3 Orphan icon library files

- `public/css/font-awesome-4.7.0/` — 5+ MB of fonts and CSS supporting 667 `fa-*` references that should consolidate into the single chosen icon library.
- `public/css/ionicons.min.css` — Ionicons font (referenced in 1 view at most).

### 4.4 Non-functional buttons / dead anchors found in spot-checks

These are `<a href="#">` or `<button>` elements that either have no handler at all or whose handler doesn't exist. (Comprehensive enumeration is part of Phase 3 per-module manual click-through; this list is what surfaced during the audit.)

- [resources/views/task/show.blade.php:96](resources/views/task/show.blade.php#L96) — `<a href="#" id="reply_close"><i class="fa fa-close"></i></a>` — no event handler bound for `#reply_close` in the file.
- [resources/views/currency_rate/show.blade.php:70](resources/views/currency_rate/show.blade.php#L70) — same `#reply_close` pattern, no handler.
- [resources/views/templates/show.blade.php:107](resources/views/templates/show.blade.php#L107) — same.
- [resources/views/clients/show.blade.php:134](resources/views/clients/show.blade.php#L134) — same.
- [resources/views/restaurant/show.blade.php:194](resources/views/restaurant/show.blade.php#L194) — same.
- [resources/views/hotel/show.blade.php:108](resources/views/hotel/show.blade.php#L108) — same.
- [resources/views/email/index.blade.php:33, 56, 80, 104, 167](resources/views/email/index.blade.php#L33) — multiple `<a href="#">` with no behavior wired.
- [resources/views/email/parts/foldersList.blade.php:19, 29](resources/views/email/parts/foldersList.blade.php#L19) — folder links use `href="#"` and rely on Vue `@click.prevent`; functional but the dead-link href is misleading to screen readers / right-click navigate.
- [resources/views/task/index.blade.php:411-509](resources/views/task/index.blade.php#L411) — six `<button>`s in the Tasks header (`mainTableBtn`, `kanbanBtn`, four "header-btn" buttons, one `header-btn ms-auto`) with no `@click` / `onclick` and no event listener registered in the file's `<script>`. **Most likely dead UI from a partial Monday-board redesign.**
- Quotation index toggle JS broken (covered in AUDIT.md) — `function myfunction()` rendered outside `<script>` tag at [resources/views/quotation/index.blade.php:133-164](resources/views/quotation/index.blade.php#L133); the Quotations/GoAhead tabs never toggle.
- Guest list index AJAX-fetches a non-existent `route('quotation.data')` ([resources/views/guest_list/index.blade.php:58-81](resources/views/guest_list/index.blade.php#L58)) — page is permanently broken (also in AUDIT.md).
- Several `<button data-widget="collapse">` / `<button data-widget="remove">` headers in dashboard cards — AdminLTE-era collapse/close behaviour that depends on the legacy AdminLTE jQuery plugin, which is not loaded. They render but do nothing.

### 4.5 Commented-out UI blocks worth removing

- [resources/views/tour/show.blade.php:401-418](resources/views/tour/show.blade.php#L401) — empty `<tbody>` comment block.
- [resources/views/tour/show.blade.php:556-568](resources/views/tour/show.blade.php#L556) — commented HPP math.
- [resources/views/export/landing_page.blade.php:182-186, 250-300, 327-328, 367-383](resources/views/export/landing_page.blade.php#L182) — multiple commented attachment / rooms / pickup blocks.
- [resources/views/quotation/edit.blade.php:64-75](resources/views/quotation/edit.blade.php#L64), [:187](resources/views/quotation/edit.blade.php#L187) — `{{--{{dump(...)}}--}}` debug leftovers.
- [resources/views/quotation/create.blade.php:264-271, 325](resources/views/quotation/create.blade.php#L264) — same.
- [resources/views/email/index_old.blade.php](resources/views/email/index_old.blade.php) — entire "old" version of the email index, still in the tree.
- [resources/views/tour/index.blade.php.backup](resources/views/tour/index.blade.php.backup) — `.backup` file checked in.
- `public/js/quotation.js:389, 684, 685, 1453` — `console.log` leftovers.
- `app/Http/Controllers/TourController.php:953-1139` — 100+ lines of commented `store()` (covered in AUDIT.md, listed here too because it touches the UI flow).

### 4.6 Unused JS / inline handlers

- 6 files with `javascript:void(...)` or `javascript:;` placeholder hrefs.
- AdminLTE `data-widget="collapse"` / `data-widget="remove"` is referenced in 19 files but the JS that implements those widgets is not loaded — those buttons are dead.

---

## 5. Suggested accent color and font

The codebase doesn't have an explicit brand spec. Best signal sources, in order:

1. **Tabler's default primary, `#066fd1`** — set via `--tblr-primary` in the bundled Tabler CSS, and embedded throughout the inline AdminLTE compatibility shim. Most "live" UI surfaces (sidebar active state, primary buttons) render this colour today.
2. **AdminLTE's `#3c8dbc`** — appears in the calendar widget's old gradient (now replaced) and in some `_all-skins.css` colour swatches.
3. **Brand fallback `#206bc4`** — Tabler's secondary blue, also seen in inline `<style>` overrides.

All three are essentially "office blue" — the same hue at slightly different saturation. I recommend picking one and committing.

### Recommendation

```css
/* Accent: Tabler primary, retain for visual continuity */
--accent-50:  #eff6ff;
--accent-100: #dbeafe;
--accent-300: #93c5fd;
--accent-500: #066fd1;  /* primary */
--accent-600: #0560b8;  /* hover */
--accent-700: #044a8e;  /* pressed */

/* Neutral scale: standard Tailwind slate, leans cool (matches travel/SaaS aesthetic) */
slate-50, slate-100, slate-200, slate-300, slate-400, slate-500, slate-600, slate-700, slate-800, slate-900

/* Semantic */
success: emerald-600
warning: amber-500
danger:  red-600
info:    sky-600
```

**Rationale:** the existing app is already blue; switching to a green or purple accent would break visual continuity for users who've been using this for years. Tabler's `#066fd1` is the most common shade in the live UI today and is what `--tblr-primary` evaluates to in CSS variables you'll see in DevTools.

### Typography

Current state: [`resources/sass/_variables.scss`](resources/sass/_variables.scss) sets `$font-family-sans-serif: 'Nunito', sans-serif;` and `$font-size-base: 0.9rem;`. Tabler's bundled CSS uses `system-ui, -apple-system, ...`. The PDF/Word templates use a mix of `Arial`, `Helvetica`, `OpenSans`, and on the landing page, `SignPainter` (which is not a web-safe font and silently falls back).

**Recommendation:**

- **Sans-serif (UI):** **`Inter`** loaded from a self-hosted woff2. Inter is the de-facto SaaS-dashboard typeface (used by Linear, Notion, Vercel, Stripe). Falls back to `system-ui, -apple-system, "Segoe UI", Roboto, sans-serif`. Nunito (the current choice) is a rounded display face that's fine for marketing but reads softly for dense data work — Linear/Notion-style office apps almost universally choose a neutral grotesque (Inter, IBM Plex Sans, or Geist).
- **Monospace (codes, ids, log output):** `JetBrains Mono` or `IBM Plex Mono`. Used for invoice numbers, tour codes, etc.
- **Type scale:** four sizes maximum.

```
text-xs:   12px / 16px line-height  (timestamps, metadata)
text-sm:   14px / 20px              (body, table cells, form inputs)
text-base: 16px / 24px              (default body, paragraph text)
text-lg:   18px / 28px              (section headings)
text-2xl:  24px / 32px              (page titles)
```

Weight tokens: `400` regular, `500` medium (subheads, table headers, button labels), `600` semibold (page titles). No `700` bold (too heavy for clinical office UI).

### Density spec

Match Notion / Linear sidebar density, not the marketing-page airy spacing of e.g. Vercel's home page:

- 8 px baseline grid (Tailwind `4` = 16 px; use `2`/`3`/`4` for most spacing).
- Form-field height: 36 px (matches Linear, Notion).
- Button height: 32 px (sm) / 36 px (md).
- Table row: 40-48 px.
- Card padding: `p-4` (16 px) for compact, `p-6` (24 px) for primary cards.
- Section spacing: `gap-6` (24 px) inside content areas.
- Modal default width: 480 px / 640 px / 800 px (sm/md/lg).

---

## 6. Migration risk and effort estimate

| Module | Files | BS class hits | Risk | Effort estimate |
|---|---:|---:|---|---|
| `chat/` | 0 | 0 | – | trivial (nothing to migrate) |
| `TMSSupplier/` | 2 | 13 | Low | half day |
| `dashboard/` | 1 | 29 | Low | half day (but layout-adjacent) |
| `comments/` | 5 | 39 | Low | half day |
| `export/` (PDF/Word) | 15 | 53 | **Special** — these are server-side rendered to PDF/Word, do NOT use Tailwind (DomPDF doesn't support modern CSS). Keep as-is or migrate to inline styles only. | 1 day (audit only) |
| `guest_list/` | 2 | 56 | Low | half day (also fix index broken-AJAX bug from AUDIT.md) |
| `driver/`, `bus/` | 9 | 155 | Low | 1 day combined |
| `comparison/` | 3 | 114 | Medium (frontsheet shared with tour show) | 1 day |
| `invoices/`, `accounting/`, `office/` | 27 | 667 | **High** (touches money — coordinate with AUDIT.md backend fixes) | 4-5 days |
| `quotation/`, `clients/` | 10 | 358 | Medium | 2 days |
| Supplier catalog (`hotel/`, `event/`, `guide/`, `restaurant/`, `transfer/`, `flight/`, `cruises/`) | 28 | 1345 | Medium | 4 days (similar templates, batch-migratable) |
| `email/` | 21 | 376 | Medium (still receiving fixes from prior session) | 2 days |
| `task/` | 5 | 268 | Medium (Monday-board view is partial; needs design decision) | 1-2 days |
| `tour_package/` | 8 | 322 | Medium-high | 2 days |
| `tour/` | 6 | 821 | **Highest** (`tour/show.blade.php` is 2700 lines, ~225 BS hits alone) | 4-5 days |
| `TMSClient/` (client portal) | 16 | 431 | High (separate visual identity from staff app — decide if migrating to same widgets) | 3-4 days |
| `scaffold-interface/` (layouts + dashboard widgets) | 37 | 960 | High (the AdminLTE shim lives here; removal touches every page) | 3-4 days |

**Total rough estimate:** ~35-45 working days of focused effort for full migration including widget library construction, manual click-through verification, and Phase 4 cleanup. Front-load the widget library (Phase 2) — 1 week to build the primitives saves time on every subsequent module.

---

## 7. Suggested ordering (overriding the brief's order with risk-balancing)

The brief proposes: Landing → Help → Offices → Tours → Itineraries → Services → Quotations → Guests → Invoices → Billings → Vouchers → Frontsheet → Tasks → Exports.

I recommend a **different order** because:
- Landing is a public-facing template that uses DomPDF-rendered HTML and Bootstrap 4 from a CDN — it shouldn't share the widget library at all. Migrating it first means inventing patterns we'll regret.
- Tours is the highest-traffic page and the worst tech debt — it should be done **last** as the proving ground for the widget library, not first.

Suggested ordering:

1. **Foundation week** (Phase 2): Install Tailwind, configure tokens, build 18 primitive widgets, document them in `widgets/README.md`. No page changes yet.
2. **Small modules first** to validate the widget library on real screens:
   - `comments/` → `guest_list/` → `dashboard/` (1 week)
3. **Supplier catalog batch** (4 days): hotel, event, guide, restaurant, transfer, flight, cruises, bus, driver — almost identical templates, do them together for muscle-memory.
4. **Finance modules** (1 week): invoices, accounting, office, billings, vouchers — coordinate tightly with [AUDIT.md](AUDIT.md) Phase-0 fixes for money columns + permissions.
5. **Quotations + Comparison/Frontsheet + Clients** (3 days).
6. **Email** (2 days) — already received attention in the previous session; finish the migration started there.
7. **Tasks** (2 days).
8. **Tour packages** (2 days).
9. **Tours** including the 2700-line show page (4-5 days). Treat this as a separate, multi-PR effort.
10. **TMSClient portal** (3-4 days) — decide separately whether to use the same widgets or a slimmer client-facing palette.
11. **Landing / Help** (1 day) — Landing stays separate (PDF context), Help is greenfield.
12. **Phase 4 cleanup**: delete Bootstrap CSS, delete AdminLTE shim, delete dead CSS files (1 day).

---

## Open questions for you (please resolve before Phase 2)

1. **Icon library choice — Lucide or Tabler Icons?** Tabler Icons is already shipping with the app (638 hits) and matches the existing visual style; Lucide is the more common SaaS-standard choice and ships as React/Vue/Svelte components. Recommendation: **Tabler Icons**, because (a) it's already in the repo, (b) the staff app already uses it heavily, (c) it has wider coverage than Lucide for travel/business glyphs (passport, route, etc.). Confirm?
2. **Select2 — keep or replace?** Select2 is jQuery-tied (27 views). Replacing it with a Tailwind-native combobox means rebuilding the searchable-select UX. Cheapest path: keep Select2, restyle to match. Cleaner path: replace with a headless combobox (e.g. hand-built, no library) once Phase 2 is shaken out.
3. **DataTables.net — keep or replace?** Same tradeoff. DataTables.net is huge and stylish but server-side-pagination integration is bespoke. Recommendation: **keep DataTables.net**, restyle via a thin Tailwind theme override so we don't rewrite 19 pages' worth of column/sort/filter logic.
4. **Vue / Alpine / jQuery — single answer?** 15 views use Vue (mostly the email inbox and tour show frontsheet). 99% of the rest is jQuery. Tailwind doesn't dictate the JS layer, but the widget library does (e.g. how does `Modal.open()` fire?). Recommendation: introduce **Alpine.js** for new widget interactivity (declarative, attribute-based, plays nice with server-rendered Blade). Leave the existing Vue islands alone for now.
5. **TMSClient portal — same design system?** The client portal has its own visual identity today (different layout, separate CDN Bootstrap 4). Migrate it to the same widgets, or keep it intentionally distinct?
6. **Export PDF/Word templates — out of scope?** `resources/views/export/*.blade.php` is rendered by DomPDF / phpword which don't understand modern CSS (Tailwind utility classes will not compile or render). I propose **excluding the export template tree from the Tailwind migration entirely** — keep them inline-styled. Confirm?
7. **Help module — same question as in [AUDIT.md](AUDIT.md):** does it exist as a scope item, or do we drop it?
8. **Accent color confirmation:** `#066fd1` (Tabler primary) for continuity — confirm or pick a different brand colour?
9. **Font confirmation:** Inter for UI, JetBrains Mono for codes — confirm or veto Nunito-removal?
10. **Component library bar:** the brief says no `shadcn` / `MUI` / `Headless UI`. To be precise: **Headless UI** (the Tailwind-team's unstyled JS primitives for Modal / Combobox / Listbox) is a ~10 KB dependency that saves several days of accessibility work on `Modal`, `Dropdown`, `Tabs`. Is the no-dependency rule absolute, or is Headless UI acceptable as a small foundation layer?
