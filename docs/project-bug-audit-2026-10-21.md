# Backend and frontend bug audit — 2026-10-21

Remediation implemented on 2026-10-21. The original 21 findings are retained below as historical evidence. The two remediation migrations have now been applied to the configured local development MySQL database. No production database changes, outbound emails or destructive record operations were performed.

## Remediation status

| Findings | Status | Changes |
| --- | --- | --- |
| B01-B02 | Fixed in code | Legacy APIs require session authentication, CSRF and module permissions. Mailbox IDs must match the authenticated user; dashboard identity comes from the session. Removed the diagnostic email-parsing route. |
| B03-B04 | Fixed in code | Inline package edits use POST, an edit permission, a description-only allowlist, validation and 404 handling. HTMLPurifier sanitizes descriptions on write and read, including previously stored HTML. |
| B05-B07 | Fixed in code | Modern APIs use the existing session login. Registered permission middleware and enforced permissions for reads, writes, settings, cloning and each bulk operation. Resource saves map store to create and update to edit, matching existing grants while preserving explicit route permissions. |
| B08 | Fixed in code | Added a dedicated dashboard API controller implementing all six registered endpoints. |
| B09-B10 | Fixed in code | Corrected client request namespaces and bound-model validation, removed invalid relationship loads, added client tours and normalized city IDs. Client/password and user/mail-password fields are hidden from model JSON. |
| B11 | Fixed in code | Task API maps its public fields to the actual task columns; list, create, update and status operations are tested. Priority matches the existing boolean column. |
| B12 | Fixed in code | Registration uses App\User and supplies the existing required email-server default. Registration and login are tested. |
| B13 | Fixed; local migrations applied | Fresh media migrations are consistent and preserve existing records; duplicate creation is guarded. Fixed additional MySQL-only migration statements that blocked SQLite tests. Added forward migrations for the user active flag and missing tour API schema. |
| B14-B15 | Fixed in code | Delete endpoints use POST/DELETE with CSRF; updated shared buttons, modal handlers and tour/chat/guest-list/allotment callers. Removed public /clear; authorized settings APIs handle cache maintenance. |
| B16-B17 | Fixed in code | Mutable API lists/statistics bypass stale caches. Invalid task/user input returns 422; CSRF failures return 419. Pagination is bounded. |
| B18 | Fixed; local migration applied | User tasks use the real relationship; grouped user search excludes inactive users. The active flag is writable through the permission-protected user API and has a forward migration. |
| B19-B20 | Fixed in code | Restored the Mix entry and verified production compilation. Updated test factories/authentication and replaced obsolete AdminLTE generator tests with current authentication coverage; tests no longer run Composer or delete seeders. |
| B21 | Fixed in code | No registered action points to a missing method. Implemented geography and calendar handlers; removed unsupported dormant reporting/chat/client-portal resource actions and unused broken helper routes. Existing supported screens and custom chat flows remain registered. |

Additional fixes found during implementation: mail attachments now use private storage and owner-bound, expiring signed downloads; permission lookup errors now deny access. Tour service/request contracts, partial updates, responsible-user assignments, cloning and tour-day creation were repaired while enabling the API tests.

## Local completion check

On the follow-up check, the local MySQL database was reachable but the two remediation migrations were pending. Both were applied successfully, adding users.is_active and tour_responsible_user. The existing tours.client_id column was retained. User/tour row counts and existing responsible-user assignments were unchanged; all assignments were backfilled into the new table.

Nine API read previews returned HTTP 200 inside a MySQL-enforced READ ONLY transaction, rolled back afterward: clients, users, tasks, tours, dashboard, statistics, charts, upcoming deadlines and recent activity. Authorization for these previews was simulated only in process memory; no account permissions changed.

The local permission inventory also exposed a remaining save regression: grants exist for create/edit, while resource routes are named store/update. This mapping is now fixed. Middleware-only checks using existing eligible local accounts allow hotel, client, tour and task save/update routes without executing their controllers or saving records. Fourteen regression cases cover allowed saves, denied writes and explicit-permission preservation.

## Current verification

- Full PHPUnit suite: **198 tests, 1,530 assertions, all passing**. All database tests used isolated SQLite, not the live application database.
- Production asset build: passed using the locked npm dependencies and temporary output under storage/app/audit-build; existing public assets were not overwritten. Existing Sass/Browserslist deprecation notices remain.
- PHP parsing: 653 files, no parse errors (app, routes, config, database and tests).
- JavaScript parsing: 49 top-level non-minified scripts, no syntax errors.
- Registered missing controller actions: 0. Registered GET destroy actions: 0.
- Legacy modal request check passed: confirmation loads with GET; deletion sends POST plus CSRF.
- Regression coverage includes anonymous rejection, mailbox ownership, denied bulk deletion, successful task/client/user writes, fresh lists, inactive-user search, dashboard endpoints, sanitized legacy HTML, CSRF enforcement, private attachment downloads and denied missing permissions.
- This does not claim a fresh browser tour of every page or successful live Google/IMAP/SMTP integration.

## Deployment requirements

The following migrations are already applied to this local development database. Apply them through the normal deployment process in other environments before enabling the updated APIs:

- database/migrations/2026_10_21_000001_add_user_active_flag.php
- database/migrations/2026_10_21_000002_complete_api_tour_contract.php

Modern and v1 APIs now use the existing web session and require the normal CSRF token for writes. They do not require Sanctum. Client API writes cover business fields; attachments, portal-password changes and contact-row editing continue through their existing web workflows.

Google Calendar needs either GOOGLE_CALENDAR_CREDENTIALS pointing to a credentials JSON file, or GOOGLE_CALENDAR_CLIENT_ID and GOOGLE_CALENDAR_CLIENT_SECRET with the /oauth callback configured. Missing configuration returns an explicit 503; no credentials were changed or external account connected.

Local verification logs are under storage/app/audit-*.txt and are excluded from Git.

## Historical baseline

## Baseline verification

- Unit suite: **145 tests, 500 assertions, 14 errors**; 131 tests completed without errors. All 14 errors are TourServiceTest setup failures caused by the missing `media` table migration below; their test bodies did not execute.
- Feature suite: aborts during class loading with an incompatible `setUp()` declaration.
- PHP parsing: **607 files**, zero syntax errors across app, routes, config and database (including nested legacy copies).
- JavaScript parsing: **49 top-level, non-minified public/js files**, zero syntax errors. This does not verify browser behavior or vendor scripts.
- Laravel boots and route enumeration succeeds. Reflection found **31 registered route entries with missing controller methods** (some are dormant resource routes).
- Runtime class/guard checks reproduced the missing Sanctum guard, registration model and client request classes. Client relationship checks confirmed missing methods.
- No fresh authenticated browser tour or live database integration run was performed. Previous browser audit results remain historical evidence. No dependency advisory lookup was performed.
- Local diagnostic output: `storage/app/project-audit-evidence-2026-10-21.txt`.

## Original findings

| ID | Priority | Defect and effect | Evidence |
| --- | --- | --- | --- |
| B01 | Critical | Legacy mailbox API has no authentication or ownership checks. Anonymous callers can reach read/send/delete/move handlers with a supplied user ID. Mailbox access uses shared configured credentials. Actual mail impact depends on working credentials/connectivity; no mailbox operation was attempted. | `routes/api.php:111` onward; `app/Http/Controllers/Api/EmailsController.php:32`; resolved middleware is only `api`, whose stack contains throttle and bindings, not authentication. |
| B02 | High | Legacy dashboard and tour-data API routes also lack authentication. `getTours`/`getArchiveTours` query tours directly; dashboard methods trust caller-supplied `userId`. This exposes data when the database is available. | `routes/api.php` v1 group; `EmailsController.php:64`; `DashboardController.php:76`. |
| B03 | High | A GET request can update an arbitrary tour-package model attribute. `ajaxUpdate` directly assigns caller-provided `fieldName` and `fieldValue` without a field allowlist or validation. GET bypasses CSRF checks; authenticated access is still required. Invalid IDs also produce a null-object failure. | `routes/web.php:240`; `app/Http/Controllers/TourPackageController.php:1588`. |
| B04 | High | Package descriptions have a stored HTML/script injection path. `descriptionPackage` stores raw request text and tour detail emits it through unescaped Blade output. A permitted author can inject active markup into another viewer's page. Source-confirmed path; no payload was saved. Rich text needs HTML sanitization, not simply removal of all formatting. | `TourPackageController.php:1550–1568`, also update at line 992; `resources/views/tour/show.blade.php:813`. |
| B05 | High | The newer API is unusable because `auth:sanctum` references an uninstalled package/undefined guard. Reproduced: `Auth guard [sanctum] is not defined.` Requests cannot reach normal authorization or handlers. | `routes/api.php:21`; `composer.json`; `config/auth.php`. |
| B06 | High | Tour API additionally uses an unregistered `permission:` middleware alias. Fixing Sanctum alone leaves permission-protected actions failing middleware resolution. Current Kernel defines only `permissions.required`; no application provider registers `permission`. | `app/Http/Controllers/Api/TourApiController.php:26–30`; `app/Http/Kernel.php:65–75`. |
| B07 | High | New API authorization is incomplete: client/task/user/settings controllers require authentication only; user update can target another user, client/task destroy has no permission gate, and cache operations have no settings gate. Tour `bulkAction` and `clone` are omitted from constructor permission restrictions; bulkAction includes deletion. Latent behind B05; fixing authentication must also fix authorization. | Constructors in `Api/{ClientApiController,TaskApiController,UserApiController,SettingsApiController}.php`; `TourApiController.php:23–30,460`. |
| B08 | High | Six new dashboard API routes point to nonexistent methods (`index`, `statistics`, `recentActivity`, `upcomingDeadlines`, `charts`, `clearCache`). They remain broken after authentication is repaired. | `routes/api.php:42–49`; `app/Http/Controllers/Api/DashboardController.php`; reflection confirmed. |
| B09 | High | Client API create/update type-hint nonexistent request classes in a `Requests\Client` namespace. Real request files are directly under `Requests`. Container resolution fails after authentication is repaired. | `app/Http/Controllers/Api/ClientApiController.php:7–8,45,79`. |
| B10 | High | Client API eagerly loads nonexistent `country`, `city`, `tours` relationships and calls `tours()`. The Client model defines only `files()`; country and city are stored attributes. List/detail/save-response/tours flows therefore fail rather than returning the advertised data. | `ClientApiController.php:28,48,70,82,135`; `app/Client.php:49–66`. |
| B11 | High | Task API uses schema names that disagree with the application: `deadline`, `tour_id`, `assigned_to`, `status_id` versus `dead_line`, `tour`, `assign`, `status`. List sorting/overdue queries fail on the checked-in schema; writes and status updates use incorrect columns and do not update the relationships used by the UI. | `Api/TaskApiController.php:34–55,93–99,157,171`; `app/Task.php:39–77`; `database/migrations/2017_06_22_113318_create_tasks_table.php:18–25`. |
| B12 | High | Public registration uses nonexistent `App\Models\User`; the active authentication model is `App\User`. A valid registration reaches a class-not-found failure. A nested `app/app/Models/User.php` file does not satisfy the configured PSR-4 path. | `app/Http/Controllers/Auth/RegisterController.php:7,73`; `routes/web.php:13`; runtime class check. |
| B13 | High | Fresh database migration/test setup fails: the migration named `create_media_table` calls `Schema::table('media')` without creating the table first. Reproduced against the configured in-memory SQLite database, blocking 14 unit tests and clean setup. Existing installations with a media table may avoid this failure. | `database/migrations/2025_09_12_181225_create_media_table.php:11`. |
| B14 | High | Many delete actions accept GET, including hotel/event/guide/restaurant/client/invoice deletes. A cross-site navigation can invoke a destructive action using an authorized user's session, without a CSRF token. Confirmation dialogs do not protect the endpoint itself. No delete endpoint was invoked. | `routes/web.php:49,59,72,80,88,100` and other delete routes; `HotelController::destroy` actually deletes records. |
| B15 | Medium | Public `/clear` executes cache clearing, config caching and view clearing with no authentication. Any visitor can repeatedly trigger operational work. | `routes/web.php:15–21`; resolved middleware is only `web`. |
| B16 | Medium | API list caches are stored without tags but invalidated using tags. Updating clients/users/tasks leaves their cached lists stale until TTL expiry (300 seconds); unsupported tag stores also make invalidation fail and return false, which callers ignore. | `ClientApiController.php:27–41,51,84,105`, equivalent user/task code; `app/Services/CacheService.php:37,150`. |
| B17 | Medium | Task/user API validation exceptions are caught as generic exceptions and converted from expected 422 validation responses into 500 errors. Clients cannot reliably distinguish invalid input from server failure. | `TaskApiController.php:48–80,91–117,167–190`; `UserApiController.php:57–82`. |
| B18 | Medium | User tasks endpoint calls nonexistent `assignedTasks()`; model defines `tasks()`. User search also has ungrouped `name LIKE ... OR email LIKE ... AND is_active = true`, so the active filter does not apply to name matches. `is_active` is omitted from User fillable fields, so update requests cannot persist it through this controller. | `Api/UserApiController.php:60–64,89–94,108`; `app/User.php:61–71,139`. |
| B19 | Medium | Frontend asset build configuration references missing `resources/js/app.js`. Even after installing npm dependencies, both configured Mix build scripts cannot compile that entry. `node_modules` is also absent locally; no install or generated asset overwrite was attempted. | `webpack.mix.js:14`; `package.json` development/production scripts; filesystem check. |
| B20 | Medium | The feature test suite cannot start: legacy `setUp()` lacks the `: void` return type required by the parent. `setUpBeforeClass()` also lacks its required return type and invokes Composer as a test side effect. Reproduced fatal at setUp before any feature test ran. | `tests/Feature/AcachaAdminLTELaravelTest.php:26,37`. |
| B21 | Medium | Additional registered routes point to absent methods. These include country/state lookups, Google Calendar OAuth/list, flight/cruise attachments, chat writes and client portal resource actions. Some may be dormant rather than visible UI failures. Full inventory below prevents treating them as browser-reproduced failures. | Runtime route reflection; see appendix. |

## Original missing route-action inventory

31 route entries total; methods/verbs are included where paths repeat. All actions were checked against the loaded controller classes, not guessed from source text.

- New dashboard (6): `GET api/dashboard` → `index`; `GET api/dashboard/statistics` → `statistics`; `GET api/dashboard/recent-activity` → `recentActivity`; `GET api/dashboard/upcoming-deadlines` → `upcomingDeadlines`; `GET api/dashboard/charts` → `charts`; `POST api/dashboard/cache/clear` → `clearCache`.
- Legacy dashboard (1): `api/v1/dashboard/chat_groups` → `DashboardController@getChatGroups`.
- Geography (2): `countries` → `ClientController@getCountries`; `states/{countryCode}` → `getStates`.
- Reporting (8): `reporting/create` → `create`; `POST reporting` → `store`; `reporting/{reporting}/edit` → `edit`; `PUT/PATCH reporting/{reporting}` → `update`; `DELETE reporting/{reporting}` → `destroy`; `reporting/{id}/update` → `update`; `reporting/{id}/delete` → `destroy`; `reporting/{id}/deleteMsg` → `DeleteMsg`.
- Calendar (2): `oauth` → `gCalendarController@oauth`; `calendar/list` → `getCalendarListJson`.
- Tour services (2): `api/tour_package_transfer/{id}/delete` → `TourPackageController@deleteTransferTourPackage`; `get_tour_days_id_hotel_service` → `TourController@getTourDaysIdHotelService`.
- Comments (1): `comment/{id}/reply` → `CommentController@reply`.
- Attachments (2): `cruises/{id}/api/attach` → `CruisesController@getAttach`; `flights/{id}/api/attach` → `FlightController@getAttach`.
- Announcements (1): `announcements/{announcement}/generate` → `AnnouncementController@generateAnnouncements`.
- Chat (2): `POST chat` → `ChatController@store`; `PUT/PATCH chat/{chat}` → `ChatController@update`.
- Client portal (4): `GET TMS-Client-tours` → `TMSClient\TourController@index`; `TMS-Client-tours/{TMS_Client_tour}/edit` → `edit`; `PUT/PATCH TMS-Client-tours/{TMS_Client_tour}` → `update`; `DELETE TMS-Client-tours/{TMS_Client_tour}` → `destroy`.

## Original repair order

Protect legacy APIs and remove unsafe GET writes first; sanitize rendered descriptions. Then repair authentication and authorization together, missing actions/classes and schema mismatches. Repair migrations and test discovery so integration checks can run, followed by cache invalidation and frontend builds. Browser and successful-save verification is still needed after those repairs.
