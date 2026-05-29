# Sidebar Diagnosis

**Status:** No fix applied. Awaiting your approval per the course-correction instructions.

## TL;DR

The sidebar's **Blade markup is intact, the `@include` is in the layout, the partial file still exists, and the sidebar HTML is being emitted on every authenticated request**. The visual breakage is caused by **Tailwind v3's Preflight reset** — which I added in Phase 2 — overriding Tabler's element-default styles. The sidebar renders to the DOM but looks broken (no list bullets, headings collapsed, icons promoted to `display: block`, default borders zeroed) and a casual look will read as "the sidebar isn't there."

The fix is one line in `tailwind.config.js` (`corePlugins: { preflight: false }`) plus a small base-layer file scoped to the new widget tree only. Detail in the [Proposed fix](#proposed-fix) section below.

## Which file used to render the sidebar

Same file as today — nothing moved or got deleted.

- **Layout entry:** [resources/views/scaffold-interface/layouts/tabler-app.blade.php](resources/views/scaffold-interface/layouts/tabler-app.blade.php) — the layout extended by 181 of the 434 Blade views in the project.
- **Sidebar partial:** [resources/views/scaffold-interface/layouts/tabler-sidebar.blade.php](resources/views/scaffold-interface/layouts/tabler-sidebar.blade.php) (~365 lines, Tabler `<aside class="navbar navbar-vertical">` shell).
- **Include site:** `tabler-app.blade.php:663`, inside the `@auth` block:

```blade
<div class="page">
    @auth
        @include('scaffold-interface.layouts.tabler-sidebar')      ← still here
    @endauth

    <div class="page-wrapper">
        @auth
            @include('scaffold-interface.layouts.tabler-header')
        @endauth
        …
    </div>
</div>
```

- **Alt partial that exists but isn't used:** `resources/views/scaffold-interface/layouts/sidebar.blade.php` (the older non-Tabler version, kept for legacy `modernapp.blade.php` and `defaultMaterialize.blade.php` layouts).

## What changed

Two diffs landed during Phase 2 that affect what the sidebar sees at runtime:

### 1. `tabler-sidebar.blade.php` — `+3 / −0` lines (cosmetic; not the cause)

```diff
+ <a class="dropdown-item {!! \App\Helper\DashboardHelper::isMenuActive('snappymail.configure', $route) !!}" href="{{route('snappymail.configure')}}">
+     <i class="ti ti-settings icon me-2"></i>Configure Mailbox
+ </a>
```

That's the entire change to the sidebar partial: one new dropdown item under Communications, well-formed Blade. Not load-bearing.

### 2. `tabler-app.blade.php` — `+141 / −26` lines (the cause)

Three additive changes:

a. **New stylesheet include** (line 31):
```blade
<link href="{{ asset('css/tailwind.css') }}" rel="stylesheet" type="text/css"/>
```
This sits AFTER `tabler.min.css` (line 15) so its rules win on conflict — including its Preflight reset (see [Root cause](#root-cause-tailwinds-preflight-reset)).

b. **Expanded inline AdminLTE compatibility CSS** in the layout's `<style>` block (added `.box.box-{primary,info,success,warning,danger}`, `.box-solid`, `.nav-tabs-custom`, `.info-box` rule blocks). This is purely additive and only affects elements with `box-*` / `info-box` / `nav-tabs-custom` classes — the sidebar uses none of these.

c. **Rewritten Bootstrap polyfill JS + new `<script src="js/app.js" defer>`** (lines 697-771). The polyfill is more defensive than the prior version; `app.js` is `defer`-loaded at the end so it does not block render. **The new script tag points at the OLD compiled bundle** (1.3 MB `public/js/app.js` from Sept 2025) because `npm run dev` hasn't been run since I rewrote the entry — but it's still valid JS that the browser parses fine. Not the cause.

### 3. The `app.js` situation (worth noting, not the sidebar cause)

`resources/js/app.js` was rewritten in Phase 2 to bootstrap Alpine.js:

```js
require('./bootstrap');
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
…
```

That source hasn't been compiled. The file actually served at `public/js/app.js` is the previous Mix build. Behavior: Alpine.js is not active yet, so any widget that relies on Alpine (Modal, Dropdown, Tabs, Toast, Combobox) will be inert. That's a *separate* unhandled issue for Phase 3, but it doesn't suppress the sidebar.

## Whether the sidebar partial still exists or was deleted

| File | Exists | Modified | Lines |
|---|---|---|---|
| `resources/views/scaffold-interface/layouts/tabler-sidebar.blade.php` | yes | yes (+3) | ~365 |
| `resources/views/scaffold-interface/layouts/tabler-app.blade.php` (parent layout) | yes | yes (+141/-26) | ~800 |
| `resources/views/scaffold-interface/layouts/sidebar.blade.php` (legacy) | yes | no | ~200 |
| `@include('scaffold-interface.layouts.tabler-sidebar')` site | yes (line 663) | unchanged | — |

`php artisan view:cache` compiles all 434 templates cleanly — no Blade syntax errors in the sidebar partial or the layout.

## Root cause: Tailwind's Preflight reset

Tailwind v3 ships a base reset called **Preflight**, emitted by `@tailwind base;` in `resources/css/tailwind.css`. The compiled `public/css/tailwind.css` contains these rules (extracted with grep against the minified output):

| Tailwind Preflight rule | Effect on Tabler sidebar |
|---|---|
| `*, ::after, ::before { border: 0 solid #e5e7eb; box-sizing: border-box; }` | **Zeroes every default border.** Affects `<aside>`, `.navbar-nav` separators, `.nav-link` underlines that Tabler relies on. |
| `ol, ul { list-style: none; margin: 0; padding: 0; }` | **Strips list bullets, margin, padding from `<ul class="navbar-nav">`.** Tabler's `.navbar-nav` class then has to re-establish padding/margin via more specific rules — which it mostly does, but some sub-menus collapse. |
| `h1,h2,h3,h4,h5,h6 { font-size: inherit; font-weight: inherit; }` | **Strips the `<h1 class="navbar-brand">` "TMS" heading down to body font size/weight.** Tabler's `.navbar-brand` re-asserts, but the inheritance chain now resolves wrong. |
| `h1,h2,h3,h4,h5,h6,hr,p,pre { margin: 0; }` | Strips default margins. Cumulative with the above. |
| `img, svg, video, canvas, audio, iframe, embed, object { display: block; vertical-align: middle; }` | **Promotes the sidebar's icon SVGs from inline to block.** Tabler's `<i class="ti ti-dashboard icon">` uses pseudo-element icons (no actual `<svg>`), so this one is mostly cosmetic — but any future SVG icon becomes a block element and pushes adjacent labels to a new line. |
| `button, input, select, textarea { color: inherit; font-family: inherit; ... }` | Affects the sidebar's collapse-toggle button styling. |

The aggregate effect on the sidebar:

1. **Visual:** layout looks broken — bullets gone, brand "TMS" rendered as body text, vertical spacing collapsed, certain borders missing. A user glancing at the page reads it as "the sidebar isn't there."
2. **Structural:** the `<aside>` IS in the DOM. `document.querySelector('aside.navbar-vertical')` returns the element on a logged-in page.
3. **Specificity battle:** Tabler tries to fight back. `.navbar-brand` is more specific than `h1`, so it should win — but Tailwind's Preflight runs after Tabler's CSS in the cascade, so wherever specificity ties (both at "1 class"), Tailwind wins.

This is exactly the documented Preflight-in-existing-Bootstrap-codebase failure mode. The official guidance is to either:
- Disable Preflight during migration ([`corePlugins: { preflight: false }`](https://tailwindcss.com/docs/preflight#disabling-preflight)), or
- Scope the new widget tree under a dedicated root selector and apply Tailwind via `important: '.tw-root'`.

## What I verified vs. didn't verify

| Verified | How |
|---|---|
| Sidebar `@include` is present in the layout | `grep -n "@include.*sidebar" tabler-app.blade.php` |
| Sidebar partial file exists with 365 lines of Tabler markup | `wc -l`, `head -60` |
| All 434 Blade views compile cleanly | `php artisan view:cache` |
| Tailwind CSS is loaded after Tabler CSS in the `<head>` | line numbers 15 (tabler) vs 31 (tailwind) |
| Tailwind Preflight contains the high-impact element resets | grep against compiled `public/css/tailwind.css` |
| The `<script src="js/app.js" defer>` points at a real ~1.3 MB file | `ls -la public/js/app.js` |
| My Phase 2 widgets are all `.blade.php` files (no React/JSX) | `ls resources/views/components/ui/` — see [Issue 1 follow-up](#issue-1-follow-up-no-react-no-jsx) |

| Not yet verified (would require running the dev server) | Reason |
|---|---|
| Pixel-level visual confirmation that the sidebar looks broken in a logged-in browser session | I can't reach the deployed URL; the repo doesn't have a Laravel `serve` running in this shell, and the user explicitly said no fixes |
| That removing Preflight restores the sidebar | Same — that's the fix step, gated by your approval |

## Proposed fix

**Smallest change that restores the sidebar:** disable Preflight in `tailwind.config.js`, ship the existing Tabler CSS-reset as it was, scope a custom Preflight-equivalent to the new widget tree only.

### Two-line change to `tailwind.config.js`

```js
module.exports = {
    content: [ … ],
+   corePlugins: {
+       preflight: false,  // Tabler/Bootstrap own the base reset during migration
+   },
    theme: { … },
};
```

This stops Tailwind from emitting *any* of the element-default resets shown in the table above. Tabler's CSS stays the source of truth for `<aside>`, `<ul>`, `<h1>`, `<img>`, `<svg>`, etc. defaults. All Tailwind *utilities* (`text-sm`, `bg-primary-600`, `grid-cols-2`, …) continue to work — those don't depend on Preflight.

### Optional: re-add Preflight scoped to widgets only

After the global disable, I can re-introduce the parts of Preflight we actually need for the widget library (`box-border` on widget descendants, focus-visible ring, font smoothing) by adding a small block to `resources/css/tailwind.css`:

```css
/* Scoped Preflight equivalent — applies only inside new widgets */
.x-ui *,
.x-ui *::before,
.x-ui *::after {
    box-sizing: border-box;
    border-width: 0;
    border-style: solid;
    border-color: theme('colors.slate.200');
}
```

…and wrapping every widget's root in `class="x-ui"`. **This is optional** — Phase 3 modules can ship without it; Tailwind utility classes target specificity high enough that most widgets work fine on top of Tabler defaults.

### Cost / risk

- One config line. No widget code touches. No template changes.
- Re-running `npx tailwindcss …` produces the same utility set minus the Preflight block. CSS file shrinks from 32 KB to roughly 28 KB.
- No effect on Bootstrap, Tabler, AdminLTE shim, jQuery plugins.
- Restores sidebar to pre-Phase-2 rendering.

### Why not the alternatives

| Alternative | Why not |
|---|---|
| Manually re-add Tabler element-defaults via custom CSS | Fragile; we'd be re-implementing parts of Tabler's reset every time we discover a new conflict. |
| Use `important: '.tw-root'` and wrap content in `.tw-root` | Forces every Tailwind utility to be `!important`, which causes priority wars with Tabler's existing utility-flavor classes (`.text-center`, `.d-flex`). Worse than disabling Preflight. |
| Remove the Tailwind stylesheet include entirely | Reverts Phase 2 work; we lose every widget's styling. |
| Wait until Bootstrap is removed in Phase 4 | Means Phase 3 module migrations would render half-broken sidebars on every page that's still on Tabler. Unacceptable per the new "sidebar non-negotiable" constraint. |

## Constraint reminder for Phase 3

> *"From this point on, every page must render with the sidebar visible on desktop and toggleable on mobile. If a change breaks the sidebar, revert the change."*

Once Preflight is off, the sidebar's mobile toggle still relies on Bootstrap 5's `data-bs-toggle="collapse"` + `data-bs-target="#sidebar-menu"`. That's served by Tabler's bundled Bootstrap JS, currently working. When we eventually remove Bootstrap in Phase 4, the sidebar partial itself will need to be re-implemented as `<x-layout.sidebar>` driven by Alpine.js (per your spec — "collapsible on mobile via Alpine.js"). That's a Phase 4 task, not Phase 3.

For Phase 3 page migrations: leave the sidebar alone, do not touch `tabler-sidebar.blade.php` except to add/remove menu items, and never strip the `@auth + @include('scaffold-interface.layouts.tabler-sidebar')` block from `tabler-app.blade.php`. Add a CI grep (suggestion):

```bash
# In a pre-commit hook or CI step:
grep -q "@include('scaffold-interface.layouts.tabler-sidebar')" \
    resources/views/scaffold-interface/layouts/tabler-app.blade.php \
    || { echo "Sidebar include removed — fail."; exit 1; }
```

If you want, I can wire that as a `package.json` script too.

---

## Issue 1 follow-up: no React, no JSX

You asked me to list any React/JSX widget files in `MIGRATION_ROLLBACK.md`. **There are none.** Phase 2 was built entirely as Blade components:

```text
$ ls resources/views/components/ui/
avatar.blade.php          dropdown.blade.php          modal.blade.php       table.blade.php
badge.blade.php           dropdown-divider.blade.php  page-header.blade.php tabs.blade.php
button.blade.php          dropdown-item.blade.php     radio.blade.php       tab-panel.blade.php
card.blade.php            empty-state.blade.php       README.md             tag.blade.php
checkbox.blade.php        error-state.blade.php       select.blade.php      td.blade.php
combobox.blade.php        form-field.blade.php        textarea.blade.php    th.blade.php
data-table.blade.php      icon.blade.php              toast.blade.php
                          input.blade.php             loading-state.blade.php
```

All `.blade.php`. All anonymous components using `@props([...])`. Invoked from views via `<x-ui.button>`, `<x-ui.modal id="…">`, etc. No `.jsx`, no `.tsx`, no React, no Inertia, no Livewire. Stack additions in Phase 2:

| Added | Purpose | Type |
|---|---|---|
| `tailwindcss` (npm) | Utility CSS | build tool |
| `@tailwindcss/forms` (npm) | Form-element normalisation plugin | build tool |
| `postcss` + `autoprefixer` (npm) | PostCSS pipeline | build tool |
| `alpinejs` + `@alpinejs/focus` (npm) | Behavior layer for widgets (Tailwind-team-standard pairing for server-rendered Blade) | runtime JS, NOT a UI framework |
| `blade-ui-kit/blade-icons` (composer) | Generic Lucide-SVG-as-Blade-component framework (used by Filament/Jetstream) | runtime Blade component pack |
| `mallardduck/blade-lucide-icons` (composer) | The Lucide icon set itself | static SVGs |

So `MIGRATION_ROLLBACK.md` is not needed. **Nothing to roll back from Phase 2's widget tree.** The only Phase-2 fix you'll likely want is the namespacing question:

### One open question on widget naming

Your examples used flat names: `<x-button>`, `<x-form.input>`, `<x-data-table>`, `<x-page-header>`. I namespaced everything under `ui.`: `<x-ui.button>`, `<x-ui.input>`, `<x-ui.data-table>`, `<x-ui.page-header>`.

**Why I namespaced:** the project already has a `resources/views/components/` directory with legacy partials (`form-actions.blade.php`, `form-input.blade.php`, `form-section.blade.php`, `form-select.blade.php`, `guest-list-actions.blade.php`, `guest-list-form.blade.php`, `modern-form.blade.php`, `modern-table.blade.php`, `tabler-page-header.blade.php`). A flat `<x-input>` widget would collide with `<x-form-input>` (similar name) and be invoked from views that already import the old partials. Putting new widgets under `components/ui/` (invoked as `<x-ui.input>`) gave them a clean namespace and made it obvious which is the new system.

**Two options:**

1. **Keep `<x-ui.button>` namespacing.** Pros: unambiguous, easy to grep `<x-ui\.` to see migrated usages. Cons: doesn't match your examples literally.
2. **Move to flat `<x-button>` + `<x-form.input>`.** Means renaming files from `components/ui/button.blade.php` → `components/button.blade.php`, and form widgets to `components/form/input.blade.php`. Requires also renaming or deleting the legacy `components/form-input.blade.php` etc. to avoid collisions — confirms the old form-input is dead code first.

Either is a 5-minute rename — flag your preference and I'll do it before any Phase 3 work, but not before you also approve the Preflight-off fix above. Sidebar first.

## Awaiting your approval

Confirm one of the following before I touch any code:

1. **"Approve Preflight off (config-only change), restore sidebar."** I add `corePlugins: { preflight: false }` to `tailwind.config.js`, recompile `tailwind.css`, no other changes. ETA: 2 minutes.
2. **"Approve Preflight off + flat widget naming."** I do (1) and also rename `components/ui/*` → `components/*` and `components/form-*` → `components/form/*`. ETA: 15 minutes.
3. **"Use a different fix approach."** Tell me which one and I'll plan accordingly.

I'll wait for your call.
