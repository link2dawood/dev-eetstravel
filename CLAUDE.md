# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Tour Management System (TMS) for **eetstravel.com** — a Laravel 8 application for travel agencies that handles tours, quotations, bookings, accounting, supplier/client portals and an integrated webmail (SnappyMail).

- PHP `^8.0`, Laravel `^8.75`, MySQL (`dev_tms` per [.env](.env))
- Frontend: Blade + Bootstrap 5 + jQuery + DataTables, compiled with Laravel Mix
- Production deploy target: `/var/www/html` on Ubuntu, served by nginx + php-fpm

## Commands

```bash
# Backend
composer install
php artisan migrate            # 148+ migrations in database/migrations
php artisan db:seed             # seeders live in database/seeds (note: not database/seeders)
php artisan serve

# Frontend (Laravel Mix / webpack)
npm install
npm run dev                     # one-shot dev build
npm run watch                   # watch mode
npm run prod                    # production build

# Tests (PHPUnit 9)
vendor/bin/phpunit                                  # all suites
vendor/bin/phpunit --testsuite=Unit                 # tests/Unit
vendor/bin/phpunit --testsuite=Feature              # tests/Feature
vendor/bin/phpunit --filter=SomeTest                # single test

# Cache reset (also exposed via GET /clear route)
php artisan optimize:clear
php artisan view:clear && php artisan config:cache && php artisan route:cache

# Scheduled jobs (see app/Console/Kernel.php) — must be wired to system cron
php artisan schedule:run
php artisan email:parse                # parse inbound mail every minute
php artisan season:import              # season import every minute
php artisan task:deadline              # daily task-deadline notifications
php artisan checktourpackage:status    # daily tour-package status check
```

## Deployment

Push to `main` triggers [.github/workflows/deploy.yml](.github/workflows/deploy.yml), which SSHes into the server, runs `git reset --hard origin/main`, `composer install --no-dev`, `php artisan migrate --force`, rebuilds caches, and reloads `php8.4-fpm` + nginx. **Any change pushed to `main` runs production migrations automatically** — review migrations before merging.

## Architecture

### Eloquent models at the app root (Laravel 7-era layout)
Models live directly under [app/](app/) (e.g. [app/Tour.php](app/Tour.php), [app/Hotel.php](app/Hotel.php), [app/Client.php](app/Client.php)) — **not** under `app/Models/`. The namespace is `App\` (no `App\Models\`). When adding a new model, follow this flat layout to match existing imports.

### Repository pattern for domain entities
Major aggregates (Tour, Hotel, Guide, Restaurant, Event, Transfer, Flight, Cruise, Bus, Driver, Client, Task, Chat, Email, TourPackage, PackageMenu, Settings) use Contract + Eloquent-implementation pairs:

- Contracts: [app/Repository/Contracts/](app/Repository/Contracts/)
- Implementations: `app/Repository/<Name>Repository/Eloquent<Name>Repository.php`
- Bindings are registered in [app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php) — add new bindings there.

Controllers and services type-hint the contract; do not new up the Eloquent repo directly.

### Three portals share one codebase
Routing (and middleware) in [routes/web.php](routes/web.php) is split into three audiences:

1. **Staff dashboard** — most routes; gated by the `perm` middleware ([PermissionsRequiredMiddleware](app/Http/Middleware/PermissionsRequiredMiddleware.php)) which reads Spatie `laravel-permission` roles. The dashboard entry-point is [ScaffoldInterface\AppController@dashboard](app/Http/Controllers/ScaffoldInterface/AppController.php).
2. **TMS-Client portal** — external client-facing pages under `/TMS-Client/*`, gated by the `clientauth` middleware ([clientauth.php](app/Http/Middleware/clientauth.php)), controllers in [app/Http/Controllers/TMSClient/](app/Http/Controllers/TMSClient/).
3. **TMS-Supplier portal** — external supplier-facing pages under `/TMS-Supplier/*`, gated by `supplierauth` ([supplierauth.php](app/Http/Middleware/supplierauth.php)), controllers in [app/Http/Controllers/TMSSupplier/](app/Http/Controllers/TMSSupplier/).

Clients/suppliers are *not* `User` records — they authenticate via separate guards backed by `Client`/`Hotel` (etc.) models.

### Permissions
Every staff route group is wrapped in `middleware('perm')`. The middleware resolves the route name to a permission slug; permissions are seeded by the `Permissions*Seeder` classes in [database/seeds/](database/seeds/). When you add a new resource route, add the corresponding permission seeder (or update an existing one) — otherwise the route will 403 for everyone except super-admins.

### API surface
Two parallel API namespaces under [routes/api.php](routes/api.php):

- **Modern** `auth:sanctum`-protected REST under [app/Http/Controllers/Api/](app/Http/Controllers/Api/) (`/api/tours`, `/api/clients`, `/api/tasks`, `/api/dashboard`, …)
- **Legacy v1** under `/api/v1/*` for email/IMAP + dashboard widgets — no auth middleware, called from the in-app webmail UI.

### Services and Helpers
- [app/Services/](app/Services/) — domain services (`TourService`, `DashboardService`, `CacheService`, `ValidationService`). `TourService` wraps tour creation in a DB transaction including `tour_days`, responsible-user sync, and status setup; use it rather than touching `Tour::create` from controllers.
- [app/Helper/helpers.php](app/Helper/helpers.php) is autoloaded via composer (`files` in [composer.json](composer.json)) — add new global helpers there. Trait-based helpers (`FileTrait`, `ExportTrait`, `HelperTrait`, `Trackable`) sit alongside it and are `use`d by controllers.
- [app/Library/Services/DeleteModel.php](app/Library/Services/DeleteModel.php) backs the Ajaxis delete-confirmation modal flow used by `*Controller@DeleteMsg`.

### Frontend conventions worth knowing
Two project-specific gotchas — documented in the loose `*_Analysis.txt` files at repo root:

- **DataTables CDN is centralized** in `resources/views/component/datatables_cdn.blade.php`. Don't hardcode DataTables `<script>` tags in new views — include the partial.
- **Bootstrap 5 modal/tab APIs need defensive checks.** Many views run in environments where `bootstrap.Modal.getOrCreateInstance` may be undefined or partially loaded; wrap calls in `typeof bootstrap !== 'undefined' && bootstrap.Modal` checks and provide a jQuery fallback. See `Bootstrap_API_Issues_Analysis.txt` for the audit of files still needing this treatment.

### SnappyMail integration
SnappyMail is mounted at `/mail` (Apache alias in [snappymail-apache.conf](snappymail-apache.conf)) and integrated via SSO using `SNAPPYMAIL_SSO_KEY` from `.env`. [app/Http/Controllers/SnappyMailController.php](app/Http/Controllers/SnappyMailController.php) handles direct/admin/configure flows. The legacy in-app webmail (under `app/Http/Controllers/Api/EmailsController` + [app/Imap/](app/Imap/)) is separate and still used by some routes.

### AppServiceProvider boot quirks
[AppServiceProvider::boot()](app/Providers/AppServiceProvider.php) forces HTTPS unless `APP_ENV=local`, and defines a global `STAPLER_NULL` constant. It also swaps Pusher for [MockPusher](app/Services/MockPusher.php) when Pusher credentials are missing — broadcasts silently no-op in that case rather than erroring.

## Conventions and gotchas

- **Routes are fully-qualified in `web.php`.** Most resource routes are registered as `'\App\Http\Controllers\FooController'` (leading backslash). Match that style when adding routes near them — mixing the short controller name with `Route::resource` inside the same group works but generates inconsistent route caches.
- **Seeders are in `database/seeds/`, not `database/seeders/`.** Both directories exist (`database/seeders/` is mostly empty); the active seeders are PSR-4 autoloaded as `Database\Seeders\` from `database/seeders/` per [composer.json](composer.json), but historic seeders are in `database/seeds/`. Check both before assuming a seeder is missing.
- **There is a `TourController.php` and a `TourControllerImproved.php`.** The "Improved" variant is not wired into any route — it's an unfinished refactor. Edit `TourController.php` for current behavior.
- **`composer.json` has `"minimum-stability": "dev"`.** New deps may resolve to dev versions if you don't pin them.
- The `GET /clear` route ([routes/web.php:15](routes/web.php#L15)) clears caches without auth — fine in staging but a footgun in prod.
