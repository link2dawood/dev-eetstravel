# Phase 3 — Help Module Migration Plan

**Branch:** `ui/phase3-help` (off `f6e8e8b6` — Phase-2 widget foundation + sidebar fix, on top of `dev`).

## Special note — this module is being **scaffolded**, not migrated

[AUDIT.md → Help](AUDIT.md) section flagged the Help module as **absent** with a single Critical finding:

> *"No Help / Documentation surface anywhere in the application for staff, client, or supplier portals. Either explicitly drop Help from project scope OR scaffold a minimal `HelpController` + `resources/views/help/index.blade.php` route gated by `perm` middleware with a `PermissionsHelpSeeder` and a sidebar entry."*

The user's directive for this phase says *"Apply functional fixes from AUDIT.md for the Help module (critical → major → minor)"* — the one Critical fix is *scaffold or drop*. The scaffold path is being taken here.

Because there is **nothing to migrate**, the brief's clauses that assume existing Bootstrap markup are reinterpreted:

| Brief clause | Action taken |
|---|---|
| *"every page/route under the Help module"* | Listed below (new routes, since none exist) |
| *"every Bootstrap class still present"* | None — all new code is Tailwind / `<x-ui.*>` from line 1 |
| *"Migrate Bootstrap markup to `<x-ui.*>`"* | Build new views with widgets from the start |
| *"Replace any Select2/DataTables"* | None present — N/A |
| *"Wire up every button and icon"* | Strictly enforced on the new views |
| *"Final grep should return 0"* | Confirmed in verification step |

The conventions established in [PHASE3_landing_PLAN.md](PHASE3_landing_PLAN.md) are reused: `<x-ui.*>` widgets from `resources/views/components/ui/`, Tailwind tokens only, no inline `style=`, no `btn-*` / `col-md-*` / `form-control` / `navbar-*` classes, every interactive control wired.

## Product scope

Minimal but useful for a staff app. Three sections on a single page:

1. **Quick links** — pointers to the existing project docs (`AUDIT.md`, `UI_MIGRATION_AUDIT.md`, `CLAUDE.md`, etc.) and the GitHub repo. Renders as a card with one `<x-ui.dropdown-item>`-style row per link.
2. **Frequently asked questions** — six static FAQ entries hard-coded in the view (no DB). Each entry is a `<details><summary>` expandable block styled via Tailwind. No JS framework needed — native HTML interactivity.
3. **Contact support** — a simple form (subject, message) that posts to a new route, validated server-side, and logs the message via `Log::info('help.contact.submitted', …)`. The first iteration deliberately doesn't email anything (avoids a new dependency on a queue + mail config); it just confirms receipt via flash message. A follow-up PR can add a mail job once the team picks an inbox.

This is intentionally lean — Help is shared chrome, not a feature module. Bigger surface (e.g. searchable docs, embedded videos) can be added incrementally without breaking the contract.

## Files added

| File | Purpose |
|---|---|
| `app/Http/Controllers/HelpController.php` | `index()`, `contact()` (POST handler) |
| `app/Http/Requests/HelpContactRequest.php` | FormRequest with validation rules |
| `resources/views/help/index.blade.php` | Help page (quick links + FAQ + contact form) |
| `database/seeds/PermissionsHelpSeeder.php` | Inserts `help.index` + `help.contact` slugs |
| `tests/Feature/HelpControllerTest.php` | Happy + failure-path tests |
| `PHASE3_help_PLAN.md` | This document |

## Files modified

| File | Change |
|---|---|
| `routes/web.php` | Two new routes inside the existing `perm` middleware group |
| `resources/views/scaffold-interface/layouts/tabler-sidebar.blade.php` | One new dropdown item under the existing "Communications" group (matches where users will look — adjacent to Comments/Webmail) |

**Sidebar partial change is intentional and documented in the commit message**, per requirement #8 in the brief.

## Routes added

| Method | URI | Name | Controller | Middleware |
|---|---|---|---|---|
| GET  | `/help`         | `help.index`   | `HelpController@index`   | `web`, `perm` |
| POST | `/help/contact` | `help.contact` | `HelpController@contact` | `web`, `perm` |

No route paths in the rest of the app are touched (constraint #1 in the brief).

## Widget usage (from the existing `<x-ui.*>` library — no widget modifications)

| Page section | Widget |
|---|---|
| Page chrome | `<x-ui.page-header>` (title + breadcrumb + actions slot) |
| Three section panels | `<x-ui.card>` × 3 |
| Form inputs | `<x-ui.form-field>` + `<x-ui.input>` + `<x-ui.textarea>` |
| Submit / cancel buttons | `<x-ui.button>` (variants `primary`, `secondary`) |
| Validation error pill | `<x-ui.badge variant="danger">` for the `:error` slot |
| Each quick-link row | `<x-ui.icon>` + plain anchor (no widget needed) |
| Empty FAQ state (defensive) | `<x-ui.empty-state>` |
| Submit confirmation flash | `<x-ui.toast>` via `session('toast')` (consumed by the layout's existing toast container) |

No widget gaps identified. `WIDGET_GAPS.md` will not be created.

## Permission seeder

`database/seeds/PermissionsHelpSeeder.php` inserts two Spatie permission slugs:

- `help.index` — gate `GET /help`
- `help.contact` — gate `POST /help/contact`

After insertion, both are assigned to the `admin` role and any seeded base role (so every authenticated user can reach Help). This is the conventional pattern used by other `Permissions*Seeder` files (e.g. `PermissionsAnnouncements.php`, `PermissionsActivities.php`).

> **AUDIT.md cross-cutting CC1 / CC2 caveat:** the `PermissionsRequiredMiddleware` fails open on missing-permission exceptions. Until that systemic bug is fixed, Help is reachable even without this seeder. The seeder is still added because it's the correct end state.

## AUDIT.md fixes applied

- ✅ Critical: Help module absent → scaffolded.
- ⏸ AUDIT.md's CC1 (middleware fails open) — Out of scope, would touch the cross-cutting permission middleware that affects every other module. Logged to OUT_OF_SCOPE_FINDINGS.md if it doesn't already exist there.

## Tests planned (`tests/Feature/HelpControllerTest.php`)

| Test | Path covered |
|---|---|
| `unauthenticated_user_is_redirected_to_login` | Failure — auth guard |
| `authenticated_user_can_view_help_index` | Happy — `GET /help` |
| `index_renders_expected_sections` | Happy — assert FAQ + contact + quick links present |
| `index_uses_tabler_layout_with_sidebar` | Regression — assert `tabler-sidebar` partial is rendered |
| `contact_form_requires_subject_and_message` | Failure — server-side validation |
| `contact_form_rejects_oversize_message` | Failure — max length |
| `contact_form_logs_and_flashes_on_success` | Happy — POST `/help/contact` |
| `contact_form_throttles_after_5_per_minute` | Failure — rate limit (abuse guard, prevents spam) |

## Hard constraints — confirmed in plan

| Constraint | Plan |
|---|---|
| No React/Inertia/Livewire/etc. | Plain Blade + Tailwind. No JS framework. |
| Don't rename widgets | `<x-ui.*>` invocation preserved. |
| Don't touch widget files | Read-only consumption. |
| Don't change route paths | Two NEW routes added; nothing existing renamed. |
| Don't add new dependencies | No new composer / npm packages. |
| Don't change business logic outside Landing | Help is a new surface; nothing else touched. |
| One module = one branch | This branch only carries Help work + the prerequisite foundation cherry-picked at branch creation. |
| Sidebar must stay visible | Help link added to existing dropdown; partial change documented in commit message; verified by `index_uses_tabler_layout_with_sidebar` test. |

## Commit chunks

1. `docs(help): PHASE3_help_PLAN.md`
2. `feat(help): scaffold HelpController + routes + permission seeder`
3. `ui(help): build /help index view with Tailwind + widgets`
4. `ui(sidebar): add Help link under Communications`
5. `test(help): feature tests covering happy + failure paths`
