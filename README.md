# TMS — Tour Management System (eetstravel.com)

End-to-end SaaS for tour operators: quotes, bookings, accounting, supplier/client
portals, document generation, and an integrated webmail. Built on Laravel 8 +
Blade, currently mid-migration from Bootstrap 5 to Tailwind CSS 3.

Production: **dev.eetstravel.com**
Repository: `link2dawood/dev-eetstravel`
Server: `/var/www/html` on Ubuntu — nginx + php-fpm 8.4 + MySQL

---

## Table of contents

- [Quick start](#quick-start)
- [Tech stack](#tech-stack)
- [Architecture](#architecture)
  - [Three portals, one codebase](#three-portals-one-codebase)
  - [Repository pattern](#repository-pattern)
  - [Routes and API surface](#routes-and-api-surface)
  - [Authentication and permissions](#authentication-and-permissions)
- [Modules — A to Z](#modules--a-to-z)
- [UI design system](#ui-design-system)
- [UI migration status (Bootstrap → Tailwind)](#ui-migration-status-bootstrap--tailwind)
- [SnappyMail integration](#snappymail-integration)
- [Conventions and gotchas](#conventions-and-gotchas)
- [Deployment](#deployment)
- [Testing](#testing)
- [Scheduled jobs](#scheduled-jobs)
- [Folder map](#folder-map)

---

## Quick start

```bash
# 1. Backend
composer install
cp .env.example .env             # fill in DB + mail credentials
php artisan key:generate
php artisan migrate              # 150+ migrations in database/migrations/
php artisan db:seed              # seeders live in database/seeds (see note)

# 2. Frontend
npm install
npm run dev                      # one-shot dev build (Laravel Mix / webpack)
npm run watch                    # rebuild on save
npm run prod                     # production build (minified)

# 3. Run
php artisan serve                # http://localhost:8000
```

**Seeder location quirk:** active seeders live in `database/seeds/`, **not**
`database/seeders/`. Both directories exist — the second is mostly empty.

**Cache reset (also exposed via `GET /clear`):**

```bash
php artisan optimize:clear
php artisan view:clear && php artisan config:cache && php artisan route:cache
```

`GET /clear` works without auth — fine in staging, a footgun in prod.

---

## Tech stack

| Layer | Choice | Notes |
|---|---|---|
| Runtime | PHP `^8.0` | Production: PHP 8.4-fpm |
| Framework | Laravel `^8.75` | Eloquent models live at app root (Laravel 7 layout) |
| Database | MySQL | `dev_tms` DB; ~150 migrations |
| Templating | Blade | `resources/views/` |
| CSS (in flight) | Bootstrap 5 → **Tailwind CSS 3** | Page-by-page migration |
| JS | jQuery + DataTables | + bundled Bootstrap-JS for tabs/dropdowns/modals |
| Icons | Lucide via `blade-ui-kit/blade-icons` + `mallardduck/blade-lucide-icons` | + Tabler icons (legacy `ti ti-*`) |
| Calendar | FullCalendar 6.1.15 | `/holidaycalendar` |
| Permissions | `spatie/laravel-permission` | Roles + permission slugs |
| Auth API | Sanctum | Modern `/api/*` endpoints |
| Document gen | DomPDF + PhpWord + PhpSpreadsheet | Vouchers, itineraries, exports |
| Webmail | SnappyMail | Mounted at `/mail`, SSO via `/email/sso` |
| Build | Laravel Mix (webpack) | Outputs `public/css`, `public/js` |
| Tests | PHPUnit 9 | `tests/Unit` + `tests/Feature` |
| HTML sanitisation | HTMLPurifier | via `purify_html()` helper |

`composer.json` has `"minimum-stability": "dev"` — pin new dependencies
explicitly or they may resolve to dev versions.

---

## Architecture

### Three portals, one codebase

Routing in [routes/web.php](routes/web.php) splits into three audiences,
each with its own auth guard and middleware:

#### 1. Staff dashboard (default routes)
- Entry: [`/home`](app/Http/Controllers/ScaffoldInterface/AppController.php) → `ScaffoldInterface\AppController@dashboard`
- Auth: `auth` (standard `User` model)
- Permission gate: `perm` middleware → [PermissionsRequiredMiddleware](app/Http/Middleware/PermissionsRequiredMiddleware.php). Resolves route name → permission slug via Spatie roles.
- Controllers: [app/Http/Controllers/](app/Http/Controllers/)

#### 2. TMS-Client portal (`/TMS-Client/*`)
- Auth: `clientauth` middleware ([clientauth.php](app/Http/Middleware/clientauth.php))
- Guard backed by [`Client`](app/Client.php) model (not `User`)
- Controllers: [app/Http/Controllers/TMSClient/](app/Http/Controllers/TMSClient/)
- External-facing — clients view quotes, approve trips, see invoices.

#### 3. TMS-Supplier portal (`/TMS-Supplier/*`)
- Auth: `supplierauth` middleware ([supplierauth.php](app/Http/Middleware/supplierauth.php))
- Guard backed by `Hotel` / `Restaurant` / etc. models (supplier types)
- Controllers: [app/Http/Controllers/TMSSupplier/](app/Http/Controllers/TMSSupplier/)
- External-facing — suppliers receive booking requests, confirm rooms, upload availability.

### Repository pattern

Domain aggregates use **Contract + EloquentImplementation** pairs:

- Contracts: [app/Repository/Contracts/](app/Repository/Contracts/)
- Implementations: `app/Repository/<Name>Repository/Eloquent<Name>Repository.php`
- Bindings: [app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php)

Aggregates using the pattern: **Tour, Hotel, Guide, Restaurant, Event, Transfer,
Flight, Cruise, Bus, Driver, Client, Task, Chat, Email, TourPackage, PackageMenu,
Settings**.

Controllers type-hint the contract; don't `new` the Eloquent class directly.

### Routes and API surface

| Surface | Path | Auth | Style |
|---|---|---|---|
| Staff web | `/home`, `/tour`, `/clients`, ... | `auth` + `perm` | server-rendered Blade |
| Client portal | `/TMS-Client/*` | `clientauth` | server-rendered Blade |
| Supplier portal | `/TMS-Supplier/*` | `supplierauth` | server-rendered Blade |
| Modern API | `/api/tours`, `/api/clients`, `/api/tasks`, `/api/dashboard` | `auth:sanctum` | JSON |
| Legacy v1 API | `/api/v1/*` | none — internal use | JSON, called by in-app webmail UI |

Routes are registered with the leading-backslash fully-qualified controller
string (`'\App\Http\Controllers\FooController'`) — match that style.

### Authentication and permissions

Three independent guards back the three portals. Each guarded route group is
wrapped in its own middleware.

For staff routes, every group is wrapped in `middleware('perm')`. The middleware
maps `Route::currentRouteName()` to a permission slug; permissions are seeded by
the `Permissions*Seeder` classes in `database/seeds/`.

**When you add a new resource route**, add or update the corresponding
permission seeder — otherwise the route returns 403 for everyone except
super-admins.

In Blade, gate UI with:

```blade
@if (Auth::user()->can('tour.edit'))
    <a href="...">Edit</a>
@endif
```

Common slug families: `tour.*`, `task.*`, `clients.*`, `quotation.*`,
`hotel.*`, `invoices.*`, `accounting.*`, `users.*`, `roles.*`,
`permissions.*`, `settings.*`.

---

## Modules — A to Z

Every module groups a sidebar entry, route(s), controller, model(s), Blade
views, and (where applicable) repository.

### Account / Accounting / Accounts
- **Purpose:** Bank accounts, balance amounts, ledger transactions, tax handling, office earnings.
- **Routes:** `/account`, `/accounting`, `/accounts`, `/transaction`, `/taxes`, `/office_earnings`
- **Controllers:** `AccountController`, `BalanceAmountController`, `OfficeEarningController`, `EmployesSalaryController`
- **Models:** `Account`

### Activities
- **Purpose:** Audit-trail activity feed (who changed what, when).
- **Routes:** `/activities`
- **Controller:** `ActivitiesController`
- **Trait:** `app/Trackable.php` — attach to any model to log create/update/delete.

### Agreements
- **Purpose:** Hotel agreements (negotiated rates per room type per season).
- **Controller:** `AgreementsController`
- **Models:** `HotelAgreements`, `HotelAgreementsRoomTypeHotels`

### Announcements
- **Purpose:** Staff-facing announcement banner.
- **Routes:** `/announcements`
- **Controller:** `AnnouncementController`
- **Model:** `Announcement`

### Attachments
- **Purpose:** File uploads attached to tours, clients, tasks, etc.
- **Controller:** `AttachmentController`
- **Models:** `Attachment`, `Attachmenttype`
- **Storage:** `storage/app/public/uploads/`

### Authentication
- **Routes:** `/login`, `/logout`, `/password/email`, `/password/reset`
- **Controllers:** `app/Http/Controllers/Auth/*`
- **Guards:** `web` (users), `clientauth` (clients), `supplierauth` (suppliers)

### Booking Requests
- **Purpose:** Inbound booking inquiries (from clients / forms).
- **Routes:** `/booking_request`
- **Controller:** `BookingRequestController`

### Buses / Drivers
- **Purpose:** Bus fleet, driver pool, day-by-day bus availability.
- **Routes:** `/bus`, `/driver`
- **Controllers:** `BusController`, `DriverController`
- **Models:** `Bus`, `BusDay`, `Driver`

### Chat
- **Purpose:** Internal team chat (staff-only).
- **Routes:** `/chats`
- **Controller:** `ChatController`
- **Models:** `Chat`, `ChatMessage`

### Clients
- **Purpose:** End-customer / agency records that book tours.
- **Routes:** `/clients` (index, show, create, edit, destroy)
- **Controllers:** `ClientController`, `ClientInvoiceController`
- **Models:** `Client`, `ClientContacts`, `ClientInvoices`
- **Repo:** `App\Repository\Contracts\ClientRepository`

### Comments
- **Purpose:** Threaded comments on tours / tasks / quotations.
- **Routes:** `/comments`
- **Controller:** `CommentController`
- **Model:** `Comment`

### Comparison
- **Purpose:** Side-by-side comparison of supplier offers.
- **Routes:** `/comparison`
- **Controller:** `ComparisonController`
- **Models:** `Comparison`, `ComparisonRow`

### Criteria
- **Purpose:** Searchable supplier filters (star rating, board basis, room amenities, etc.).
- **Routes:** `/criteria`
- **Controller:** `CriteriaController`
- **Models:** `Criteria`, `CriteriaType`

### Cruises
- **Purpose:** Cruise products catalog.
- **Routes:** `/cruises`
- **Controller:** `CruisesController`
- **Model:** `Cruises`

### Currencies / Currency Rates
- **Purpose:** Multi-currency support with periodic rate updates.
- **Routes:** `/currencies`, `/currency_rate`
- **Controllers:** `CurrenciesController`, `CurrencyRateController`
- **Models:** `Currencies`, `CurrencyRate`

### Customers
- **Purpose:** End-traveler records (sit *inside* a client booking).
- **Controller:** `CustomerController`

### Dashboard / Home
- **Routes:** `/home`
- **Controller:** `HomeController`, `ScaffoldInterface\AppController`
- **Service:** `app/Services/DashboardService.php`
- **API:** `Api/DashboardController` for widget data

### Email (in-app webmail, legacy)
- **Purpose:** Old in-app IMAP client (predecessor to SnappyMail). Still used by
  some flows; new mail UI is SnappyMail.
- **Routes:** `/emails`, plus `/api/v1/imap/*`
- **Controller:** `EmailController`, `Api/EmailsController`
- **Library:** `app/Imap/`

### Email Configuration (SnappyMail)
- **Purpose:** Save the user's mailbox credentials so `/mail` SSO works.
- **Routes:** `/email/configure`, `/email/save`, `/email/sso`, `/email/admin`
- **Controller:** `SnappyMailController`
- **Apache mount:** [snappymail-apache.conf](snappymail-apache.conf)
- **SSO key:** `SNAPPYMAIL_SSO_KEY` in `.env`

### Events
- **Purpose:** Bookable events / experiences attached to a tour day.
- **Routes:** `/event`
- **Controller:** `EventController`
- **Model:** `Event`

### Exports / Imports
- **Purpose:** CSV / XLSX / PDF / DOC generation for tours, vouchers, itineraries, invoices.
- **Routes:** `/tour/{id}/export/*`, `/tour/{id}/pdf/*`, `/tour/{id}/doc/*`, `/tour/{id}/html`
- **Controllers:** `ExportController`, `ImportController`
- **Trait:** `app/ExportTrait.php`
- **Vendor:** DomPDF, PhpSpreadsheet, PhpWord

### Files
- **Purpose:** Generic file management (alternative to Attachments for some flows).
- **Controller:** `FileController`
- **Trait:** `app/FileTrait.php`

### Flights
- **Purpose:** Flight schedules / segments attached to a tour.
- **Routes:** `/flight`
- **Controller:** `FlightController`
- **Model:** `Flight`

### gCalendar
- **Purpose:** Google Calendar integration (read-only sync).
- **Routes:** `/gcalendar/*`
- **Controller:** `gCalendarController`
- **API key:** `API_KEY_GOOGLE_CALENDAR` in `.env`

### Guest List / Room List
- **Purpose:** Per-tour list of travelers + their room assignments.
- **Routes:** `/guest_list`, `/roomlist`
- **Controllers:** `GuestListController`, `RoomListController`
- **Model:** `GuestList`

### Guides
- **Purpose:** Tour guide pool (per-language, per-region).
- **Routes:** `/guide`
- **Controller:** `GuideController`
- **Model:** `Guide`

### Help
- **Purpose:** In-app help articles (already migrated to Tailwind).
- **Routes:** `/help`
- **Controller:** `HelpController`

### Holiday Calendar
- **Purpose:** Public holidays per country, used in availability planning.
- **Routes:** `/holidaycalendar`
- **Controller:** `HolidayController`
- **Models:** `Hollydaycalendar`, `Holidaycalendarday`
- **JS:** FullCalendar 6.1.15

### Hotels
- **Purpose:** Hotel catalog: details, contacts, room types, agreements, offers.
- **Routes:** `/hotel`
- **Controller:** `HotelController`
- **Models:** `Hotel`, `HotelContacts`, `HotelRoomTypes`, `HotelAgreements`,
  `HotelAgreementsRoomTypeHotels`, `HotelOffers`
- **Repo:** `App\Repository\Contracts\HotelRepository`

### Invoices
- **Purpose:** Client invoices, office invoices, supplier invoices.
- **Routes:** `/invoices`, `/client_invoices`, `/office_invoices`
- **Controllers:** `InvoicesController`, `ClientInvoiceController`, `OfficeInvoiceController`
- **Models:** `Invoices`, `InvoicesTours`, `ClientInvoices`

### Kontingent
- **Purpose:** Room contingents — pre-booked block allotments at hotels.
- **Controller:** (managed within hotel agreement flow)

### Landing (public)
- **Purpose:** Public landing page for a tour (shareable link).
- **Routes:** `/landing/{slug}`
- **Controller:** part of `TourController` / `ExportController`
- **View:** [resources/views/export/landing_page.blade.php](resources/views/export/landing_page.blade.php) — **Tailwind-migrated.**

### Legend
- **Purpose:** Color-coded legend for tour grids (status colors).
- **Routes:** `/legend`
- **View:** [resources/views/legend/](resources/views/legend/)

### Menu / Package Menu
- **Purpose:** Restaurant menu items per tour package.
- **Routes:** `/menu`
- **Controller:** `MenuController`
- **Repo:** `App\Repository\Contracts\PackageMenuRepository`

### Notifications
- **Purpose:** Bell-icon notifications (new tasks, comments, booking requests).
- **Controller:** `NotificationsController`
- **Broadcast:** Pusher (real-time) OR `MockPusher` no-op (see [AppServiceProvider boot quirks](#appserviceprovider-boot-quirks))

### Offers
- **Purpose:** Current / past offers, current bookings, cancellation policies.
- **Routes:** `/current_offers`, `/past_offers`, `/current_bookings`, `/cancellation_policies`
- **Controller:** `OfferController`

### Office Management
- **Purpose:** Multi-office setup (each office has own branding, currency, invoices).
- **Routes:** `/office`, `/office_earnings`, `/office_invoices`
- **Controllers:** `OfficeController`, `OfficeEarningController`, `OfficeInvoiceController`, sub-controllers in `app/Http/Controllers/OfficeManagement/`
- **Row-level isolation:** every domain row carries `office_id`.

### Profile
- **Purpose:** Logged-in user's own profile + password change.
- **Routes:** `/profile`
- **Controller:** `ProfileController`

### Quotations
- **Purpose:** Pre-tour quote builder. Toggle to convert into an active tour.
- **Routes:** `/quotation`
- **Controller:** `QuotationController`
- **Model:** within Tour aggregate

### Rates
- **Purpose:** Generic rate management (used by various supplier types).
- **Routes:** `/rate`
- **Controller:** `RateController`

### Reporting
- **Purpose:** Aggregate reports: tours by month, revenue per office, etc.
- **Routes:** `/reporting`
- **Controller:** `ReportingController`

### Restaurants
- **Purpose:** Restaurant catalog with menus.
- **Routes:** `/restaurant`
- **Controller:** `RestaurantController`
- **Model:** `Restaurant`

### Room Types
- **Purpose:** SIN / DBL / TPL / HPP etc. — typed room categories shared across hotels.
- **Routes:** `/room_types`
- **Controller:** `RoomTypesController`

### Roles / Permissions / Users
- **Purpose:** Spatie-backed RBAC admin.
- **Routes:** `/users`, `/roles`, `/permissions`
- **Controllers:** `ScaffoldInterface/UsersController` etc.

### Scaffold Interface
- **Purpose:** Admin scaffolding bundled when the project was generated.
- **Path:** [app/Http/Controllers/ScaffoldInterface/](app/Http/Controllers/ScaffoldInterface/)
- **Views:** [resources/views/scaffold-interface/](resources/views/scaffold-interface/)

### Seasons
- **Purpose:** Date-range season definitions (high / low / shoulder).
- **Routes:** `/season`
- **Controller:** `SeasonsController`
- **Console:** `season:import` (runs every minute)

### Services
- **Purpose:** Generic add-on services on a tour day.
- **Routes:** `/service`
- **Controller:** `ServicesController`

### Settings
- **Purpose:** Application-wide settings (key/value store).
- **Routes:** `/settings`
- **Controller:** `SettingController`
- **Repo:** `App\Repository\Contracts\SettingsRepository`

### SnappyMail
- **Purpose:** Modern webmail UI mounted at `/mail`.
- **Routes:** `/mail/*` (Apache alias)
- **Controller:** `SnappyMailController` (SSO, configure, admin)
- See [SnappyMail integration](#snappymail-integration) below.

### Status
- **Purpose:** Tour / task / quote status flow management.
- **Routes:** `/status`
- **Controller:** `StatusController`

### Supplier Search
- **Purpose:** Cross-catalog search across hotels / restaurants / etc.
- **Routes:** `/supplier_search`
- **Controller:** `SupplierSearchController`

### Taxes
- **Purpose:** VAT / tax setup per office / per service type.
- **Routes:** `/taxes`
- **Controller:** (part of `AccountController` family)

### Tasks
- **Purpose:** Internal task tracking. Each task can link to a tour / client.
- **Routes:** `/task`
- **Controller:** `TaskController`
- **Repo:** `App\Repository\Contracts\TaskRepository`
- **Console:** `task:deadline` (daily reminder emails)

### Templates
- **Purpose:** Reusable document templates (voucher, itinerary, email).
- **Routes:** `/templates`

### Tours (the core)
- **Purpose:** A single trip / tour: itinerary, services, guests, invoices, billing.
- **Routes:** `/tour` (index, show, create, edit, destroy, export, voucher, itinerary, landing)
- **Controllers:** `TourController`, plus `TourControllerImproved.php` (unwired refactor — DO NOT EDIT, see [Conventions and gotchas](#conventions-and-gotchas))
- **Models:** `Tour`, plus large supporting set (TourDay, TourPackage, etc.)
- **Service:** `app/Services/TourService.php` — wraps tour creation in a DB transaction including `tour_days`, responsible-user sync, status setup. **Use the service, don't `Tour::create` from controllers.**
- **Repo:** `App\Repository\Contracts\TourRepository`
- **Console:** `checktourpackage:status` (daily)

### Tour Packages
- **Purpose:** Reusable package definitions that snap together a tour.
- **Routes:** `/tour_package`
- **Repo:** `App\Repository\Contracts\TourPackageRepository`

### Transactions
- **Purpose:** Money movements: client payments, supplier payouts, refunds.
- **Routes:** `/transaction`
- **Controller:** part of `AccountController` family

### Transfers
- **Purpose:** Point-to-point transfer suppliers (airport pickups, transit transfers).
- **Routes:** `/transfer`
- **Controller:** `TransferController`

### TMS-Client (external portal)
- **Purpose:** Client-facing booking + tour view + invoice access.
- **Path:** `/TMS-Client/*`
- **Controllers:** [app/Http/Controllers/TMSClient/](app/Http/Controllers/TMSClient/)
- **Views:** [resources/views/TMSClient/](resources/views/TMSClient/)

### TMS-Supplier (external portal)
- **Purpose:** Supplier-facing booking-request inbox + availability uploads.
- **Path:** `/TMS-Supplier/*`
- **Controllers:** [app/Http/Controllers/TMSSupplier/](app/Http/Controllers/TMSSupplier/)
- **Views:** [resources/views/TMSSupplier/](resources/views/TMSSupplier/)

---

## UI design system

### Design tokens

Defined in [tailwind.config.js](tailwind.config.js):

| Token | Value |
|---|---|
| Font (sans) | Inter, system-ui |
| Font (mono) | JetBrains Mono |
| Sizes | xs / sm / base / lg / xl / 2xl only |
| Weights | 400 / 500 / 600 only |
| Spacing | 8px baseline grid |
| Primary | teal-600 (`#0d9488`) |
| Semantic | success / warning / danger / info — each with 50 / 600 / 700 |
| Border radius | sm 4px / DEFAULT 6px / md 8px / lg 12px |
| Shadows | subtle / card / overlay — single low layer, no double-shadow |
| Container | `2xl: 1440px` (narrower than Tailwind default) |
| Icons | Lucide (via blade-lucide-icons) |

### Tailwind ↔ Bootstrap coexistence (Phase 3)

**Preflight is OFF.** Tabler / Bootstrap owns the base element reset; Tailwind
contributes utilities only (`flex`, `grid`, `gap-4`, `bg-primary-600`, ...).
This is intentional and stays this way until the last Bootstrap-rendered page
is gone (Phase 4). See the comment block in
[tailwind.config.js](tailwind.config.js) for the full root-cause analysis.

**`visibility` plugin is OFF.** Tailwind's `.collapse { visibility: collapse }`
collides with Bootstrap's `.collapse` component (used by the sidebar).
Tailwind wins on source order, hiding the sidebar. Lost: `.visible` / `.invisible`
utilities — no widget uses them.

**With Preflight off, `<ol>` and `<ul>` keep browser-default decimal / disc
markers.** Always add `list-none pl-0 m-0` to lists in Tailwind pages.

### Widget library (`<x-ui.*>`)

Original, in-house, Blade-only — **no third-party UI kit is copied or licensed.**
All widgets in [resources/views/components/ui/](resources/views/components/ui/).

Widgets currently shipping:

| Widget | Purpose |
|---|---|
| `<x-ui.button>` | Primary / secondary / ghost / danger button |
| `<x-ui.icon>` | Lucide icon wrapper |
| `<x-ui.page-header>` | Breadcrumb + title + actions |
| `<x-ui.card>` | Surface container |
| `<x-ui.stat>` | KPI stat tile (number + label + delta) |
| `<x-ui.badge>` | Status pill |
| `<x-ui.empty-state>` | Empty list / no-results illustration block |
| `<x-ui.toolbar>` | Search / filters bar above tables |
| `<x-ui.input>` | Text input (uses `@tailwindcss/forms` class strategy) |
| `<x-ui.select>` | Native select (Tailwind-styled) |
| `<x-ui.modal>` | Headless Tailwind modal (vanilla JS) |
| (planned) `<x-ui.table>`, `<x-ui.pagination>`, `<x-ui.tabs>`, `<x-ui.dropdown>` |

The widget library expands as modules need new patterns. **Do not rename
widgets** — convention is `x-ui.<lowercase-name>`.

### Responsive strategy (mobile-first)

Tailwind breakpoints used in this project:

| Prefix | Min width | Use for |
|---|---|---|
| (none) | mobile | Default styles |
| `sm:` | 640px | Larger phones / small tablets |
| `md:` | 768px | Tablets |
| `lg:` | 1024px | Small laptops |
| `xl:` | 1280px | Desktops |
| `2xl:` | 1440px | Wide desktops |

Patterns to use:

```blade
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">  {{-- KPI strip --}}
<div class="flex flex-col sm:flex-row gap-2">                        {{-- Stacked → row --}}
<table class="hidden md:table">                                      {{-- Desktop table --}}
<div class="md:hidden space-y-2">                                    {{-- Mobile card list --}}
```

Data tables get a horizontal scroll wrapper:

```blade
<div class="overflow-x-auto">
    <table class="min-w-full">...</table>
</div>
```

### JS strategy

- Alpine.js is **planned** but not compiled in this phase.
- For interactive widgets in this phase, use **vanilla JS** or **Bootstrap's bundled JS** (`data-bs-toggle="modal"`, `data-bs-toggle="tab"`, `data-bs-toggle="dropdown"`).
- When wrapping a Bootstrap component in Tailwind chrome, **preserve every CSS hook and JS handler** the existing code listens on (e.g. `.selectedOffice`, `nav-link`, `dropdown-toggle`). The redesign is visual; the behavior must keep working unchanged.

---

## UI migration status (Bootstrap → Tailwind)

### ✓ Migrated

| Page | Route | Notes |
|---|---|---|
| Dashboard | `/home` | KPI strip + recent tours + tasks + announcements |
| Tours — list | `/tour` | Toolbar + table + edit/delete row actions + delete modal |
| Tours — show (chrome) | `/tour/{id}` | Breadcrumb / header / office / convert banner / tab nav. Tab panel content below is still Bootstrap. |
| Tasks — list | `/task` | Toolbar + table |
| Help — index | `/help` | Article list |
| Landing — public | `/landing/{slug}` | Public tour landing page |
| Mailbox configure | `/email/configure` | IMAP credentials |
| Landing 404 | `errors/landing-not-found` | |

### ✗ Pending — grouped by priority

#### Tier 1 — daily-use, high impact
| Module | Pages |
|---|---|
| **Clients** | index, show, create, edit, destroy |
| **Quotations** | index, show, create, edit |
| **Invoices + Accounting** | invoices index/show/create + accounting / accounts / transactions / taxes |
| **Tour create / edit forms** | `/tour/create`, `/tour/{id}/edit` |
| **Tour show — tab panel contents** | Front Sheet, Services, Tour, Quotation, Guest List, Invoices, Billing |

#### Tier 2 — supplier catalog (same shape, can batch-migrate)
hotel, event, guide, restaurant, transfer, bus, driver, flight, cruises

#### Tier 3 — configuration & supporting
settings, office, season, currencies, currency_rate, rate, tour_package, room_types, templates, menu, criteria, kontingent, taxes, status, legend, announcements, profile

#### Tier 4 — communication & supporting
chats, email/emails (legacy in-app webmail), activities, comments, attachment, comparison, holidaycalendar, reporting, offers, supplier_search, booking_request

#### Tier 5 — external portals
TMS-Client (entire portal), TMS-Supplier (entire portal)

#### Tier 6 — auth screens
login, password email, password reset, register

### Backend hardening (paused)

See [AUDIT.md](AUDIT.md) for items CC1–CC16. Highlights:

- Money columns should be `DECIMAL(15,2)` not floats
- Destructive GET routes (e.g. `/tour/{id}/delete`) should be `DELETE` + CSRF
- `GET /clear` should be auth-gated
- Permission seeders have gaps

---

## SnappyMail integration

SnappyMail is the modern webmail UI, mounted at `/mail` via the Apache alias in
[snappymail-apache.conf](snappymail-apache.conf). Integration uses SSO so users
don't re-enter their IMAP password.

### Flow

1. User saves IMAP credentials at [/email/configure](resources/views/snappymail/configure.blade.php).
2. Submitting POSTs to `/email/save` → encrypts password, persists, redirects to `/email/sso`.
3. `/email/sso` calls `\RainLoop\Api::CreateUserSsoHash($email, $password)`, gets a single-use hash valid for ~10 seconds.
4. Redirects to `/mail/?sso&hash=<hash>`. SnappyMail consumes the hash and logs the user in.

### `.env` keys

```
SNAPPYMAIL_URL=/mail
SNAPPYMAIL_ADMIN_URL=/mail/?admin
SNAPPYMAIL_SSO_KEY=<random-base64>
IMAP_HOST=<imap.your-mail.com>
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
```

### Sec-Fetch policy

SnappyMail enforces Sec-Fetch-* headers. The SSO redirect chain (cross-origin
POST → 302 → GET on different scope) gets flagged `Site: cross-site` and
rejected by default.

The fix lives in
`public/snappymail/data/_data_/_default_/configs/application.ini`:

```ini
secfetch_allow = "mode=navigate,dest=document,site=same-site;mode=navigate,dest=document,site=cross-site"
```

The `data/` dir is gitignored (per-environment runtime state), so this
setting must be applied on each environment.

### Legacy in-app webmail

The pre-SnappyMail in-app webmail still exists at `app/Http/Controllers/Api/EmailsController` +
`app/Imap/` and is wired to some flows. Don't delete — some routes depend on it.

---

## Conventions and gotchas

### Eloquent models live at app root
Models are under [app/](app/) directly (e.g. [app/Tour.php](app/Tour.php),
[app/Hotel.php](app/Hotel.php), [app/Client.php](app/Client.php)) — **not**
under `app/Models/`. The namespace is `App\` (no `App\Models\`). Match this
flat layout when adding new models.

### Seeders are in `database/seeds/`, not `database/seeders/`
Both directories exist. `database/seeders/` is mostly empty. Check both
before assuming a seeder is missing.

### `TourControllerImproved.php` is dead code
It's an unfinished refactor — NOT wired into any route. Edit
[TourController.php](app/Http/Controllers/TourController.php) for current behavior.

### Routes use fully-qualified controller strings
Most routes are registered as `'\App\Http\Controllers\FooController'` (leading
backslash). Match that style when adding routes near them.

### DataTables CDN is centralized
Include `resources/views/component/datatables_cdn.blade.php` rather than
hardcoding the DataTables `<script>` tag.

### Bootstrap 5 modal/tab APIs need defensive checks
Many views run in environments where `bootstrap.Modal.getOrCreateInstance`
may be undefined. Wrap with:

```js
if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
    // use bootstrap.Modal
} else {
    // jQuery fallback
}
```

See `Bootstrap_API_Issues_Analysis.txt` for the audit.

### AppServiceProvider boot quirks
[AppServiceProvider::boot()](app/Providers/AppServiceProvider.php):

- Forces HTTPS unless `APP_ENV=local`.
- Defines a global `STAPLER_NULL` constant.
- Swaps Pusher for [MockPusher](app/Services/MockPusher.php) when Pusher
  credentials are missing — broadcasts silently no-op rather than erroring.

### Helper trait pattern
Trait-based helpers (`FileTrait`, `ExportTrait`, `HelperTrait`, `Trackable`)
sit at the app root and are `use`d by controllers.
[app/Helper/helpers.php](app/Helper/helpers.php) is autoloaded via composer
`files`.

### Storage permissions
After running `php artisan view:cache` as root, generated files in
`storage/framework/views/` are owned by root. PHP-FPM then 500s. Run:

```bash
chown -R www-data:www-data storage/framework/views storage/logs storage/app
chmod -R 775 storage/framework/views
```

---

## Deployment

Push to `main` triggers
[.github/workflows/deploy.yml](.github/workflows/deploy.yml) which:

1. SSHs into the server
2. `git reset --hard origin/main`
3. `composer install --no-dev`
4. `php artisan migrate --force`  ⚠ **runs production migrations**
5. Rebuilds caches
6. Reloads `php8.4-fpm` + nginx

**Any change pushed to `main` runs production migrations automatically.**
Review migrations before merging.

### Workflow

- `main` — production
- `dev-ui` — staging branch for the Tailwind migration; auto-deployed to dev.eetstravel.com
- `ui/<module>-*` — feature branches off `dev-ui` — one per module / fix bundle

### Manual cache reset

```bash
php artisan optimize:clear
# or
curl https://dev.eetstravel.com/clear   # no auth required (footgun)
```

---

## Testing

```bash
vendor/bin/phpunit                                  # all suites
vendor/bin/phpunit --testsuite=Unit                 # tests/Unit
vendor/bin/phpunit --testsuite=Feature              # tests/Feature
vendor/bin/phpunit --filter=SomeTest                # single test
```

Tests use `DatabaseTransactions` — they roll back automatically.
PHPUnit 9.

---

## Scheduled jobs

Wired in [app/Console/Kernel.php](app/Console/Kernel.php). Must be running
via system cron:

```cron
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

| Command | Frequency | Purpose |
|---|---|---|
| `email:parse` | every minute | Parse inbound IMAP mail into the in-app webmail |
| `season:import` | every minute | Import / refresh season data |
| `task:deadline` | daily | Email task-deadline notifications |
| `checktourpackage:status` | daily | Refresh tour-package status |

---

## Folder map

```
/var/www/html/
├── app/
│   ├── *.php                       # Eloquent models (Laravel 7 layout)
│   ├── Console/Commands/           # Artisan commands (email:parse, etc.)
│   ├── Helper/helpers.php          # Global helpers (autoloaded)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                # /api/* (Sanctum-protected)
│   │   │   ├── Auth/               # login / register / password reset
│   │   │   ├── ScaffoldInterface/  # dashboard scaffold
│   │   │   ├── TMSClient/          # client portal
│   │   │   ├── TMSSupplier/        # supplier portal
│   │   │   └── *.php               # staff controllers (one per module)
│   │   └── Middleware/             # clientauth, supplierauth, perm, etc.
│   ├── Imap/                       # Legacy in-app IMAP client
│   ├── Library/Services/           # DeleteModel (Ajaxis delete modal)
│   ├── Providers/                  # AppServiceProvider has repo bindings
│   ├── Repository/
│   │   ├── Contracts/              # Repository interfaces
│   │   └── *Repository/            # Eloquent implementations
│   └── Services/                   # TourService, DashboardService, etc.
├── database/
│   ├── migrations/                 # ~150 migrations
│   ├── seeds/                      # ACTIVE seeders here
│   └── seeders/                    # mostly empty — historical
├── public/
│   ├── css/                        # compiled Tailwind + Bootstrap
│   ├── js/                         # compiled app.js
│   └── snappymail/                 # SnappyMail PHP app (gitignored data/)
├── resources/
│   ├── css/tailwind.css            # Tailwind entry
│   ├── sass/app.scss               # Bootstrap entry
│   ├── js/                         # JS entry
│   └── views/
│       ├── components/ui/          # <x-ui.*> widget library
│       ├── scaffold-interface/     # layouts + admin scaffolding
│       ├── snappymail/configure.blade.php
│       ├── TMSClient/              # client portal views
│       ├── TMSSupplier/            # supplier portal views
│       └── <module>/               # one folder per module
├── routes/
│   ├── web.php                     # staff + client + supplier portals
│   ├── api.php                     # modern API + legacy v1
│   └── channels.php                # broadcast channels
├── storage/
│   ├── app/public/uploads/         # user uploads
│   └── framework/views/            # compiled blades (chown www-data!)
├── tests/
│   ├── Unit/
│   └── Feature/
├── AUDIT.md                        # Backend hardening punch list
├── UI_MIGRATION_AUDIT.md           # UI migration plan
├── CLAUDE.md                       # AI assistant working notes
├── snappymail-apache.conf          # Apache alias for /mail
├── tailwind.config.js              # design tokens
├── webpack.mix.js                  # Laravel Mix build
└── README.md                       # this file
```

---

## Contributing checklist

When adding a feature or migrating a module:

- [ ] Eloquent model at app root (`App\Foo`, not `App\Models\Foo`)
- [ ] Repository contract + implementation if domain-significant
- [ ] Repo binding registered in `AppServiceProvider`
- [ ] Routes use fully-qualified controller strings
- [ ] Permission slug added to the relevant `Permissions*Seeder`
- [ ] Permission gate in Blade (`@if (Auth::user()->can('foo.bar'))`)
- [ ] If migrating UI: use only `<x-ui.*>` widgets, no new BS classes
- [ ] `<ol>` / `<ul>` get `list-none pl-0 m-0`
- [ ] Responsive: tested at 360 / 768 / 1024 widths
- [ ] No JS framework added (no React / Inertia / Livewire)
- [ ] Existing JS handlers preserved when rewriting markup
- [ ] After Tailwind changes: `npm run dev` + `php artisan view:clear`
- [ ] Storage perms: `chown -R www-data:www-data storage/framework/views`

---

## Contact

- Production URL: https://dev.eetstravel.com
- Repository: https://github.com/link2dawood/dev-eetstravel
- Mailbox SSO test: https://dev.eetstravel.com/email/configure
