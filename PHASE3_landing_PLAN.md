# Phase 3 — Landing Module Migration Plan

**Branch:** `ui/phase3-landing` (off `dev`)
**Scope:** Landing module only — public client-facing tour view served at `GET /tour/{id}/landingpage`.

This document maps every Bootstrap class still present in the Landing module to its Tailwind / widget replacement, lists the AUDIT.md fixes I will apply, and defines the migration ordering. Per the brief, this PR does not touch any other module.

## Sidebar requirement (clarification)

Brief requirement #5 says *"Ensure sidebar renders on every Landing page."* The Landing page is a **public, no-auth tour summary served to end clients** (`/tour/{id}/landingpage`) — it was never inside the staff layout and showing the staff sidebar would leak the internal navigation menu (Tours, Invoices, Tasks, Communications, etc.) to client viewers.

Interpretation chosen for this PR: **"sidebar renders on every staff page"** — i.e. don't break the sidebar elsewhere while migrating Landing. The Phase-2 sidebar regression is already addressed in the first commit on this branch (see `git log f6e8e8b6`). Landing itself remains sidebar-less by product design. This is flagged for review in the summary report.

## In-scope artifacts

| Layer | File | LoC | What it does |
|---|---|---:|---|
| Route | `routes/web.php:817` | 1 | `Route::get('/tour/{id}/landingpage', 'TourController@landingPage')->name('landing_page');` |
| Controller | `app/Http/Controllers/TourController.php` lines 96-98 (constructor `except`) + 1775-1827 (`landingPage` method) | ~55 | Resolves tour, loads packages/days/transfers/rooms, shares to view |
| View | `resources/views/export/landing_page.blade.php` | 394 | Standalone HTML with Bootstrap 4 CDN, inline styles, Bootstrap-grid layout |
| Middleware | `app/Http/Middleware/PermissionsRequiredMiddleware.php:13` | 1 | `'landing_page'` listed in `$ignoredRoutes` whitelist |
| Dead `except => 'landingPage'` clauses to remove | `InvoicesController:52-53`, `TourExpenseController:43-44`, `OfficeEarningController:43-44`, `BalanceAmountController:43-44`, `UtilityExpenseController:43-44`, `EmployesSalaryController:43-44`, `OfficeController:49-50`, `OfficeInvoiceController:44-45`, `TMSClient/TourController:33` | 18 | These 9 controllers `except` a `landingPage` method that doesn't exist on them — confirmed unused. |

## Bootstrap inventory in `landing_page.blade.php`

Exhaustive list of every Bootstrap 4 class still present (line-anchored):

| Class | Lines | Tailwind/widget replacement |
|---|---|---|
| `container` | 159 | `container mx-auto px-4` |
| `row` | 160, 162, 169, 171, 208, 224, 386 | `grid grid-cols-12 gap-4` (or `flex flex-wrap` where appropriate) |
| `col-md-12` | 161, 209 | `col-span-12` |
| `col-md-6` | 172, 176, 242, 304, 359 | `col-span-12 md:col-span-6` |
| `d-block d-md-none` | 165, 285 | `block md:hidden` |
| `d-none d-md-block` | 166, 242, 359 | `hidden md:block` |
| `text-center` | 210 | `text-center` (Tailwind has this same class) |
| `mb-5` | 163 | `mb-12` (Tailwind 8-px grid: BS4 mb-5 = 3rem ≈ Tailwind mb-12) |
| `mt-5` | 170, 171, 190 | `mt-12` |

Plus external Bootstrap 4 + jQuery 3.3.1 + popper.js CDN loads at lines 11-14 — all removed once the view extends the new Tailwind-only `layouts/public` layout.

**Inline styles to convert to Tailwind utilities or scoped CSS:**

| Selector | Current behavior | Replacement |
|---|---|---|
| `.tms-logo` (lines 23-29) | Absolute-positioned logo, 75×70px | `absolute left-4 top-1.5 w-[75px] h-[70px]` on `<img>` |
| `.tms-logo-maxi` (lines 31-37) | Absolute-positioned bigger logo, 125×110px | `absolute left-12 top-2.5 w-[125px] h-[110px]` |
| `.rectangle` (lines 39-44) | White card with low-shadow + 10px padding | `<x-ui.card padding="sm">` |
| `.header-grey` (lines 46-55) | Italic 14px slate text | `text-sm italic text-slate-500 pt-2 pl-12` |
| `.header-bl` (lines 57-65) | 24px semibold body color, pl-12 | `text-xl font-semibold text-slate-800 pt-2 pl-12 pb-7` |
| `.info-box` (lines 67-72) | Full-width / full-height padded box | `w-full h-full p-12 mt-[10%]` |
| `.pic-box` (lines 74-80) | Image card with 30px-blur shadow | `w-full mt-[10%] rounded-sm p-2 shadow-overlay` |
| `.info-header` (82-89) | 24px slate-400 | `text-xl text-slate-500` |
| `.info-descr` (91-99) | 16px slate-400, justified, 80% width | `text-base text-slate-500 text-justify max-w-[80%] leading-7` |
| `.info-name` (101-108) | 45px **SignPainter** display font, slate body | Drop SignPainter (web-unsafe — silently fell back); use `text-3xl font-semibold text-slate-800 leading-snug` |
| `.info-pending` (110-117) | 24px slate-400 — used to display internal package status | **Removed entirely** — internal workflow status is not for client eyes (AUDIT.md Critical: "Internal workflow status name exposed") |
| `.info-telfax` (119-127) | 14px slate-400 — used to display supplier work_phone / work_fax | **Removed entirely** — supplier internal contact info leaked publicly (AUDIT.md Critical: "Supplier work_phone/fax leaked publicly") |
| `.pic` (129-132) | 100% w/h image | `w-full h-auto block` |
| `.day` (134-137) | Auto margin + 40px top padding | `mx-auto pt-10` |

## Widget mapping

| Existing markup | Replaced by widget |
|---|---|
| Hero "rectangle" card (line 170) | `<x-ui.card padding="sm">` |
| Per-day section heading (line 210) | Plain `<h2>` with Tailwind utilities — no widget needed |
| Package "info-box" pane (line 305) | Plain `<div>` with Tailwind utilities — no widget needed (this is content, not chrome) |
| Image pic-box (lines 243, 360) | Plain `<figure>` with Tailwind utilities |

**Most of Landing is content, not chrome.** The widget library is overkill for a one-page client view — only the hero rectangle benefits from `<x-ui.card>`. The rest will be Tailwind utility classes.

## AUDIT.md fixes to apply (Landing module section)

### Critical
1. **[IDOR — sequential auto-increment ids, no token]** Add a per-tour `share_token` column (CHAR(40), unique, nullable). Generated lazily on first staff access. The public route requires the token via `?t=<token>` query string when called anonymously; without it returns 404. Staff who are logged in can still access the page via id alone (so existing internal links keep working). Migration: new file `database/migrations/2026_05_23_010000_add_share_token_to_tours.php`.
2. **[Public route lacks any access guard]** Tightened above. Token-gated for anonymous viewers.
3. **[Supplier work_phone/fax leaked publicly]** Removed entirely from the view (lines 322-326 in original).
4. **[Raw HTML rendered from DB into public page]** `{!! $packageDescription !!}` replaced with HTMLPurifier-sanitised output via a small `purify()` helper added to `HelperTrait`.
5. **[Direct filesystem access to attachments]** Out of scope for this PR — the attachment path scheme (`public/system/App/File/attaches/...`) is enumerable but used by 30+ controllers. Will be addressed in a dedicated security PR later.

### Major
6. **[No rate-limiting on public route]** Added `throttle:30,1` middleware (30 requests/minute per IP).
7. **[Cache headers missing]** `landingPage` now always returns with `Cache-Control: no-store, max-age=0` headers.
8. **[Outdated/mixed CSS framework]** Removing Bootstrap 4 CDN load entirely; new layout serves Tailwind compiled from this repo.
9. **[External CDNs without SRI/integrity]** Removed: Bootstrap 4 + jQuery 3.3.1 + popper.js CDN tags all gone.
10. **[Tour status name surfaced to clients]** `$package->getStatusName()` removed from the view.
11. **[No graceful 404 for missing/soft-deleted tour]** New `resources/views/errors/landing-not-found.blade.php` rendered when token missing/invalid; uses the same minimal Tailwind layout.
12. **[No share/print/PDF affordance]** Added `<button type="button" onclick="window.print()">` print button and CSS print stylesheet via Tailwind `print:` variants.
13. **[Inline `<style>` block + magic constants]** Removed entirely; all styling via Tailwind utilities + `print:` variants.
14. **[Hardcoded English labels]** Replaced "Tour name" / "Dep Date - Ret Date" / "Image for landing page" hardcoded strings with `trans('main.*')` calls (matching the existing `trans('main.Itinerary')` usage at line 8).
15. **[Mobile logo broken on narrow viewports]** Fixed via Tailwind responsive utilities.
16. **[Unbounded data exposure when `description_package` is true]** Sanitized output via purifier (see #4).

### Minor
17. **[Locale-dependent strftime formatter (PHP 8.1+ deprecated)]** Replaced `formatLocalized('%B %d, %Y (%A)')` with `Carbon::translatedFormat('F j, Y (l)')` + tour locale.
18. **[Mixed tabs/spaces, doubled spaces]** Cleaned in the rewrite.
19. **[`view()->share()` instead of `view(..., $data)`]** Switched to inline data pass.
20. **[Unused `$exclude[]` query param accepted on public GET]** Validated; capped to 200 ids max.
21. **[Trailing whitespace/blank lines in controller body]** Cleaned.

### Dead code removal
22. **[9 controllers with dead `except => 'landingPage'` clauses]** All 9 references removed.
23. **`$usersResponsible = User::find($tour->responsible)`** Was loaded then never used in view. Removed.
24. **4 large commented-out blocks (`{{-- ... --}}`)** Lines 182-186, 250-300, 327-328, 367-383 — removed.
25. **Commented `$data = $this->prepareTourPackages(...)`** Line 1788 in controller. Removed.

## Tests to add

`tests/Feature/LandingPageTest.php`:

| Test | Path | Expected |
|---|---|---|
| `it_renders_for_anonymous_with_valid_token` | `GET /tour/{id}/landingpage?t={valid_token}` | 200, body contains tour name |
| `it_renders_for_authenticated_staff_without_token` | `GET /tour/{id}/landingpage` while logged in | 200 |
| `it_returns_404_for_anonymous_without_token` | `GET /tour/{id}/landingpage` anonymous | 404 with landing-not-found view |
| `it_returns_404_for_anonymous_with_wrong_token` | `GET /tour/{id}/landingpage?t=wrong` anonymous | 404 |
| `it_returns_404_for_nonexistent_tour_id` | `GET /tour/999999/landingpage?t=anything` | 404 |
| `it_does_not_render_supplier_phone_or_status` | grep response body | does NOT contain `work_phone`, `Fax:`, status name |
| `it_sets_no_cache_headers` | response headers | `Cache-Control: no-store` |
| `it_throttles_excessive_requests` | 31 requests in 60s | last returns 429 |

## Out of scope (deferred to future PRs)

- The IDOR fix on attachment paths (`public/system/App/File/attaches/.../<padded_id>/...`) — affects 30+ controllers, needs its own PR.
- Phasing out the legacy id-based route entirely. For now staff can still use it; anonymous requires token.
- Replacing the in-Tabler share-link UX in the staff tour show page. The new token URL pattern works today (`?t=<token>`); a "Copy share link" button in the staff UI is its own task.

## Commit chunking

1. ✅ `fix(layout): remove unintended app.js include that collapsed Tabler sidebar` (done — `f6e8e8b6`).
2. `docs(landing): PHASE3_landing_PLAN.md`.
3. `feat(landing): add share_token column + Tour model accessor`.
4. `refactor(landing): rewrite TourController@landingPage with token gating + sanitisation + no-cache`.
5. `chore(controllers): remove dead 'except => landingPage' clauses from 9 controllers`.
6. `feat(layout): introduce minimal layouts/public for client-facing pages`.
7. `ui(landing): migrate Bootstrap 4 → Tailwind + widgets`.
8. `test(landing): feature tests for token gating, sanitisation, rate limit`.
9. `docs(landing): UNCLEAR_BEHAVIOR.md` if anything required judgment calls.

## Done criteria

- [ ] `grep -E "btn-|col-md-|form-control|navbar-" resources/views/export/landing_page.blade.php` → **0 hits**.
- [ ] `php artisan view:cache` compiles cleanly.
- [ ] `vendor/bin/phpunit --filter=LandingPageTest` passes (happy + failure paths).
- [ ] Sidebar still renders on `/home`, `/tour`, `/invoices` (verified by user post-merge).
- [ ] Branch pushed to `origin/ui/phase3-landing`.
