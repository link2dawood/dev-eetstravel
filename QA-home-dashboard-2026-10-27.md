# QA Report: `/home` Dashboard, `/users`, `/roles`, `/permissions`, `/settings`, `/tour`, `/current_offers`, `/current_bookings`, `/past_offers`, `/cancellation_policies`, `/task`, `/supplier_search`, `/event`, `/restaurant`, `/guide`, `/images/index`, `/bus/calendar`, `/transfer`, `/bus`, `/driver`, `/status`, `/room_types`, `/rate`, `/currency_rate`, `/currencies`, `/criteria`, `/email`, `/email/webmail`, `/comment`, `/clients`, `/accounting`, `/invoices`, `/reporting` and `/activities`

- **Date:** 2026-10-27
- **Environment:** local, DB `eets-travel`
  - `/home` was tested on `http://localhost:8000`.
  - `/users`, `/roles`, `/permissions`, `/settings`, `/tour`, `/current_offers`, `/current_bookings`, `/past_offers`, `/cancellation_policies`, `/task`, `/supplier_search`, `/event`, `/restaurant`, `/guide`, `/images/index`, `/bus/calendar`, `/transfer`, `/bus`, `/driver`, `/status`, `/room_types`, `/rate`, `/currency_rate`, `/currencies`, `/criteria`, `/email`, `/email/webmail`, `/comment`, `/clients`, `/accounting`, `/invoices`, `/reporting` and `/activities` were tested on `http://127.0.0.1:8010`.
- **Method:** logged in over HTTP as a seeded admin, a seeded regular user (role `user`) and a guest. Submitted the real forms and endpoints, then checked responses, rendered HTML, database state and the Laravel logs. Not clicked through in a real browser, so the JS console was not checked.

> **Fix first (app-wide data loss):** `DeleteModel::check()` deletes a record exactly when it is **in use** (inverted condition). It affects restaurants, events, flights, cruises, guides, transfers, drivers, criteria and rates. See **RS1**.

## Summary

| ID | Severity | Issue | Where it shows up | Request that failed |
|----|----------|-------|-------------------|---------------------|
| H1 | Critical | Opening `/home` logs the user out | `/home` | `GET /api/v1/dashboard/tours` (and every `/api/v1/*`) |
| H2 | Critical | Calendar, Latest Tours and Activities are always empty (403) | `/home` | `GET /home/getToursTasksForCalendar`, `GET /api/v1/dashboard/tours`, `GET /api/v1/dashboard/activities` |
| H3 | Critical | Dashboard chat is completely blocked (403) | `/home` | `GET /chat/{id}/renderChat`, `POST /chat/message`, `GET /chat/getChatNotifications` |
| U1 | Critical | Any logged-in user can create, edit and delete any user | `/users` | `GET /users`, `POST /users`, `POST /users/{id}`, `DELETE /users/{id}` |
| U2 | Critical | Editing user A can change user B (`user_id` tampering) | `/users/{id}/edit` | `POST /users/{id}` |
| U3 | Critical | SVG avatar upload allows stored XSS | `/users/create`, `/users/{id}/edit` | `POST /users`, `GET /uploads/avatars/*.svg` |
| U4 | Critical | Role and permission management is broken for everyone (403) | `/users/{id}/edit` | `POST /users/addRole`, `POST /users/removeRole`, `POST /users/addPermission`, `GET /users/removePermission/{id}/{key}`, `GET /users/{id}/deleteMsg` |
| H4 | High | Stored XSS in the header notifications on every page | `/home` (and every page) | page render |
| H5 | High | The task Delete button on the dashboard doesn't work (405) | `/home` | `DELETE /task/{id}` |
| U5 | High | Saving the user edit form wipes location, education and note | `/users/{id}/edit` | `POST /users/{id}` |
| U6 | High | Saving a user always turns `email_server` on | `/users/{id}/edit` | `POST /users/{id}` |
| U7 | High | Deleting a user shows an error toast even though it worked | `/users` | `DELETE /users/{id}` → redirect → `DELETE /users` (405) |
| H6 | Medium | Tasks without a deadline show as "due now" and overdue | `/home` | page render |
| U8 | Medium | Any password is accepted (e.g. `1`) | `/users/create`, `/users/{id}/edit` | `POST /users` |
| U9 | Medium | Names longer than 191 characters are silently cut | `/users/create` | `POST /users` |
| U10 | Medium | A duplicate email on edit gets a vague database error | `/users/{id}/edit` | `POST /users/{id}` |
| H7 | Low | Announcement order, `quickCreateCalendarEvent` bugs, jQuery `.success()` | `/home` | various |
| U11 | Low | The "No roles" / "No permissions" badges never show | `/users` | page render |
| U12 | Low | The users list has no pagination and runs N+1 queries | `/users` | `GET /users` |
| R1 | Critical | Any logged-in user can create, rename and delete roles | `/roles` | `GET /roles`, `POST /roles/store`, `POST /roles/update`, `DELETE /roles/{id}` |
| R2 | Critical | Any user can give a role they hold more permissions (privilege escalation) | `/roles/{id}/edit` | `POST /roles/addPermission`, `GET /roles/removePermission/{perm}/{role}` |
| R3 | High | Removing a permission is a state-changing GET link (CSRF) | `/roles/{id}/edit` | `GET /roles/removePermission/{perm}/{role}` |
| R4 | High | The `admin` role can be renamed or deleted | `/roles`, `/roles/{id}/edit` | `POST /roles/update`, `DELETE /roles/{id}` |
| R5 | High | Adding several permissions is not all-or-nothing | `/roles/{id}/edit` | `POST /roles/addPermission` |
| R6 | Medium | A duplicate role name crashes with a 500 page | `/roles/create` | `POST /roles/store` |
| R7 | Medium | A permission name that doesn't exist causes a 500 | `/roles/{id}/edit` | `POST /roles/addPermission`, `GET /roles/removePermission/{perm}/{role}` |
| R8 | Medium | Deleting a role in use gives no warning; users silently lose it | `/roles` | `DELETE /roles/{id}` |
| R9 | Medium | Role names longer than 191 characters are silently cut | `/roles/create` | `POST /roles/store` |
| R10 | Medium | Renaming to an existing name gets a vague database error | `/roles/{id}/edit` | `POST /roles/update` |
| R11 | Low | Button styles, "No permissions" badge, empty `deleteMsg` page, legacy delete route always 403 | `/roles` | various |
| P1 | Critical | A guest who isn't logged in can create, rename and delete permissions | `/permissions` | `GET /permissions`, `POST /permissions/store`, `POST /permissions/update`, `DELETE /permissions/{id}` |
| P2 | Critical | Any logged-in user can create, rename and delete permissions | `/permissions` | same as P1 |
| P3 | High | Renaming a permission in use silently changes who has access | `/permissions/{id}/edit` | `POST /permissions/update` |
| P4 | High | The "permission in use" delete guard is bypassed by every delete form (`force=1`) | `/permissions` | `DELETE /permissions/{id}` |
| P5 | Medium | A duplicate permission name crashes with a 500 page | `/permissions/create` | `POST /permissions/store` |
| P6 | Medium | No format check on permission names (spaces and symbols accepted) | `/permissions/create`, `/permissions/{id}/edit` | `POST /permissions/store`, `POST /permissions/update` |
| P7 | Medium | Permission names longer than 191 characters are silently cut | `/permissions/create` | `POST /permissions/store` |
| P8 | Medium | The permissions list is 870 KB / 1.5 s with no pagination | `/permissions` | `GET /permissions` |
| P9 | Low | Creating a permission shows no success message | `/permissions/create` | `POST /permissions/store` |
| P10 | Low | Legacy delete route always returns 403 | — | `POST /permissions/{id}/delete` |
| P11 | Low | Edit/Delete button styles don't follow the UI rules | `/permissions` | page render |
| S1 | High | No one can open `/settings`: no role, not even `admin`, holds `settings.*` | `/settings` | `GET /settings` (403) |
| S2 | High | Setting values aren't type-checked; `abc` / `-5` are saved and silently break scheduled jobs | `/settings/{id}/edit` | `PUT /settings/{id}` |
| S3 | High | Mass assignment on create (`id`, `created_at` can be set) | `/settings/create` | `POST /settings` |
| S4 | High | Settings the app depends on can be deleted; `RunImport` then crashes | `/settings` | `DELETE /settings/{id}` |
| S5 | Medium | The create form never shows validation errors | `/settings/create` | `POST /settings` |
| S6 | Medium | Missing IDs crash with a 500 on update and delete | `/settings/{id}/edit`, `/settings` | `PUT /settings/{id}`, `DELETE /settings/{id}` |
| S7 | Medium | No format check on setting names; long names silently cut to 191 | `/settings/create` | `POST /settings` |
| S8 | Low | Edit success message always reads "Setting  edited" | `/settings/{id}/edit` | `PUT /settings/{id}` |
| S9 | Low | Delete shows no success message | `/settings` | `DELETE /settings/{id}` |
| S10 | Low | `GET /settings/{id}` returns an empty page | `/settings/{id}` | `GET /settings/{id}` |
| S11 | Low | Edit page has a duplicate top Save button; button styles don't follow the UI rules | `/settings/{id}/edit`, `/settings` | page render |
| S12 | Low | The list has no Name (setting key) column | `/settings` | page render |
| T1 | Critical | Users with no tour permissions can create and edit tours | `/tour/create`, `/tour/{id}/edit` | `POST /tour/save`, `POST /tour/{id}/update`, `POST /tour/{id}/updateCalendar` |
| T2 | Critical | Export, clone, convert and PDF are 403 for everyone, admin included | `/tour/{id}` | `GET /tour/{id}/export/html`, `/export/pdf`, `/doc/{type}`, `/clone`, `/convert_to_quotation`, `/convert_to_tour`, `/pdf_data`, `POST /tour/{id}/generatePackages` |
| T3 | High | Tours that started over 30 days ago can't be edited at all | `/tour/{id}/edit` | `POST /tour/{id}/update` |
| T4 | High | Inline edit saves invalid values (pax `abc`/`-5`, pax_free > pax, unknown status, empty external name) | `/tour/{id}` | `POST /tour/{id}/update` (AJAX) |
| T5 | High | Inline edit creates junk `City` records from request input | `/tour/{id}` | `POST /tour/{id}/update` (AJAX, `cityName`) |
| T6 | High | Any tour's landing page is public by numeric ID | `/tour/{id}/landingpage` | `GET /tour/{id}/landingpage` |
| T7 | High | Deleting a tour hard-deletes its client invoices and quotations | `/tour` | `POST\|DELETE /tour/{id}/delete` |
| T8 | Medium | Edit requires a phone, but create never asks for one | `/tour/create`, `/tour/{id}/edit` | `POST /tour/{id}/update` |
| T9 | Medium | Tour names reject `.`, `'`, `&` and accented letters | `/tour/create`, `/tour/{id}/edit` | `POST /tour/save`, `POST /tour/{id}/update` |
| T10 | Medium | Every user sees and can edit every tour (no ownership scoping) | `/tour`, `/tour/{id}`, `/tour/{id}/edit` | `GET /tour`, `POST /tour/{id}/update` |
| T11 | Medium | `child_count` is unbounded; `room_types_qty` accepts unknown types and negative counts | `/tour/create` | `POST /tour/save` |
| T12 | Medium | The PUT edit route rejects every save ("external name already taken") | — | `PUT /tour/{id}` |
| T13 | Medium | Tour pages are slow (1.5-1.8 s) | `/tour`, `/tour/create`, `/tour/{id}/edit` | `GET /tour`, `GET /tour/create`, `GET /tour/{id}/edit` |
| T14 | Low | Validation messages don't match the actual problem | `/tour/create`, `/tour/{id}/edit` | `POST /tour/save`, `POST /tour/{id}/update` |
| T15 | Low | Deleting a missing tour gives no message | `/tour` | `POST /tour/{id}/delete` |
| T16 | Low | `?notification_click={id}` marks any user's notification as read | `/tour/{id}` | `GET /tour/{id}?notification_click={id}` |
| T17 | High | `updateCalendar` writes unvalidated dates and can set them to NULL | `/tour` (calendar drag) | `POST /tour/{id}/updateCalendar` |
| C1 | High | No one can open `/current_offers` (permission doesn't exist) | `/current_offers` | `GET /current_offers` (403) |
| C2 | High | Delete from the offers list sends a GET, which returns 405 | `/current_offers` | `GET /tour/{id}/delete` |
| C3 | Medium | Tours still running but ending within 7 days vanish from "current" | `/current_offers` | `GET /current_offers` |
| C4 | Medium | Tours with no status disappear from the list (`status <> 46` is false for NULL) | `/current_offers` | `GET /current_offers` |
| C5 | Medium | Cancelled tours are listed as current offers | `/current_offers` | `GET /current_offers` |
| C6 | Medium | The list has no pagination | `/current_offers` | `GET /current_offers` |
| C7 | Low | Empty cells show `�` (view saved in Windows-1252) | `/current_offers` | page render |
| C8 | Low | CSV export includes action-button text and sample rows; fake sample row shown when empty | `/current_offers` | Export CSV button |
| B1 | High | No one can open `/current_bookings` (permission doesn't exist) | `/current_bookings` | `GET /current_bookings` (403) |
| B2 | High | "Current" bookings include stays long past (no date filter, no sort) | `/current_bookings` | `GET /current_bookings` |
| B3 | High | Delete from the bookings list sends a GET, which returns 405 | `/current_bookings` | `GET /tour_package/{id}/delete` |
| B4 | Medium | The Tour column is blank for most bookings | `/current_bookings` | `GET /current_bookings` |
| B5 | Medium | "Payments Made" column shows the payment policy, not payments | `/current_bookings` | page render |
| B6 | Medium | Policy text is badly formatted ("10percentage deposit…", "0 days before arrival: 0 …") | `/current_bookings` | page render |
| B7 | Medium | 103 queries for 15 rows (N+1) and no pagination | `/current_bookings` | `GET /current_bookings` |
| B8 | Low | Empty cells are blank instead of `-` | `/current_bookings` | page render |
| B9 | Low | CSV export includes action-button text; fake sample row shown when empty | `/current_bookings` | Export CSV button |
| B10 | Low | Deleting a hotel package right after creating it crashes the activity log | (any package delete) | `TourPackage::delete()` → `Trackable.php:302` |
| O1 | High | No one can open `/past_offers` (permission doesn't exist) | `/past_offers` | `GET /past_offers` (403) |
| O2 | High | Running and future "Requested" tours are listed as past offers | `/past_offers` | `GET /past_offers?page=N` |
| O3 | High | Delete from the past offers list sends a GET, which returns 405 | `/past_offers` | `GET /tour/{id}/delete` |
| O4 | Medium | Some tours appear in neither current nor past offers | `/past_offers`, `/current_offers` | `GET /past_offers`, `GET /current_offers` |
| O5 | Medium | Rows are sorted by ID, not by date | `/past_offers` | `GET /past_offers` |
| O6 | Medium | Search, sort and CSV export only cover the current page | `/past_offers` | search box, sort headers, Export CSV |
| O7 | Low | Empty cells show `�` (view saved in Windows-1252) | `/past_offers` | page render |
| O8 | Low | CSV includes action-button text; fake sample row shown when empty | `/past_offers` | Export CSV button |
| O9 | Low | Extra tour query per row on every page | `/past_offers` | `GET /past_offers` |
| CP1 | Critical | Delete (and edit/show) on a policy row targets the whole **tour** | `/cancellation_policies` | `/tour/{tour_id}/delete` from a policy row |
| CP2 | High | No one can open `/cancellation_policies` (permission doesn't exist) | `/cancellation_policies` | `GET /cancellation_policies` (403) |
| CP3 | High | Policies from outdated offers are listed next to current ones | `/cancellation_policies` | `GET /cancellation_policies` |
| CP4 | High | Policies of deleted bookings and orphan policies show as blank rows | `/cancellation_policies` | `GET /cancellation_policies` |
| CP5 | Medium | Policy text badly formatted ("45percentage", "150fixed", "0 … 0") | `/cancellation_policies` | page render |
| CP6 | Medium | SIN / DOU / TRI columns are always empty (hard-coded) | `/cancellation_policies` | page render |
| CP7 | Medium | Tour column blank when the offer has no `tour_id` or the tour is deleted | `/cancellation_policies` | page render |
| CP8 | Medium | Unused tours query, 67 queries for 14 rows, no pagination or date filter | `/cancellation_policies` | `GET /cancellation_policies` |
| CP9 | Low | Option Date blank for most rows | `/cancellation_policies` | page render |
| CP10 | Low | CSV includes action-button text; fake sample row (S-301) when empty | `/cancellation_policies` | Export CSV button |
| CP11 | Low | View file isn't UTF-8 | `/cancellation_policies` | `resources/views/offers/cancellation_policies.blade.php` |
| K1 | Critical | Users with no task permissions can edit any task (inline edit, calendar move) | `/task` | `POST /task/{id}/update-field`, `POST /task/{id}/updateCalendar` |
| K2 | Critical | Stored XSS on the task page (title and tour name printed raw) | `/task/{id}` | `GET /task/{id}` |
| K3 | Critical | Users never see tasks assigned to them on their dashboard (`assign` = creator) | `/task/create`, `/home` | `POST /task`, `GET /home` |
| K4 | High | Personal tasks are visible to and editable by every user | `/task`, `/task/{id}`, `/task/{id}/edit` | `GET /task`, `POST /task/{id}/update-field` |
| K5 | High | A bad assignee leaves a half-created task (404 after the task is saved) | `/task/create` | `POST /task` |
| K6 | High | Inline edit and AJAX status change save invalid values; tasks with unknown status vanish from all lists | `/task` | `POST /task/{id}/update-field`, `POST /task/{id}/update` (AJAX `newStatus`) |
| K7 | High | Calendar move with missing or garbage values wipes the deadline; missing ID gives 500 | `/home` calendar, `/task` | `POST /task/{id}/updateCalendar` |
| K8 | Medium | Create saves invalid date, status, task type and tour | `/task/create` | `POST /task` |
| K9 | Medium | Task page crashes (500) for an unknown task type | `/task/{id}` | `GET /task/{id}` |
| K10 | Medium | `?notification_click` with a missing ID gives 500; any user's notification can be marked read | `/task/{id}` | `GET /task/{id}?notification_click={id}` |
| K11 | Medium | Task list sorted by creation date, not deadline | `/task` | `GET /task` |
| K12 | Low | `task/{id}/delete/{tab}` and `task/{id}/deleteMsg` need permissions that don't exist | — | `POST /task/{id}/delete/{tab}`, `GET /task/{id}/deleteMsg` |
| K13 | Low | Helper routes have no permission check | — | `GET /task/statuses/list`, `GET /task/getTasksBlock` |
| K14 | Low | Empty deadlines show as "now" (same as H6) | `/task`, `/task/{id}` | page render |
| K15 | Low | Task pages are slowish (0.8-0.9 s) | `/task`, `/task/create` | `GET /task`, `GET /task/create` |
| SS1 | Critical | Any logged-in user can download every supplier, including contact data and portal passwords | `/supplier_search`, tour package service lists | `GET /table_service_list` |
| SS2 | Critical | Supplier endpoints that work with no login: offer prices for any package; guests can post supplier comments (raw HTML echoed) | supplier portal / booking pages | `GET /booking/offers/{package_id}/{supplier}`, `POST /TMS-Supplier/add_comment/{id}` |
| SS3 | Critical | Stored XSS in the supplier search results table | `/supplier_search` | `GET /supplier_show` (DataTables) |
| SS4 | High | Search never returns results: results and criteria endpoints are 403 for everyone | `/supplier_search` | `GET /supplier_show`, `GET /supplier_criteria` |
| SS5 | High | Search results return full supplier records (emails, phones, encrypted password) | `/supplier_search` | `GET /supplier_show` |
| SS6 | High | Supplier portal passwords are stored reversibly (`Crypt`), not hashed | TMS supplier login | `POST /TMS-Supplier/login`, `hotels.password` |
| SS7 | High | `restaurants.id` has no AUTO_INCREMENT; new restaurants get id 0, then duplicate-key errors | restaurant create | `restaurants` table |
| SS8 | Medium | "All services" loads 10,742 suppliers per request; 800 MB memory, no time limit | `/supplier_search` | `GET /supplier_show?service=All` |
| SS9 | Medium | `_` and `%` aren't escaped in name search (`_` matches every hotel) | `/supplier_search` | `GET /supplier_show?searchname=_` |
| SS10 | Medium | Unknown `service_type_id` gives 500 | — | `GET /table_service_list?service_type_id=99` |
| E1 | Critical | Users with no event permissions can edit any event | `/event/{id}/edit` | `POST /event/{id}/update` |
| E2 | High | Mass assignment on create/update (`id`, `created_at` can be set) | `/event/create`, `/event/{id}/edit` | `POST /event`, `PUT /event/{id}` |
| E3 | High | Event create/edit forms never show validation errors | `/event/create`, `/event/{id}/edit` | `POST /event`, `PUT /event/{id}` |
| E4 | High | Attachments accept any file type (SVG saved) | `/event/create`, `/event/{id}/edit` | `POST /event` (`attach[]`) |
| E5 | High | Event page crashes (500) for a missing ID | `/event/{id}` | `GET /event/999999` |
| E6 | Medium | Criteria IDs not validated (non-existent ids and `0` saved) | `/event/create`, `/event/{id}/edit` | `POST /event` (`criterias`) |
| E7 | Medium | City lookup by name ignores country ("Atlantis" + AT matched a ZA city) | `/event/create`, `/event/{id}/edit` | `POST /event` (`city`) |
| E8 | Medium | Valid names rejected (`'`, accents) | `/event/create`, `/event/{id}/edit` | `POST /event`, `PUT /event/{id}` |
| E9 | Medium | Delete button in the list opens the event instead of deleting it | `/event` | `GET /event/{id}` from the delete button |
| E10 | Medium | Event pages are slow (1.2-1.5 s) | `/event`, `/event/create` | `GET /event`, `GET /event/create` |
| E11 | Low | Update wipes `city`/criterias when the field isn't sent | — | `PUT /event/{id}`, `POST /event/{id}/update` |
| E12 | Low | `public/storage` is a plain file locally, so all `/storage/...` attachments 404 | `/event/{id}` (attachments) | `GET /storage/uploads/{file}` |
| RS1 | Critical | The "in use" delete check **deletes** the record it should protect (app-wide) | `/restaurant` (and `/event`, guides, transfers, flights, cruises, drivers, criteria, rates) | `DELETE /restaurant/{id}`, `POST /restaurant/{id}/delete` |
| RS2 | Critical | New restaurants' criteria and attachments are saved against restaurant **0** | `/restaurant/create` | `POST /restaurant` |
| RS3 | Critical | Restaurant creation breaks permanently after the newest restaurant is soft-deleted (duplicate primary key) | `/restaurant/create` | `POST /restaurant` |
| RS4 | Critical | Users with no restaurant permissions can edit any restaurant | `/restaurant/{id}/edit` | `POST /restaurant/{id}/update` |
| RS5 | High | Stored XSS on the restaurant page (address, comments, internal comments) | `/restaurant/{id}` | `GET /restaurant/{id}` |
| RS6 | High | Restaurant create/edit forms never show validation errors | `/restaurant/create`, `/restaurant/{id}/edit` | `POST /restaurant`, `PUT /restaurant/{id}` |
| RS7 | High | Mass assignment on create/update (`created_at` etc.) | `/restaurant/create`, `/restaurant/{id}/edit` | `POST /restaurant`, `PUT /restaurant/{id}` |
| RS8 | High | Attachments accept any file type (SVG saved) | `/restaurant/create` | `POST /restaurant` (`attach[]`) |
| RS9 | Medium | A deleted restaurant's name can never be reused (unique rule counts soft-deleted rows) | `/restaurant/create` | `POST /restaurant` |
| RS10 | Medium | Valid names rejected (`'`, accents) | `/restaurant/create`, `/restaurant/{id}/edit` | `POST /restaurant`, `PUT /restaurant/{id}` |
| RS11 | Medium | Delete button sends a GET, which returns 405 | `/restaurant` | `GET /restaurant/{id}/delete` |
| RS12 | Medium | Criteria IDs not validated (`999999`, `0` saved) | `/restaurant/create` | `POST /restaurant` (`criterias`) |
| RS13 | Medium | Restaurant pages are slow (1.1-1.2 s) | `/restaurant`, `/restaurant/create` | `GET /restaurant`, `GET /restaurant/create` |
| G1 | Critical | Deleting a guide that's used in a tour deletes it anyway (RS1 confirmed for guides) | `/guide` | `DELETE /guide/{id}`, `POST /guide/{id}/delete` |
| G2 | Critical | Users with no guide permissions can edit any guide | `/guide/{id}/edit` | `POST /guide/{id}/update` |
| G3 | High | Guide page crashes (500) for a missing ID | `/guide/{id}` | `GET /guide/999999` |
| G4 | High | Mass assignment on create/update (`id`, `created_at` can be set) | `/guide/create`, `/guide/{id}/edit` | `POST /guide`, `PUT /guide/{id}` |
| G5 | High | Guide create/edit forms never show validation errors | `/guide/create`, `/guide/{id}/edit` | `POST /guide`, `PUT /guide/{id}` |
| G6 | High | Attachments accept any file type (SVG saved) | `/guide/create` | `POST /guide` (`attach[]`) |
| G7 | Medium | Guide names can't contain digits or accents | `/guide/create`, `/guide/{id}/edit` | `POST /guide`, `PUT /guide/{id}` |
| G8 | Medium | `contact_name` has no validation rule at all | `/guide/create`, `/guide/{id}/edit` | `POST /guide`, `PUT /guide/{id}` |
| G9 | Medium | Criteria IDs not validated (`999999`, `0` saved) | `/guide/create` | `POST /guide` (`criterias`) |
| G10 | Medium | Delete button sends a GET, which returns 405 | `/guide` | `GET /guide/{id}/delete` |
| I1 | High | No one can open the images page or upload (permissions don't exist) | `/images/index` | `GET /images/index`, `POST /images/savefile` (403) |
| I2 | High | Uploading several files at once keeps only the last one | `/images/index` | `POST /images/savefile` |
| I3 | High | Uploading a tour image deletes all of the tour's other attachments | `/images/index`, tour pages | `POST /images/savefile` (`model=Tour`) |
| I4 | High | Category images point to a hard-coded old domain; new uploads store absolute URLs | `/images/index` | page render, `POST /images/savefile` |
| I5 | Medium | Upload failures return the raw exception message | `/images/index` | `POST /images/savefile` |
| I6 | Low | On edit pages, an upload changes every image; file name inserted as HTML | pages with "edit" in the URL using `attachments.js` | `public/js/attachments.js` |
| I7 | Low | Two upload inputs per card wired to the same handler | `/images/index` | page render |
| BC1 | Critical | One bus day with an unknown status breaks the whole calendar (500) for everyone | `/bus/calendar` | `GET /api/bus_days` (after `POST /api/add_day` with a bad `status`) |
| BC2 | Critical | "View only" users can create, edit and delete bus schedules | `/bus/calendar` | `POST /api/add_day`, `POST /bus_day/update`, `POST /bus_day/delete` |
| BC3 | Critical | Stored XSS in the calendar tooltip (trip, bus and driver names) | `/bus/calendar` | `GET /api/bus_days` → `public/js/lib/chart_bus.js:326` |
| BC4 | High | Bus, driver and status IDs not validated; trips with missing/no bus become invisible | `/bus/calendar` | `POST /api/add_day`, `POST /bus_day/update` |
| BC5 | High | No limit on trip length (400-day trip = 400 rows in one request) | `/bus/calendar` | `POST /api/add_day` |
| BC6 | High | Update with an unknown status crashes in the activity log (500) | `/bus/calendar` | `POST /bus_day/update` |
| BC7 | Medium | Past trips can't be viewed (fixed window: this month + 6 months) | `/bus/calendar` | `GET /api/bus_days` |
| BC8 | Medium | Trip create echoes the whole request; busy messages hard-coded in English | `/bus/calendar` | `POST /api/add_day` |
| BC9 | Medium | Calendar data built with heavy per-row queries (`LIKE '%name%'` hotel lookups) | `/bus/calendar` | `GET /api/bus_days` |
| BC10 | Low | Calendar page loads every bus, driver, tour and transfer (206 KB) | `/bus/calendar` | `GET /bus/calendar` |
| TR1 | Critical | Deleting a bus company that's used in a tour deletes it anyway (RS1 confirmed for transfers) | `/transfer` | `DELETE /transfer/{id}`, `POST /transfer/{id}/delete` |
| TR2 | Critical | Users with no transfer permissions can edit any bus company | `/transfer/{id}/edit` | `POST /transfer/{id}/update` |
| TR3 | High | Bus company page crashes (500) for a missing ID | `/transfer/{id}` | `GET /transfer/999999` |
| TR4 | High | Mass assignment on create/update (`id`, `created_at` can be set) | `/transfer/create`, `/transfer/{id}/edit` | `POST /transfer`, `PUT /transfer/{id}` |
| TR5 | High | Bus company forms never show validation errors | `/transfer/create`, `/transfer/{id}/edit` | `POST /transfer`, `PUT /transfer/{id}` |
| TR6 | High | Attachments accept any file type (SVG saved) | `/transfer/create` | `POST /transfer` (`attach[]`) |
| TR7 | Medium | Deleting a bus company leaves its buses and drivers active | `/transfer`, `/bus`, `/bus/calendar` | `DELETE /transfer/{id}` |
| TR8 | Medium | Valid names rejected (umlauts, apostrophes) | `/transfer/create`, `/transfer/{id}/edit` | `POST /transfer`, `PUT /transfer/{id}` |
| TR9 | Medium | Criteria IDs not validated (`999999`, `0` saved) | `/transfer/create` | `POST /transfer` (`criterias`) |
| TR10 | Medium | Delete button sends a GET, which returns 405 | `/transfer` | `GET /transfer/{id}/delete` |
| BU1 | Critical | Users with no bus permissions can edit any bus (the edit form's own route is unprotected) | `/bus/{id}/edit` | `POST /bus/{id}/update` |
| BU2 | High | Saving a bus shows a raw JSON page instead of returning to the list | `/bus/create`, `/bus/{id}/edit` | `POST /bus`, `POST /bus/{id}/update` |
| BU3 | High | Buses can be attached to a deleted bus company | `/bus/create`, `/bus/{id}/edit` | `POST /bus` (`transfer_id`) |
| BU4 | Medium | A deleted bus's number can never be reused | `/bus/create` | `POST /bus` (`bus_number`) |
| BU5 | Medium | Delete button sends a GET, which returns 405 | `/bus` | `GET /bus/{id}/delete` |
| BU6 | Medium | Bus number has no format check and is shown raw in the bus calendar tooltip | `/bus/create`, `/bus/calendar` | `POST /bus` → `GET /api/bus_days` |
| BU7 | Low | `capacity` and `comments` are validated but never saved | `/bus/create`, `/bus/{id}/edit` | `POST /bus`, `POST /bus/{id}/update` |
| BU8 | Low | Umlauts rejected in bus names | `/bus/create`, `/bus/{id}/edit` | `POST /bus` |
| DR1 | Critical | Deleting a driver assigned to trips deletes them **permanently** (no soft delete) | `/driver` | `POST /driver/{id}/delete`, `DELETE /driver/{id}` |
| DR2 | Critical | Deleting an unused driver always returns 500, though the driver is deleted | `/driver` | `POST /driver/{id}/delete` |
| DR3 | High | Stored XSS on the driver page (name, phone, email, company) | `/driver/{id}` | `GET /driver/{id}` |
| DR4 | High | Any logged-in user can list a bus company's drivers and buses | (tour/bus forms) | `GET /driver_bus_transfer/api/{id}`, `GET /driver_bus_transfer/table/api/{id}` |
| DR5 | High | Saving a driver shows a raw JSON page | `/driver/create`, `/driver/{id}/edit` | `POST /driver`, `PUT /driver/{id}` |
| DR6 | High | Unknown company ID causes a database error instead of a form error | `/driver/create` | `POST /driver` (`transfer_id`) |
| DR7 | Medium | Very weak validation (no name length/format, phone `abc`, duplicate emails) | `/driver/create`, `/driver/{id}/edit` | `POST /driver`, `PUT /driver/{id}` |
| DR8 | Medium | Drivers can be attached to a deleted bus company | `/driver/create` | `POST /driver` (`transfer_id`) |
| DR9 | Medium | Delete button sends a GET, which returns 405 | `/driver` | `GET /driver/{id}/delete` |
| DR10 | Low | Unused `getButton()` builds routes with the wrong parameter name | — | `DriverController::getButton()` |
| DR11 | Low | Four finance tables use the `latin1` character set | (invoices, office fees) | `client_invoices`, `invoices_tours`, `office_fees`, `supplier_invoices` |
| ST1 | Critical | Users with no status permissions can edit any status (including hard-coded ones) | `/status/{id}/edit` | `POST /status/{id}/update` |
| ST2 | High | Stored XSS on the status page (name and color) | `/status/{id}` | `GET /status/{id}` |
| ST3 | High | A status in use can be switched to another type | `/status/{id}/edit` | `PUT /status/{id}` |
| ST4 | High | New task statuses can never be "Completed" or "Aborted" | `/status/create`, `/status/{id}/edit` | `POST /status`, `PUT /status/{id}` |
| ST5 | High | Status type not validated (unknown types saved, status becomes invisible) | `/status/create` | `POST /status` (`type`) |
| ST6 | High | Statuses the code depends on by ID are not protected | `/status` | `PUT /status/{id}`, `POST /status/{id}/delete` |
| ST7 | Medium | Offer statuses in use (stored by name) are deleted anyway | `/status` | `POST /status/{id}/delete` |
| ST8 | Medium | Duplicate statuses (same name and type) allowed | `/status/create` | `POST /status` |
| ST9 | Medium | Sort order rounded (-5.5 → -6), long names silently cut | `/status/create` | `POST /status` |
| ST10 | Medium | Color is free text used inside `style` attributes | `/status/create` | `POST /status` (`color`) |
| ST11 | Low | Delete button sends a GET, which returns 405 | `/status` | `GET /status/{id}/delete` |
| ST12 | Low | AJAX show returns a task URL | `/status/{id}` | `GET /status/{id}` (AJAX) |
| RT1 | Critical | Users with no room-type permissions can edit any room type | `/room_types/{id}/edit` | `POST /room_types/{id}/update` |
| RT2 | Critical | SVG images with scripts are accepted and served publicly from the app's domain | `/room_types/create`, `/room_types/{id}/edit` | `POST /room_types` (`image`) → `GET /assets/img/svg/{file}.svg` |
| RT3 | High | Changing the `DOU`/`SIN` code crashes package price calculations | `/room_types/{id}/edit` | `PUT /room_types/{id}` → `TourPackage::getDoublePrice()` |
| RT4 | High | Stored XSS on the room-type page (name, code) | `/room_types/{id}` | `GET /room_types/{id}` |
| RT5 | High | Duplicate codes and names allowed (a second `DOU` was saved) | `/room_types/create` | `POST /room_types` |
| RT6 | Medium | Long names silently cut to 191 characters | `/room_types/create` | `POST /room_types` |
| RT7 | Medium | Delete button sends a GET, which returns 405 | `/room_types` | `GET /room_types/{id}/delete` |
| RT8 | Medium | Room-type deletes are permanent (no soft delete) | `/room_types` | `POST /room_types/{id}/delete` |
| RT9 | Low | `sort_order` accepts decimals and negatives | `/room_types/create` | `POST /room_types` |
| RT10 | Low | Unused `getButton()` builds routes with the wrong parameter name | — | `RoomTypesController::getButton()` |
| RA1 | Critical | Deleting a rate used by hotels deletes it **permanently** (inverted guard, no soft delete) | `/rate` | `POST /rate/{id}/delete`, `DELETE /rate/{id}` |
| RA2 | Critical | Rates used by events, restaurants, guides or bus companies are never checked before delete | `/rate` | `POST /rate/{id}/delete` |
| RA3 | Critical | Users with no rate permissions can edit any rate (the edit form's own route is unprotected) | `/rate/{id}/edit` | `POST /rate/{id}/update` |
| RA4 | High | Stored XSS on the rate page (name, mark) | `/rate/{id}` | `GET /rate/{id}` |
| RA5 | High | Rate type not validated (`999999` saved; real data has `"NULL"` strings) | `/rate/create`, `/rate/{id}/edit` | `POST /rate`, `POST /rate/{id}/update` |
| RA6 | Medium | Duplicate rate names allowed | `/rate/create` | `POST /rate` |
| RA7 | Medium | Long names silently cut to 191 characters | `/rate/create` | `POST /rate` |
| RA8 | Medium | Delete button sends a GET, which returns 405 | `/rate` | `GET /rate/{id}/delete` |
| RA9 | Low | `sort_order` accepts decimals/negatives; unused `getButton()` has the wrong route parameter | `/rate/create` | `POST /rate`, `RateController::getButton()` |
| CR1 | Critical | Users with no currency-rate permissions can edit any rate (the edit form's own route is unprotected) | `/currency_rate/{id}/edit` | `POST /currency_rate/{id}/update` |
| CR2 | High | Currency rates are not used anywhere in the app (latest real data from 2012) | `/currency_rate` | — |
| CR3 | High | Rates rounded to 2 decimals and silently capped at 999999.99 (`double(8,2)`) | `/currency_rate/create`, `/currency_rate/{id}/edit` | `POST /currency_rate` |
| CR4 | High | Stored XSS on the currency-rate page | `/currency_rate/{id}` | `GET /currency_rate/{id}` |
| CR5 | Medium | Rate `0` and negative rates accepted | `/currency_rate/create` | `POST /currency_rate` |
| CR6 | Medium | Currency is free text (not checked against `currencies`) | `/currency_rate/create` | `POST /currency_rate` |
| CR7 | Medium | Duplicate currency + date allowed | `/currency_rate/create` | `POST /currency_rate` |
| CR8 | Medium | Far-future dates accepted (2099) | `/currency_rate/create` | `POST /currency_rate` |
| CR9 | Low | Delete button sends a GET, which returns 405 | `/currency_rate` | `GET /currency_rate/{id}/delete` |
| CU1 | Critical | Deleting a currency in use deletes it **permanently** with no check (packages/offers show a bare id) | `/currencies` | `POST /currencies/{id}/delete`, `DELETE /currencies/{id}` |
| CU2 | Critical | Users with no currency permissions can edit any currency (the edit form's own route is unprotected) | `/currencies/{id}/edit` | `POST /currencies/{id}/update` |
| CU3 | High | Stored XSS on the currency page (name, code, symbol, cent) | `/currencies/{id}` | `GET /currencies/{id}` |
| CU4 | High | Duplicate currency codes allowed (a second `EUR` was saved) | `/currencies/create` | `POST /currencies` |
| CU5 | Medium | Currency code not validated as an ISO code | `/currencies/create` | `POST /currencies` (`code`) |
| CU6 | Medium | `cent` / `symbol_cent` not validated | `/currencies/create` | `POST /currencies` |
| CU7 | Medium | Delete button sends a GET, which returns 405 | `/currencies` | `GET /currencies/{id}/delete` |
| CU8 | Low | Create page loads every currency without using them | `/currencies/create` | `GET /currencies/create` |
| CU9 | Low | Unused `getButton()` has a copy-pasted parameter name | — | `CurrenciesController::getButton()` |
| TP1 | Critical | Tour package edit page crashes (500) for packages with an empty `pax_free` (75 of 391) | `/tour_package/{id}/edit` | `GET /tour_package/{id}/edit` |
| CT1 | Critical | Criterion names are printed raw on supplier pages (stored XSS spreads to restaurants, cruises, flights, reports) | `/criteria/create` → `/restaurant/{id}` and 6 other pages | `POST /criteria` → `GET /restaurant/{id}` |
| CT2 | Critical | Deleting a criterion in use deletes it **permanently** (inverted guard, no soft delete) | `/criteria` | `POST /criteria/{id}/delete`, `DELETE /criteria/{id}` |
| CT3 | Critical | Users with no criteria permissions can edit any criterion (the edit form's own route is unprotected) | `/criteria/{id}/edit` | `POST /criteria/{id}/update` |
| CT4 | High | Stored XSS on the criteria page (name, short name, icon) | `/criteria/{id}` | `GET /criteria/{id}` |
| CT5 | High | Criteria type not validated (`999999` saved) | `/criteria/create` | `POST /criteria` (`criteria_type`) |
| CT6 | High | The type of a criterion in use can be changed | `/criteria/{id}/edit` | `POST /criteria/{id}/update` |
| CT7 | Medium | Duplicate criteria (same name and type) allowed | `/criteria/create` | `POST /criteria` |
| CT8 | Medium | Criteria list has no pagination and renders a template per row | `/criteria` | `GET /criteria` |
| CT9 | Medium | Delete button sends a GET, which returns 405 | `/criteria` | `GET /criteria/{id}/delete` |
| EM1 | Critical | Built-in email client can't work: the `ddeboer/imap` library is not installed | `/email` (mailbox pages) | `GET /email/remove/…`, `/email/attachmentList/…`, `/email/another`, `php artisan` ParseEmails |
| EM2 | Critical | Four users' mailbox passwords are stored in plain text | `/users/{id}/edit` (mailbox fields) | `users.email_password` |
| EM3 | High | `email.main` permission is not enforced on any `/email` page | `/email`, `/email/configure` | `GET /email`, `GET /email/configure`, … |
| EM4 | High | Three users can't log into webmail (encrypted password, flag not set) | `/email/webmail` | `GET /email/webmail` (SSO) |
| EM5 | High | Webmail password falls back to the server-wide `IMAP_PASSWORD` | `/email/webmail` | `GET /email/webmail` (SSO) |
| EM6 | High | Emails can be deleted with a plain GET link (CSRF) | `/email/mail/{id}` | `GET /email/remove/{id}/{folder}` |
| EM7 | Medium | Three email pages crash on undefined view variables | `/email` (compose, move, template) | `GET /email/getComposeForm`, `/email/getMoveToForm`, `/email/template` |
| EM8 | Medium | Pages needing a mailbox silently redirect without explaining why | `/email` | `GET /email/folder/INBOX`, `/email/search`, `/email/readAll` |
| EM9 | Low | Webmail status endpoint open to any user | `/email/webmail/status` | `GET /email/webmail/status` |
| EM10 | Critical | SnappyMail config (admin password hash), domain and cache files download with no login; also committed to git | `/email/webmail` (SnappyMail behind it) | `GET /snappymail/data/_data_/_default_/configs/application.ini`, `…/domains/*.json`, `…/cache/*` |
| EM11 | High | SnappyMail admin panel is open to anyone, including guests; the Laravel admin-only check is bypassed | `/email/webmail/admin` | `GET /snappymail/?admin` |
| EM12 | High | Users without a mailbox get an endless redirect loop (`ERR_TOO_MANY_REDIRECTS`) | `/email/webmail` | `GET /email/webmail` → 302 → `/email/webmail` … |
| EM13 | Medium | "Direct" and "Admin" webmail links go to `/mail`, which is a 404; status reports the wrong URL | `/email/webmail/direct`, `/email/webmail/admin`, `/email/webmail/status` | `GET /mail`, `GET /mail?admin` (404) |
| EM14 | Medium | Mailbox credentials aren't checked on save, and the "saved" message is never shown | `/email/configure` → `/email/webmail` | `POST /email/configure` |
| EM15 | Medium | "Not configured" sends the user back with "contact administrator" instead of to `/email/configure` | `/email/webmail` | `GET /email/webmail` |
| EM16 | Low | Configure page uses old Bootstrap 3 / AdminLTE markup, wrong button order, loses input on error | `/email/configure` | page render |
| EM17 | Low | Webmail feature flags are ignored; dead SSO code; mailbox address in logs; hard-coded SnappyMail path | `/email/webmail` | — |
| EM18 | Low | Mail "View all" link in the old email layout points to `snappymail.eu` (relative, broken) | `/email` (header mail dropdown) | `GET /email/snappymail.eu` (404) |
| CO1 | Critical | Comment HTML is rendered raw in every comment thread (tours, tasks, suppliers, clients, invoices) | comment section on any record | `POST /comment` → `POST /comment/generate-comments` |
| CO2 | Critical | Any regular user can edit or delete anyone's comments, and change the author | comment section on any record | `PUT /comment/{id}`, `POST /comment/{id}/delete` |
| CO3 | Critical | Users with no comment permissions can read any record's comment thread | comment section on any record | `POST /comment/generate-comments` |
| CO4 | High | Mass assignment on comments (`id`, `status`, `created_at`; `author_id` on update) | comment section | `POST /comment`, `PUT /comment/{id}` |
| CO5 | High | Comment on a non-existent record is saved, then the request and its page return 500 | comment section, `/comment/{id}` | `POST /comment`, `GET /comment/{id}` |
| CO6 | Medium | Deleting a missing comment reports success | `/comment` | `POST /comment/{id}/delete` |
| CO7 | Medium | Comment deletes are permanent, including every reply | comment section | `POST /comment/{id}/delete` |
| CO8 | Medium | Comment attachments have no file-type rule | comment section | `POST /comment` (`attach[]`) |
| CO9 | Low | Comment helper routes are unnamed (can't carry permissions) | — | `GET /comment/{id}/delete_msg`, `POST /comment/generate-comments` |
| CL1 | Critical | Users with no client permissions can change client details and the client-portal password | `/clients/{id}/edit` | `POST /clients/{id}/update` |
| CL2 | Critical | Any logged-in user can read any client's contacts | client edit/show pages | `GET /api/getClientContacts?clientId={id}` |
| CL3 | High | Stored XSS on the client page (name, address, phones, emails, contacts) | `/clients/{id}` | `GET /clients/{id}` |
| CL4 | High | Mass assignment on client create (`id`, `created_at`, `dep_date`) | `/clients/create` | `POST /clients` |
| CL5 | High | Attachments on client create accept any file type | `/clients/create` | `POST /clients` (`attach[]`) |
| CL6 | High | Clients in use (tours, invoices) can be deleted without warning | `/clients` | `POST /clients/{id}/delete` |
| CL7 | Medium | Resource delete needs a non-existent permission; delete button sends a GET (405) | `/clients` | `DELETE /clients/{id}`, `GET /clients/{id}/delete` |
| CL8 | Medium | Two client-portal passwords stored in plain text; all are reversible | `/clients/create`, `/clients/{id}/edit` | `clients.password` |
| CL9 | Medium | A valid accented company name was rejected on create with no visible reason (needs manual retry) | `/clients/create` | `POST /clients` |
| AC1 | High | Client invoices and their items are deleted permanently (no soft delete / audit trail) | `/accounting` | `POST /accounting/{id}/delete` |
| AC2 | Medium | "Service transaction" creation is 403 for everyone (permission doesn't exist) | tour page → service transaction | `GET /accountingServiceTransaction/create/{tour_id}` |
| AC3 | Medium | Delete button sends a GET, which returns 405 | `/accounting` | `GET /accounting/{id}/delete` |
| AC4 | Medium | Invoice list has no pagination; 85 queries for 26 invoices | `/accounting` | `GET /accounting` |
| AC5 | Low | Quotation lookup has no permission check (returns a quotation id) | accounting create/edit | `GET /accounting/api/getTourquotation/{tour_id}` |
| IN1 | High | Supplier invoice extra amount can never be paid (payments capped at `total_amount`) | `/invoices/create`, `/add_payment/{id}` | `POST /invoices`, `POST /payment_store/{id}` |
| IN2 | High | Duplicate supplier invoice numbers allowed | `/invoices/create` | `POST /invoices` |
| IN3 | High | An invoice can be linked to another tour's services | `/invoices/create` | `POST /invoices` (`package_id{tour}[]`) |
| IN4 | High | Package dropdown has no permission check, outputs names unescaped, 500 for unknown tour | `/invoices/create` | `GET /supplierdropdown/{tour_id}` |
| IN5 | Medium | Due and received dates are set automatically and can't be entered | `/invoices/create` | `POST /invoices` |
| IN6 | Medium | Invoice list: 1.45 s, 555 queries for 117 rows, no pagination | `/invoices` | `GET /invoices` |
| IN7 | Medium | Show page crashes (500) for a link to a missing invoice | `/invoices/{id}` | `GET /invoices/{id}` |
| IN8 | Medium | Supplier invoices are deleted permanently | `/invoices` | `POST /invoices/{id}/delete` |
| IN9 | Low | URLs mix invoice-tour link ids and invoice ids | `/invoices/{id}`, `/add_payment/{id}` | — |
| RP1 | High | Every supplier report page is 403 for everyone, admin included (permissions don't exist) | `/reporting` (View links) | `GET /reporting/{hotel,restaurant,event,guide}/{id}/show` |
| RP2 | High | Supplier search endpoints are 403 for everyone | `/reporting` | `GET /reporting/{id}`, `GET /reporting_supplier_show` |
| RP3 | Medium | Only admin and Accountant can open the reporting list | `/reporting` | `GET /reporting` |
| RP4 | Medium | Unused `getTotalAmountForDateRange()` queries a column that doesn't exist | — | `ReportingController::getTotalAmountForDateRange()` |
| RP5 | Low | `_` and `%` are not escaped in the name search | `/reporting` | `GET /reporting?q=_` |
| RP6 | Low | An unknown service filter is silently ignored | `/reporting` | `GET /reporting?service=Bogus` |
| AT1 | High | Nobody can open the activity log (route name `activities_index` vs permission `activities.index`) | `/activities`, dashboard "View all activities" | `GET /activities` |
| AT2 | Medium | Activity log has no pagination (2,578 rows, 2.5 MB HTML, 1.6 s) and loads the log twice | `/activities` | `GET /activities` |
| AT3 | Medium | "View" buttons point to the hard-coded `https://dev.eetstravel.com` domain | `/activities` | stored `activity_log.properties.link` |
| AT4 | Low | "View" button is built as raw HTML from the stored link | `/activities` | `ActivitiesController@index` → `{!! $activity->link_button !!}` |
| AT5 | Low | Everyone with access sees every user's actions (no user/module filter) | `/activities` | `GET /activities` |

Fixed during this QA session:

| Issue | URL | Status |
|-------|-----|--------|
| Announcement preview crashed (`foreach() argument must be of type array\|object, null given`) | `/announcements/{id}` (e.g. `/announcements/37`) | Fixed in `resources/views/announcements/childs.blade.php` |
| Task list Previous/Next reloaded the whole page | `/home` | Changed to AJAX (`tasks_list.blade.php`, `AppController@dashboard`) |

---

# Part 1: `/home` dashboard

## H1. Critical: opening `/home` logs the user out

- **URL:** `/home`. The Latest Tours widget calls `GET /api/v1/dashboard/tours` when the page loads.
- **What happens:** every `/api/v1/*` request returns 401 and replaces the user's session cookie with an unencrypted one. On the next request that cookie can't be decrypted, so the user is logged out.

**Steps to reproduce:**
1. Log in.
2. `GET /api/v1/dashboard/tours` returns 401 "Authentication required".
3. `GET /home` now redirects (302) to `/login`.

**Cause:** in `routes/api.php:89` the v1 group uses `['web', 'auth', 'api.permissions']` inside the `api` group. Laravel's middleware sorting then runs `StartSession` before `EncryptCookies`:

```
StartSession → ShareErrorsFromSession → Authenticate → ThrottleRequests → SubstituteBindings → EncryptCookies → ...
```

**Affected widgets:** Latest Tours, Inbox emails, Activities, Chat groups, plus every other `/api/v1/*` call.

**Possible fix:** add `EncryptCookies` (and `AddQueuedCookiesToResponse`) at the top of `$middlewarePriority` in `app/Http/Kernel.php`, or serve these routes through the `web` group only.

---

## H2. Critical: Calendar, Latest Tours and Activities are always empty

- **URL:** `/home`, through these endpoints:

| Endpoint | Result |
|----------|--------|
| `GET /home/getToursTasksForCalendar` | 403 (`PermissionsRequiredMiddleware`, route permission `dashboard.index`) |
| `GET /home/getToursForCalendar` | 403 |
| `GET /api/v1/dashboard/tours` | 403 (`ApiPermissions` maps `DashboardController` to `dashboard.index`) |
| `GET /api/v1/dashboard/activities` | 403 |
| `GET /api/v1/dashboard/chat_groups` | 403 |

- **What happens:** the `dashboard.index` permission does not exist in the `permissions` table, so every user is refused, admins included.
- **Evidence:** the existing admin (user 1) has been refused `dashboard.index` 37 times in `storage/logs`.
- **UX impact:** the calendar JS turns a 403 into an empty event list and only logs to the console. Users see an empty calendar with no message.

**Possible fix:** create a `dashboard.index` permission and assign it to the right roles, or point the routes and `ApiPermissions` at existing `dashboard.*` permissions.

---

## H3. Critical: dashboard chat is completely blocked

- **URL:** `/home` (Main chat / Direct chat widgets).
- **What happens:** `ChatController::__construct` applies `permissions.required` to every action, using the route name as the permission. Most of those names don't exist.

| Route | Missing permission |
|-------|-------------------|
| `GET /chat/{id}/renderChat` | `chat.renderChat` |
| `GET /chat/{id}/renderDirectChat` | `chat.renderDirectChat` |
| `POST /chat/message` (send message) | `chat.post` |
| `GET /chat/getChatNotifications` (polled repeatedly) | `chat.getChatNotifications` |
| `GET /chat/{id}/getMessage` | `chat.getMessage` |
| `GET /chat/getOrCreateChat` | `chat.getOrCreateChat` |
| `GET /chat/createCustomChat`, `addUserToCustomChat`, `removeUserFromChat` | same pattern |
| `POST /chat/deleteChat`, `/chat/{id}/destroy_chat`, `DELETE /chat/{chat}` | same pattern |

**Evidence:** `chat.getChatNotifications` was refused 599 times for user 1 in the logs.

**Possible fix:** give these routes an explicit `'permissions' => 'chat.main'` (or another existing `chat.*` permission), or add the missing permissions.

---

## H4. High: stored XSS in the header notification dropdown

- **URL:** `/home`, and every page that uses the Tabler header or the `app` layout.
- **What happens:** task content is printed without escaping:
  - `resources/views/scaffold-interface/layouts/tabler-header.blade.php:117`: `{!! $task->tourNameNotification() !!}`
  - `resources/views/scaffold-interface/layouts/app.blade.php:113`: same

**Steps to reproduce:** create a task titled `<img src=x onerror=alert(1)>` and open `/home`. The rendered HTML contains:

```html
<div class="d-block text-body">Task [QA] <img src=x onerror=alert("xss-task")> task without tour</div>
```

**Possible fix:** use `{{ $task->tourNameNotification() }}`, or escape `content` and the tour name inside `Task::tourNameNotification()`.

---

## H5. High: the task Delete button on the dashboard doesn't work

- **URL:** `/home` (task list widget), which sends `DELETE /task/{id}`.
- **What happens:** `tasks_list.blade.php` sends `DELETE /task/${taskId}`, but the only delete route is `task/{id}/delete` (`routes/web.php:327`). The server answers `405 Method Not Allowed` and the task is not deleted.

**Possible fix:** change the URL to `/task/${taskId}/delete`, or use `route('task.destroy', ...)`.

---

## H6. Medium: tasks without a deadline show as "due now" and overdue

- **URL:** `/home` (task list and calendar).
- **What happens:** `app/Task.php:115`:

```php
public function getDeadLineAttribute($value)
{
    return Carbon::parse($value)->format('Y-m-d H:i');
}
```

`Carbon::parse(null)` returns the current time. A task with `dead_line = NULL` therefore shows today's date and time, gets the `overdue` class, and appears on the calendar at the current time.

**Possible fix:** `return $value ? Carbon::parse($value)->format('Y-m-d H:i') : null;`

---

## H7. Low

- **URL:** `/home`.
- **Announcement order is random when timestamps tie.** `AppController@dashboard` sorts only by `created_at`, so the 5-item widget may drop the newest announcement. Add `->orderBy('announcements.id', 'desc')`.
- **`quickCreateCalendarEvent` (`POST /calendar/quick-create`) is broken, though the UI doesn't use it.** It calls `$task->users()`, which doesn't exist (the relation is `assigned_users()`), and it never sets `assign`.
- **`$.post(...).success()` in `direct_chat.blade.php:215`.** `.success()` was removed in jQuery 3; use `.done()`.

---

# Part 2: `/users`

## U1. Critical: any logged-in user can create, edit and delete any user

- **URLs:** `/users`, `/users/create`, `/users/{id}`, `/users/{id}/edit`, plus `POST /users`, `POST /users/{id}`, `DELETE /users/{id}`.
- **What happens:** the resource routes (`routes/web.php:641-642`) have only the `web` middleware, and `UserController` adds only `auth`. No permission is checked.

**Result as the regular user (`qa.agent@eets.test`, role `user`):**

| Action | Result |
|--------|--------|
| `GET /users`, `/users/create`, `/users/{id}/edit`, `/users/{id}` | 200 |
| `POST /users` | user created |
| `POST /users/179` with a new password | **admin's password changed** |
| `DELETE /users/183` | **user deleted** |

**Possible fix:** add `permissions.required` to the resource routes, mapped to `users.index`, `users.create`, `users.edit`, `users.destroy`, `users.show`. Those permissions exist, but they would also need to be assigned to the admin role (see U4).

---

## U2. Critical: editing user A can change user B

- **URL:** `/users/{id}/edit`, which posts to `POST /users/{id}`.
- **What happens:** `UserController@update` loads the user with `$request->user_id`, a hidden form field, and ignores the `{id}` in the URL (`UserController.php:154`).
- **Steps to reproduce:** `POST /users/181` with `user_id=183`, `name=[QA] Tampered via user_id`. User 183 is renamed.
- **Impact:** combined with U1, any user can rewrite any account's email and password.

**Possible fix:** use `User::findOrFail($id)` and drop the hidden `user_id`.

---

## U3. Critical: SVG avatar upload allows stored XSS

- **URLs:** `/users/create` and `/users/{id}/edit` (avatar field); the file is served at `/uploads/avatars/{time}_{name}.svg`.
- **What happens:** the `image` validation rule accepts SVG. An SVG containing `<script>alert(...)</script>` was saved to `public/uploads/avatars/`. `GET /uploads/avatars/1793119267_evil.svg` returns 200 with `image/svg+xml`, so the script runs on the app's own domain when the file is opened.
- **Also:** the original client filename is kept in the stored name.

**Possible fix:** validate with `mimes:jpg,jpeg,png,gif,webp`, and store with a generated name (`$file->hashName()`).

---

## U4. Critical: role and permission management is broken for everyone

- **URL:** `/users/{id}/edit` (Roles and Permissions sections, and the delete-confirmation popup).

| Request | Result for the admin |
|---------|---------------------|
| `POST /users/addRole` | 403 |
| `POST /users/removeRole` | 403 |
| `POST /users/addPermission` | 403 |
| `GET /users/removePermission/{id}/{key}` | 403 |
| `GET /users/{id}/deleteMsg` | 403 |

**Cause:**
- These routes sit in the `perm` group, which uses the route name as the permission (`users.addRole`, `user.remove_role`, `users.addPermission`, `users.revokePermission`, `users.deleteMsg`). None of those permissions exist.
- The `admin` role also has **none** of the existing `users.*` permissions (`users.index/create/edit/destroy/show`), so the legacy `POST /users/{id}/delete` (`users.destroy`) is refused too.

**Possible fix:** give these routes explicit permissions (e.g. `users.edit`), and assign the `users.*` permissions to the admin role.

---

## U5. High: saving the edit form wipes profile data

- **URL:** `/users/{id}/edit`, which posts to `POST /users/{id}`.
- **What happens:** the form only sends email, name, password and avatar, but `update()` always sets `location`, `education` and `note` from the request.
- **Steps to reproduce:** set location, education and note on a user, then save their edit form without changes. All three become `null`.

**Possible fix:** only update those fields when they are in the request (`$request->has('location')`), or add them to the form.

---

## U6. High: saving a user always turns `email_server` on

- **URL:** `/users/{id}/edit`, which posts to `POST /users/{id}`.
- **What happens:** `update()` sets `$user->email_server = 1` unconditionally (`UserController.php:161`). A user with `email_server = 0` becomes `1` after any save, so `/home` then treats their mailbox as configured.

**Possible fix:** set it from `$request->email_server` only when that field is sent.

---

## U7. High: deleting a user shows an error toast even though it worked

- **URL:** `/users` (red delete button), which sends `DELETE /users/{id}`.
- **What happens:**
  1. The button sends `fetch(DELETE /users/{id})`.
  2. The user is deleted, and the controller returns `redirect('users')`.
  3. `fetch` follows the redirect still using DELETE, so it requests `DELETE /users`, which returns 405.
  4. The UI shows the error "Method not allowed for this endpoint." although the user is gone.
- **Self-delete:** it is correctly blocked, but the message also comes back through the redirect, so the UI result is confusing.

**Possible fix:** for AJAX requests, return `response()->json(['success' => true, 'message' => ...])` from `destroy()` (and `success: false` for self-delete).

---

## U8. Medium: any password is accepted

- **URLs:** `/users/create` (`POST /users`) and `/users/{id}/edit` (`POST /users/{id}`).
- **What happens:** `password=1` was accepted. There is no minimum length and no confirmation field.

**Possible fix:** `'password' => 'required|min:8|confirmed'` on create, and `nullable|min:8|confirmed` on edit.

---

## U9. Medium: long names are silently cut

- **URL:** `/users/create`, which posts to `POST /users`.
- **What happens:** a 300-character name was accepted and stored as 191 characters, with no error shown.

**Possible fix:** add `max:191` to the `name` rule (and to `email`).

---

## U10. Medium: duplicate email on edit gets a vague database error

- **URL:** `/users/{id}/edit`, which posts to `POST /users/{id}`.
- **What happens:** `update()` validates `email` only as `required|email`. Changing it to another user's email hits the database unique index, and the user sees "This record already exists. Please check your input and try again." instead of a field error.

**Possible fix:** `'email' => 'required|email|unique:users,email,' . $id`.

---

## U11. Low: the "No roles" / "No permissions" badges never show

- **URL:** `/users`.
- **What happens:** `index.blade.php` uses `@if(!empty($user->roles))`. A collection is never `empty()`, so users with no roles get an empty cell instead of the badge.

**Possible fix:** `@if($user->roles->isNotEmpty())`, and the same for permissions.

---

## U12. Low: the users list has no pagination and runs N+1 queries

- **URL:** `/users`.
- **What happens:** `User::all()` loads every user. Roles and permissions are then queried separately for each row. With 42 users, the page is about 245 KB and takes about 1.2 s.

**Possible fix:** `User::with(['roles', 'permissions'])->paginate(25)`.

---

# Part 3: `/roles`

## R1. Critical: any logged-in user can create, rename and delete roles

- **URLs:** `/roles`, `/roles/create`, `/roles/{id}`, `/roles/{id}/edit`, plus `POST /roles/store`, `POST /roles`, `POST /roles/update`, `PUT /roles/{id}`, `DELETE /roles/{id}`.
- **What happens:** the resource routes (`routes/web.php:644-646`) have only the `web` middleware, and `RoleController` adds only `auth`. No permission is checked. Also, no `roles.*` permissions exist in the `permissions` table at all.

**Result as the regular user (`qa.agent@eets.test`, role `user`):**

| Action | Result |
|--------|--------|
| `GET /roles`, `/roles/create`, `/roles/13/edit` | 200 |
| `GET /roles/1/edit` (the **admin** role) | 200 |
| `POST /roles/store` | role created |
| `POST /roles/update` (`role_id=13`) | role renamed |
| `DELETE /roles/21` | role deleted |

**Possible fix:** create `roles.index/create/edit/destroy` permissions, assign them to the admin role, and add `permissions.required` to all role routes.

---

## R2. Critical: any user can give a role they hold more permissions

- **URLs:** `/roles/{id}/edit` (Permissions section), which sends `POST /roles/addPermission` and `GET /roles/removePermission/{perm}/{role}`.
- **What happens:** these two routes (`routes/web.php:522-523`) have no route name and no `permissions` key, so `PermissionsRequiredMiddleware` lets every logged-in user through.

**Steps to reproduce:**
1. `qa.agent` holds `user` plus `[QA] Agent Role`.
2. As `qa.agent`: `POST /roles/addPermission` with `role_id=<[QA] Agent Role>` and `permission_name[]=users.index`.
3. `qa.agent->can('users.index')` is now **true**.

**Impact:** any user can grant themselves any permission through a role they hold. Combined with R1 and R4, they can also rename or delete `admin`.

**Possible fix:** give both routes explicit permissions (e.g. `roles.edit`).

---

## R3. High: removing a permission is a state-changing GET link

- **URL:** `/roles/{id}/edit` (trash icon in the Permissions table), which sends `GET /roles/removePermission/{perm}/{role}`.
- **What happens:** a GET request removes permissions, so it has no CSRF protection. Any link or `<img src>` on a page a logged-in user visits can strip permissions from a role. The same pattern exists at `GET /users/removePermission/{user_id}/{key}`.

**Possible fix:** change it to `POST`/`DELETE` with `@csrf`.

---

## R4. High: the `admin` role can be renamed or deleted

- **URLs:** `/roles/1/edit` → `POST /roles/update`; `/roles` → `DELETE /roles/1`.
- **What happens:** there is no protection for system roles, and 15 places in `app/` and `resources/views` check for a role named exactly `admin` (e.g. `hasRole('admin')` in `AppController` and `Api\DashboardController`). Renaming or deleting it would quietly turn off admin behaviour across the app.
- **Verified on QA roles only:** rename and delete both work with no checks. The real `admin` role was not changed.

**Possible fix:** block renaming and deleting `admin` (and `user`) in `RoleController@update`/`destroy`, and hide those actions in the UI.

---

## R5. High: adding several permissions is not all-or-nothing

- **URL:** `/roles/{id}/edit`, which sends `POST /roles/addPermission`.
- **What happens:** `addPermission()` loops over `permission_name[]` and calls `givePermissionTo()` one at a time. If one name doesn't exist, the request fails with a 500, but the permissions before it are already saved.
- **Evidence:** a request with `users.index`, `roles.index` and `settings.index` saved `users.index`, then failed on `roles.index` with "There is no permission named `roles.index`".

**Possible fix:** validate with `'permission_name.*' => 'exists:permissions,name'`, then call `givePermissionTo($request->permission_name)` once inside a transaction.

---

## R6. Medium: a duplicate role name crashes with a 500 page

- **URL:** `/roles/create`, which sends `POST /roles/store`.
- **What happens:** `store()` validates only `name => required`. A duplicate name throws Spatie's `RoleAlreadyExists`, which shows a 500 error page with the raw exception. The check ignores case, so `[qa] role editor` and `ADMIN` also fail this way.

**Possible fix:** `'name' => 'required|max:191|unique:roles,name'`.

---

## R7. Medium: a permission name that doesn't exist causes a 500

- **URL:** `/roles/{id}/edit`, which sends `POST /roles/addPermission` and `GET /roles/removePermission/no.such.permission/{role}`.
- **What happens:** both return 500 "There is no permission named `no.such.permission` for guard `web`" instead of a validation error.

**Possible fix:** validate against `exists:permissions,name` (see R5).

---

## R8. Medium: deleting a role in use gives no warning

- **URL:** `/roles` (red delete button), which sends `DELETE /roles/{id}`.
- **What happens:** `qa.spaces` held only `[QA] Role ToDelete`. After that role was deleted, `qa.spaces` had **no roles** at all. There was no warning about how many users would be affected.
- **Live usage:** `user` has 30 users, `admin` 4, `Super User` 3.

**Possible fix:** show the number of affected users in the confirmation, or refuse to delete a role that still has users.

---

## R9. Medium: long role names are silently cut

- **URL:** `/roles/create`, which sends `POST /roles/store`.
- **What happens:** a 300-character name was accepted ("Role ... created" shows the full name) but stored as 191 characters.

**Possible fix:** add `max:191` to the `name` rule on create and update.

---

## R10. Medium: renaming to an existing name gets a vague database error

- **URL:** `/roles/{id}/edit`, which sends `POST /roles/update`.
- **What happens:** renaming a role to an existing name hits the database unique index. The user sees "This record already exists. Please check your input and try again." instead of a field error.

**Possible fix:** `'name' => 'required|max:191|unique:roles,name,' . $request->role_id`.

---

## R11. Low

- **URL:** `/roles`.
- **Action button styles:** the index uses `btn-ghost-warning` (orange) for Edit and `btn-ghost-danger` for Delete. CLAUDE.md requires Edit to be a blue filled button and Delete a red filled button.
- **"No permissions" badge never shows:** `@if(!empty($role->permissions))` is always true for a collection. Use `->isNotEmpty()`.
- **`GET /roles/{id}/deleteMsg`** returns an empty 200 page unless it is called by AJAX.
- **Legacy `POST /roles/{id}/delete`** (`routes/web.php:838`) requires `roles.destroy`, which doesn't exist, so it returns 403 for everyone, admin included. The UI doesn't use it; remove the route or fix the permission.

---

# Part 4: `/permissions`

## P1. Critical: a guest who isn't logged in can create, rename and delete permissions

- **URLs:** `/permissions`, `/permissions/create`, `/permissions/{id}/edit`, plus `POST /permissions/store`, `POST /permissions`, `POST /permissions/update`, `PUT /permissions/{id}`, `DELETE /permissions/{id}`.
- **What happens:** `PermissionController::__construct` applies `auth` **only to `show()`**:

```php
$this->middleware('auth')->only('show');
```

The route group (`routes/web.php:650-652`) is just `web`, so every other action is open to anyone.

**Result with no login at all:**

| Action | Result |
|--------|--------|
| `GET /permissions`, `/permissions/create`, `/permissions/{id}/edit` | 200 |
| `GET /permissions/{id}` | 302 to `/login` (the only protected action) |
| `POST /permissions/store` | permission `qa.perm.byguest` created |
| `POST /permissions/update` | permission `qa.perm.alpha` renamed to `qa.perm.alpha.guest` |
| `DELETE /permissions/{id}` | permission deleted |

**Impact:** anyone who can reach the server can rename or delete any of the 250 real permissions (e.g. `tour.index`), cutting that access for every role and user. They can also flood the table.

**Possible fix:** `$this->middleware('auth');` for all actions, plus `permissions.required` on the routes, with new `permissions.index/create/edit/destroy` permissions assigned to the admin role.

---

## P2. Critical: any logged-in user can create, rename and delete permissions

- **URLs:** same as P1.
- **What happens:** even after P1 is fixed by adding `auth`, there is still no permission check. As the regular user (`qa.agent@eets.test`, role `user`):
  - `GET /permissions`, `/permissions/create`, `/permissions/1/edit` → 200,
  - `POST /permissions/store` created a permission,
  - `POST /permissions/update` edited one,
  - `DELETE /permissions/{id}` deleted one.

**Possible fix:** see P1. Restrict to admin-level permissions.

---

## P3. High: renaming a permission in use silently changes who has access

- **URL:** `/permissions/{id}/edit`, which sends `POST /permissions/update`.
- **What happens:** code and routes check permissions by **name** (`can('tour.index')`, route `permissions` keys, `PermissionHelper`). `update()` lets the name change freely, with no warning about roles that hold it.

**Steps to reproduce:**
1. `qa.created` holds `[QA] Agent Role`, which has `qa.perm.inuse`; `can('qa.perm.inuse')` is `yes`.
2. Rename `qa.perm.inuse` to `qa.perm.inuse.renamed`.
3. `can('qa.perm.inuse')` is now `no`.

Renaming a real permission such as `tour.index` would lock every role out of that page.

**Possible fix:** make `name` read-only after creation and allow editing only `alias`, or at least warn and list the roles that use it.

---

## P4. High: the "permission in use" delete guard is bypassed by every delete form

- **URL:** `/permissions` (red delete button), which sends `DELETE /permissions/{id}`.
- **What happens:** `destroy()` correctly refuses to delete a permission assigned to roles or users unless `force=1` is sent. But `index.blade.php` puts `<input type="hidden" name="force" value="1">` in **every** delete form (256 of 256). The only safeguard is the browser confirm dialog.
- **Verified:**
  - `DELETE` without `force` → "Cannot delete permission ... It is assigned to roles: [QA] Agent Role." (good).
  - With `force=1` → deleted and removed from the role.

**Possible fix:** only send `force=1` after the user confirms the in-use warning, e.g. set it from the confirm handler instead of hard-coding it.

---

## P5. Medium: a duplicate permission name crashes with a 500 page

- **URL:** `/permissions/create`, which sends `POST /permissions/store`.
- **What happens:** `store()` validates only `required`.
  - Exact duplicate (`qa.perm.alpha`) → 500 "A `qa.perm.alpha` permission already exists for guard `web`."
  - Different case (`QA.PERM.ALPHA`) → database error "This record already exists. Please check your input and try again."
- `update()` already uses `unique:permissions,name,{id}` and gives a proper field error, so only create is affected.

**Possible fix:** `'name' => 'required|max:191|unique:permissions,name'`.

---

## P6. Medium: no format check on permission names

- **URLs:** `/permissions/create` (`POST /permissions/store`), `/permissions/{id}/edit` (`POST /permissions/update`).
- **What happens:** `qa perm with spaces!@#` was accepted. All real permissions follow `module.action` (e.g. `tour.index`), and the route middleware builds names in that form, so a malformed name can never match a route.

**Possible fix:** `'name' => ['required', 'regex:/^[a-z0-9_]+(\.[a-z0-9_-]+)+$/']`.

---

## P7. Medium: long permission names are silently cut

- **URL:** `/permissions/create`, which sends `POST /permissions/store`.
- **What happens:** a 308-character name was accepted and stored as 191 characters, with no error.

**Possible fix:** add `max:191` to `name` on create and update.

---

## P8. Medium: the permissions list is heavy

- **URL:** `/permissions`.
- **What happens:** all 256 permissions render on one page, each with its own delete form and a confirm message built from its roles. The response is **870 KB** and takes about **1.5 s**.

**Possible fix:** paginate (`Permission::with('roles:id,name')->paginate(50)`) or use the DataTables server-side pattern used elsewhere, and add a search box.

---

## P9. Low: creating a permission shows no success message

- **URL:** `/permissions/create`, which sends `POST /permissions/store`.
- **What happens:** `store()` returns `redirect('permissions')` with no flash message. Update shows "Permission updated successfully" and delete shows "Permission deleted successfully".

**Possible fix:** add `->with('success', 'Permission created successfully')` (or `LaravelFlashSessionHelper`).

---

## P10. Low: legacy delete route always returns 403

- **URL:** `POST|DELETE /permissions/{id}/delete` (`routes/web.php`, `'permissions' => 'permissions.destroy'`); also the target of `GET /permissions/{id}/deleteMsg`.
- **What happens:** the `permissions.destroy` permission doesn't exist, so this route returns 403 for everyone, admin included. The index page doesn't use it (it posts to `DELETE /permissions/{id}`), but the `deleteMsg` modal points to it.

**Possible fix:** remove the legacy route and `deleteMsg`, or create the permission.

---

## P11. Low: Edit/Delete button styles don't follow the UI rules

- **URL:** `/permissions`.
- **What happens:** Edit uses `btn-ghost-warning` (orange outline) and Delete uses `btn-ghost-danger`. CLAUDE.md requires Edit to be a blue filled button and Delete a red filled button. Same as R11 on `/roles`.

---

# Part 5: `/settings`

The real settings (`tour_service_notification`, `hotel_seasons_date`, `client_fio_text` and two test rows) were only read, never changed. Because no real user can open `/settings` (S1), functional tests used `qa.created@eets.test` with a temporary `[QA] Settings Role` holding `settings.index/create/show/edit/destroy`.

## S1. High: no one can open `/settings`

- **URLs:** `/settings`, `/settings/create`, `/settings/{id}/edit` (routes named `settings.*`, checked by `PermissionsRequiredMiddleware`).
- **What happens:** the permissions `settings.index`, `settings.create`, `settings.show`, `settings.edit`, `settings.destroy` exist, but **no role holds them**, including `admin`, and no user has them directly.

| User | `GET /settings` |
|------|-----------------|
| `qa.admin` (role `admin`) | 403 |
| `qa.agent` (role `user`) | 403 |
| guest | 302 to `/login` |

**Possible fix:** assign the `settings.*` permissions to the `admin` role (e.g. in a seeder or migration).

---

## S2. High: setting values aren't type-checked

- **URL:** `/settings/{id}/edit`, which sends `PUT /settings/{id}`.
- **What happens:** the edit page shows `<input type="number" min="0">` for day-count settings, but `update()` only validates `value => required`. A direct request saved `abc` and `-5`.
- **Impact on scheduled jobs:**
  - `TourPackageNotification` uses `tour_service_notification` in `Carbon::now()->addDays($value)`.
  - `TaskDeadlineNotification` uses the task-deadline setting the same way.
  - In a dry run, `addDays('abc')` raised no error and behaved as 0 days, so notifications would silently stop. A negative value moves the window into the past.

**Possible fix:** validate by setting type: `integer|min:0` for day counts, `date_format:Y-m-d H:i:s` for `*date*` settings.

---

## S3. High: mass assignment on create

- **URL:** `/settings/create`, which sends `POST /settings`.
- **What happens:** `store()` calls `Setting::create($request->all())`, and `App\Setting` has `protected $guarded = [];`. Extra fields not on the form are saved.
- **Steps to reproduce:** `POST /settings` with `name=qa_setting_massassign`, `description=...`, `value=1`, `id=987654`, `created_at=2000-01-01 00:00:00`.
- **Result:** the row was saved with **id 987654** and **created_at 2000-01-01**, and the table's AUTO_INCREMENT jumped to 987656 (the next setting got id 987655).

**Possible fix:** `Setting::create($request->only(['name', 'description', 'value']))`, and set `$fillable` on the model.

---

## S4. High: settings the app depends on can be deleted

- **URL:** `/settings` (red delete button), which sends `DELETE /settings/{id}`.
- **What happens:** any setting can be deleted, including ones the code reads by name. `RunImport` does:

```php
$parseFrequency = App\Setting::where('name', 'hotel_seasons_date')->first();
if (Carbon::now()->format('Y-m-d H:i:00') !== $parseFrequency->value) {
```

- **Impact:** with `hotel_seasons_date` gone, `$parseFrequency` is null, and every run of the import command fails with "Attempt to read property "value" on null". This was confirmed with a dry run; the real setting was not deleted.

**Possible fix:** block deleting the system settings (`tour_service_notification`, `hotel_seasons_date`, `client_fio_text`, the task-deadline setting) and null-check in `RunImport`.

---

## S5. Medium: the create form never shows validation errors

- **URL:** `/settings/create`, which sends `POST /settings`.
- **What happens:** `resources/views/settings/create.blade.php` has no `$errors` output. Submitting empty fields, or a duplicate name, redirects back with no message, so the user doesn't know why nothing was saved.
- The edit page has the same gap: an empty description/value redirects back with no visible error.

**Possible fix:** add the standard `@if ($errors->any())` alert block used on other create forms, and `old()` values on the inputs.

---

## S6. Medium: missing IDs crash with a 500

- **URLs:** `/settings/{id}/edit` (`PUT /settings/{id}`), `/settings` (`DELETE /settings/{id}`).

| Request | Result |
|---------|--------|
| `PUT /settings/999999` | 500 "Attempt to assign property "description" on null" |
| `DELETE /settings/999999` | 500 "Call to a member function delete() on null" |
| `GET /settings/999999/edit` | 404 (correct) |

**Possible fix:** use `Setting::findOrFail($id)` in `update()` and `destroy()`.

---

## S7. Medium: no format check on setting names

- **URL:** `/settings/create`, which sends `POST /settings`.
- **What happens:** the code looks settings up by `name` (e.g. `tour_service_notification`), but any string is accepted:
  - `qa setting spaces !@#` was saved,
  - a 300-character name was stored cut to 191 characters, with no error.

**Possible fix:** `'name' => ['required', 'max:191', 'regex:/^[a-z0-9_]+$/', 'unique:settings']`.

---

## S8. Low: the edit success message is broken

- **URL:** `/settings/{id}/edit`, which sends `PUT /settings/{id}`.
- **What happens:** `update()` builds the message from `$setting->desc`, but the column is `description`. The toast always reads **"Setting  edited"**.

**Possible fix:** use `$setting->description`.

---

## S9. Low: delete shows no success message

- **URL:** `/settings`, which sends `DELETE /settings/{id}`.
- **What happens:** `destroy()` redirects to the list with no flash message; create and edit do show one.

---

## S10. Low: `GET /settings/{id}` returns an empty page

- **URL:** `/settings/{id}` (e.g. `/settings/1`).
- **What happens:** `show()` has an empty body, so the route returns a blank 200 page (0 bytes).

**Possible fix:** remove `show` from the resource routes (`->except('show')`) or render `resources/views/settings/show.blade.php`.

---

## S11. Low: edit page and buttons don't follow the UI rules

- **URLs:** `/settings/{id}/edit`, `/settings`.
- **What happens:**
  - The edit form has **two Save buttons**: one at the top (`edit.blade.php:28`, next to Back) and one at the end (`edit.blade.php:67`). CLAUDE.md says to remove the top Save and keep Back on the left and Save at the form end.
  - Cancel is a yellow `btn-warning` button.
  - On the list, Edit is `btn-ghost-warning` and Delete is `btn-ghost-danger`, instead of blue and red filled buttons.

---

## S12. Low: the list has no Name column

- **URL:** `/settings`.
- **What happens:** the table shows only Description and Value. The setting key (`name`), which the code depends on and which decides the edit input type (date / text / number), isn't visible, so it's hard to tell which setting is being edited.

---

# Part 6: `/tour`

Tested as `qa.admin` (admin), `qa.agent` (role `user`, which has full `tour.*`), `qa.created` (no tour permissions at all) and a guest. Only QA tours were created, edited or deleted.

## T1. Critical: users with no tour permissions can create and edit tours

- **URLs:** the create form at `/tour/create` posts to **`POST /tour/save`**; the edit form at `/tour/{id}/edit` posts to **`POST /tour/{id}/update`**. Also `POST /tour/{id}/updateCalendar`.
- **What happens:** `TourController` uses `permissions.required`, which takes the permission from the route name. These three routes have **no name**, so there is nothing to check and every logged-in user passes. The named resource routes (`GET /tour`, `/tour/create`, `/tour/{id}/edit`) are protected; the routes the forms actually submit to are not.

**Result as `qa.created` (no `tour.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /tour`, `/tour/create`, `/tour/133`, `/tour/133/edit` | 403 (correct) |
| `POST /tour/save` | **tour 137 created** |
| `POST /tour/133/update` | **tour 133 renamed** to "QA Edited Without Permission #1106" |
| `POST /tour/133/updateCalendar` | **dates changed** (200 `true`) |

**Possible fix:** name these routes, or give them explicit permissions: `tour/save` → `tour.create`, `tour/{id}/update` and `updateCalendar` → `tour.edit`.

---

## T2. Critical: export, clone, convert and PDF are 403 for everyone

- **URL:** the action buttons on `/tour/{id}`, which send:

| Request | Permission required (missing) |
|---------|-------------------------------|
| `GET /tour/{id}/export/html` | `tour_html_export` |
| `GET /tour/{id}/export/{pdf_type}` | `tour_pdf_export` |
| `GET /tour/{id}/doc/{doc_type}` | `tour_doc_export` |
| `GET /tour/{id}/api/{export}/{type?}` | `tour_export` |
| `GET /tour/{id}/clone` | `tour_clone` |
| `GET /tour/{id}/convert_to_quotation` | `tour.convertToQuotation` |
| `GET /tour/{id}/convert_to_tour` | `tour.convert_to_tour` |
| `GET /tour/{id}/pdf_data` | `pdf_data` |
| `POST /tour/{id}/generatePackages` | `generate_packages` |
| `GET /tour_achieve_data` | `tour_achieve_data` |

- **What happens:** none of these permissions exist, so every user gets 403, admin included.
- **Evidence:** the real admin (user 1) is refused `tour_html_export` and `tour_pdf_export` in `storage/logs`.

**Possible fix:** give these routes explicit existing permissions (e.g. `tour.show` for exports, `tour.create` for clone, `tour.edit` for convert/generate).

---

## T3. High: tours that started over 30 days ago can't be edited at all

- **URL:** `/tour/{id}/edit`, which posts to `POST /tour/{id}/update`.
- **What happens:** `UpdateTourRequest` requires `departure_date after_or_equal: now - 30 days`. Saving `[QA] Past Vienna Tour` (departure 40 days ago) **with the dates unchanged** fails with "Departure date cannot be more than 30 days in the past". Finished or running tours can't get any correction (phone, remark, status, pax).

**Possible fix:** apply the past-date limit only when the departure date is being changed, or drop it on update.

---

## T4. High: inline edit saves invalid values

- **URL:** inline fields on `/tour/{id}`, which send AJAX `POST /tour/{id}/update` with `fieldName` / `fieldValue`.
- **What happens:** in inline mode, `UpdateTourRequest` validates only `name` strictly; every other field in `INLINE_FIELDS` is just `nullable|max:191`. `pax` and `pax_free` are `varchar(191)` columns, so the database accepts anything too.

| Inline edit | Response | Saved |
|-------------|----------|-------|
| `pax=abc` | 200 | yes |
| `pax=-5` | 200 | `-5` |
| `pax_free=999` (pax = 22) | 200 | `999` |
| `status=999999` (no such status) | 200 | `999999` |
| `external_name=` (empty) | 200 | `""` |
| `name=<b>x</b>` | 422 | no (correct) |
| `fieldName=deleted_at` | 422 | no (correct) |

**Possible fix:** per-field inline rules matching the full form (`pax` integer 1-500, `pax_free` integer `lte` pax, `status` `exists:status,id`, `external_name` unique and required).

---

## T5. High: inline edit creates junk `City` records

- **URL:** inline city field on `/tour/{id}`, which sends AJAX `POST /tour/{id}/update` with `cityName`.
- **What happens:** `update()` runs `City::create(['code' => fieldValue, 'name' => cityName, 'country' => countryAlias])` when no city matches the code, with no validation.
- **Evidence:** `cityName=[QA] Fake City <script>alert(1)</script>`, `fieldValue=QA<script>`, `countryAlias=ZZ` created **city 26468**, and the cities count went from 26467 to 26468. The name wasn't seen rendered unescaped in `/tour` or `/tour/{id}`, but any user can pollute the shared cities table.

**Possible fix:** only allow picking existing cities (`exists:cities,id`), or validate code, name and country before creating one.

---

## T6. High: any tour's landing page is public by numeric ID

- **URL:** `/tour/{id}/landingpage` (`TourController` excludes `landingPage` from `auth`).
- **What happens:** a guest can open `/tour/133/landingpage` (200). It shows the tour name, phone (`+43 1 234567`) and external reference (`EETSAT233`). The page loads by `Tour::findOrFail($id)`, so anyone can walk sequential IDs to see every tour. The `tours.share_token` column (unique, `char(40)`) exists but isn't used.

**Possible fix:** serve landing pages by `share_token` (e.g. `/tour/landing/{share_token}`) and stop accepting the numeric ID.

---

## T7. High: deleting a tour hard-deletes its invoices and quotations

- **URL:** delete button on `/tour`, which sends `POST|DELETE /tour/{id}/delete`.
- **What happens:** `TourController@destroy` soft-deletes the tour (`tours.deleted_at`), but then **permanently** deletes related records:

```php
Quotation::query()->where('tour_id', $id)->delete();
ClientInvoices::query()->where('tour_id', $id)->delete();
InvoicesTours::query()->where('invoices_tours_id', $id)->delete();
```

(also bus days, driver/bus transfers and comments). A restored tour comes back without its invoices and quotations, and financial records are lost. This was verified by reading the code; no real invoices were deleted.

**Possible fix:** block deleting tours that have invoices, or soft-delete the related financial records too.

---

## T8. Medium: edit requires a phone, but create never asks for one

- **URLs:** `/tour/create` (form has no phone field) and `/tour/{id}/edit`, which posts to `POST /tour/{id}/update`.
- **What happens:** `StoreTourRequest` has `phone` nullable, and `store()` saves `''`. `UpdateTourRequest` has `phone` **required**, and `external_name` required too. Every newly created tour fails its first full edit with "Contact phone number is required" until one is added.

**Possible fix:** make `phone` nullable on update too, or add it to the create form.

---

## T9. Medium: tour names reject common characters

- **URLs:** `/tour/create` (`POST /tour/save`), `/tour/{id}/edit` (`POST /tour/{id}/update`).
- **What happens:** the name rule `regex:/^[a-zA-Z0-9\s\-#]+$/` rejects realistic names:

| Name | Result |
|------|--------|
| `St. Petersburg Tour` | rejected |
| `QA O'Brien Group` | rejected |
| `QA Zürich Trip` | rejected |
| `QA Smith & Sons` | rejected |

**Possible fix:** allow `.` `'` `&` `/` `,` `(` `)` and Unicode letters (`/^[\pL\pN\s\-#.'&\/,()]+$/u`). Output is already escaped, so a strict whitelist isn't needed for XSS.

---

## T10. Medium: every user sees and can edit every tour

- **URLs:** `/tour`, `/tour/{id}`, `/tour/{id}/edit`.
- **What happens:** `qa.agent` (role `user`) sees the admin's QA tours in `/tour` and can open and edit `/tour/133`, which they aren't assigned to. The dashboard API (`/api/v1/dashboard/tours`) shows non-admins only their assigned tours, so the two disagree. Confirm which behaviour is intended.

---

## T11. Medium: unbounded `child_count`; unvalidated `room_types_qty`

- **URL:** `/tour/create`, which posts to `POST /tour/save`.
- **What happens:**
  - `child_count` isn't validated, and `store()` loops that many times. `child_count=40` with no ages created **40 `childrens` rows** with age 0 and price 0. A very large value would insert that many rows in one request.
  - `room_types_qty[...]` isn't validated: `room_types_qty[999999]=5` (no such room type) and `room_types_qty[1]=-3` were both saved to `tour_room_type_hotels`.

**Possible fix:** `child_count => integer|min:0|max:50`, `ages.*`/`prices.*` numeric, `room_types_qty.*` integer `min:0` with keys checked against `room_types`.

---

## T12. Medium: the PUT edit route rejects every save

- **URL:** `PUT /tour/{id}` (resource route `tour.update`). The UI uses `POST /tour/{id}/update` instead.
- **What happens:** `UpdateTourRequest` ignores the current tour in its unique check with `$this->route('id')`, but this route's parameter is `{tour}`. The ignore is null, so saving a tour with its own unchanged external name fails with "This external name is already taken by another tour".

**Possible fix:** `$this->route('id') ?? $this->route('tour')`.

---

## T13. Medium: tour pages are slow

- **URLs and timings (local, about 57 tours):**

| URL | Time | Size |
|-----|------|------|
| `/tour` | 1.77 s | 215 KB |
| `/tour/create` | 1.85 s | — |
| `/tour/{id}/edit` | 1.46 s | 215 KB |

- **What happens:** `TourController@index` runs `$allToursQuery->get()` (every tour) and paginates in PHP for several sections. This will get slower as tours grow.

**Possible fix:** paginate in the database (`->paginate()`), or load the tables through DataTables server-side.

---

## T14. Low: validation messages don't match the problem

- **URLs:** `/tour/create` (`POST /tour/save`), `/tour/{id}/edit` (`POST /tour/{id}/update`).

| Input | Message shown |
|-------|---------------|
| Departure after return | "Departure date cannot be more than 2 years in the future." |
| Duplicate external name on edit | "External name is required for system integration." |

**Possible fix:** fix the custom `messages()` keys (`departure_date.before_or_equal` is shared by two rules; use separate rules/messages, and `external_name.unique` vs `.required`).

---

## T15. Low: deleting a missing tour gives no message

- **URL:** `POST /tour/999999/delete`.
- **What happens:** it redirects back with no error message.

---

## T16. Low: `notification_click` marks any user's notification as read

- **URL:** `/tour/{id}?notification_click={notificationId}`.
- **What happens:** `show()` loads `Notification::find($request->notification_click)` and sets `click = true`, without checking that the notification belongs to the current user.

**Possible fix:** look it up through `Auth::user()->notifications()`.

---

## T17. High: `updateCalendar` writes unvalidated dates and can set them to NULL

- **URL:** tour calendar drag/resize, which sends `POST /tour/{id}/updateCalendar`.
- **What happens:** `TourController@updateCalendarTour` writes the request straight into the tour, with no validation:

```php
Tour::query()->where('id', $id)->update([
    'departure_date' => $request->get('start_date'),
    'retirement_date' => $request->get('end_date'),
]);
```

- **Found during the `/current_offers` test:**
  - The `/tour` test sent `departure_date`/`retirement_date` (wrong field names) to `POST /tour/133/updateCalendar`.
  - The response was 200 `true`, but **both dates on tour 133 were set to NULL**, so the tour disappeared from date-based lists such as `/current_offers`.
  - That request came from `qa.created`, who has no tour permissions (see T1). Tour 133's dates were restored afterwards.
- **Also:**
  - It doesn't check that start is before end.
  - It doesn't update the tour days (`createUpdateTourDates`), so `tour_days` drift out of step with the new dates.

**Possible fix:** validate `start_date`/`end_date` as `required|date` with `start_date <= end_date`, refresh the tour days, and protect the route with `tour.edit`.

---

# Part 7: `/current_offers`

The page is 403 for every user (C1), so its content was also tested by rendering `OfferController@current_offers` in-process as `qa.admin`, skipping only the route middleware. 6 edge-case tours (ids 138-143, "[QA] Offer …") were added for this.

## C1. High: no one can open `/current_offers`

- **URL:** `/current_offers` (route `current_offers.index`, `routes/web.php:249`).
- **What happens:** `OfferController` uses `permissions.required`, which needs a `current_offers.index` permission. It doesn't exist; there are **no** offer permissions at all.

| User | `GET /current_offers` |
|------|-----------------------|
| `qa.admin` (admin) | 403 |
| `qa.agent` (user) | 403 |
| guest | 302 to `/login` |

- **Evidence:** the real admin (user 1) is refused `current_offers.index` in `storage/logs`.

**Possible fix:** create `current_offers.index` (and the other `OfferController` permissions) and assign them to admin, or point the route at an existing permission such as `tour.index`.

---

## C2. High: delete from the offers list sends a GET, which returns 405

- **URL:** red delete button on `/current_offers`, from `component.action_buttons` with `routePrefix => 'tour'`.
- **What happens:**
  - The component maps `tour` to the `POST` method.
  - For POST, `submitDeleteRequest()` does `window.location.href = deleteUrl`, which is a **GET** to `/tour/{id}/delete`.
  - That route only accepts `POST|DELETE`, so `GET /tour/143/delete` returns **405**.
- Every page that renders tour rows through `component.action_buttons` is affected.

**Possible fix:** in `submitDeleteRequest()`, send POST with the CSRF token (a hidden form or `fetch`) instead of navigating, as the DELETE branch already does.

---

## C3. Medium: tours still running but ending within 7 days vanish from "current"

- **URL:** `/current_offers`.
- **What happens:** `current_offers()` filters `retirement_date >= now + 1 week`. "[QA] Offer Running Ends In 3 Days" (departed 4 days ago, ends in 3) is **not** listed, though it is still running. Meanwhile `past_offers()` uses `retirement_date < now + 1 week`, so a running tour shows up as a *past* offer.

**Possible fix:** use `retirement_date >= today` for current offers, and `< today` for past offers.

---

## C4. Medium: tours with no status disappear from the list

- **URL:** `/current_offers`.
- **What happens:** the filter `->where('status', '<>', 46)` is never true when `status` is NULL in SQL, so tours without a status are silently excluded. "[QA] Offer No City No Status" (status NULL, departs in 30 days) is missing.

**Possible fix:** `->where(fn($q) => $q->where('status', '<>', 46)->orWhereNull('status'))`.

---

## C5. Medium: cancelled tours are listed as current offers

- **URL:** `/current_offers`.
- **What happens:** only status 46 (Requested) is excluded. "[QA] Offer Cancelled Future" (status 6, Cancelled) is listed as a current offer. Finished (58) would be too, if its return date were in the future.

**Possible fix:** exclude cancelled and finished statuses (`whereNotIn('status', [6, 46, 58])`), or whitelist the offer statuses.

---

## C6. Medium: the list has no pagination

- **URL:** `/current_offers`.
- **What happens:** `current_offers()` uses `->get()` and renders every matching tour, each with action buttons. `past_offers()` already uses `->paginate(10)`. The current render is 0.43 s / 150 KB for 9 rows, and it will grow with the tours table.

**Possible fix:** `->orderBy('departure_date')->paginate(25)`.

---

## C7. Low: empty cells show `�`

- **URL:** `/current_offers`.
- **What happens:** `resources/views/offers/current_offers.blade.php` is saved as Windows-1252, not UTF-8. The fallback for empty city, status, dates, pax and created-at is byte `0x97` (an em dash in Windows-1252), which a UTF-8 page shows as `�`. CLAUDE.md asks for ASCII fallbacks such as `{{ $value ?? '-' }}`.

**Possible fix:** replace the fallbacks with `'-'` and save the file as UTF-8.

---

## C8. Low: CSV export and sample row

- **URL:** `/current_offers` (Export CSV button, `offerExportTable()`).
- **What happens:**
  - The export includes the Actions column, so button text ends up in the CSV.
  - When there are no tours, the page shows a fake "Sample Rome Spring Offer" (ID `S-101`) row, which real users can mistake for data, and which also ends up in the CSV.

**Possible fix:** skip the last column (`.actions-cell`) and `.sample-row` rows in the export, and show a plain "No current offers" empty state instead.

---

# Part 8: `/current_bookings`

The page is 403 for every user (B1), so its content was also tested by rendering `OfferController@current_bookings` in-process as `qa.admin`, skipping only the route middleware. 9 QA hotel bookings (tour packages 556-564, "[QA] Booking …") were added next to the 8 real confirmed bookings.

**Rendered rows (real data first, then QA):**

| ID | Tour | Hotel | City | Stay | Cancellation | Payment |
|----|------|-------|------|------|--------------|---------|
| 46 | Cola Vie-Vie 12D #1026 | Kanigos 21 Hotel | Sumqayıt | 2023-10-27 | N/A | N/A |
| 200 | *(blank)* | Agora Hotel Paris | *(blank)* | 2023-11-24 | N/A | N/A |
| 213 | *(blank)* | Best Western Hotel Etoile Saint Honore | *(blank)* | 2023-11-23 | N/A | 10percentage deposit due 12 days before arrival. |
| 222 | *(blank)* | Cervantes Hotel | *(blank)* | 2024-04-20 | N/A | N/A |
| 556 | QA Edited Without Permission #1106 | [QA] Booking Future Hotel | Salzburg | 2026-11-08 | 7 days before arrival: 50% can be cancelled free of charge. | 30% deposit due 14 days before arrival. |
| 557 | QA Edited Without Permission #1106 | [QA] Booking Past Stay 60 Days Ago | Salzburg | 2026-08-28 | N/A | N/A |
| 559 | *(blank)* | [QA] Booking For Deleted Tour | Salzburg | 2026-11-11 | N/A | N/A |
| 563 | QA Edited Without Permission #1106 | [QA] Booking Empty Policies | Salzburg | 2026-11-22 | 0 days before arrival: 0 can be cancelled free of charge. | 0 deposit due 0 days before arrival. |

(Rows 201-203, 266, 558, 562 and 564 omitted.)

**Re-test (2026-10-27, same session):** no code, route or permission changes since the first run. The results are unchanged:

| URL / request | Result | Issue |
|---------------|--------|-------|
| `GET /current_bookings` (admin and regular user) | still 403 | B1 |
| `GET /current_bookings` (rendered) | still 8 stays from 2023-2024 among 15 rows | B2 |
| `GET /tour_package/564/delete` (delete button) | still 405 | B3 |
| `GET /current_bookings` (rendered) | "10percentage deposit due…" still shown | B6 |
| `GET /current_bookings` (rendered) | `<script>` hotel name still escaped | passed |

## B1. High: no one can open `/current_bookings`

- **URL:** `/current_bookings` (route `current_bookings.index`, `routes/web.php:252`).
- **What happens:** `OfferController` uses `permissions.required`, which needs a `current_bookings.index` permission. It doesn't exist.

| User | `GET /current_bookings` |
|------|-------------------------|
| `qa.admin` (admin) | 403 |
| `qa.agent` (user) | 403 |

**Possible fix:** same as C1. Create the permission and assign it, or map the route to an existing one.

---

## B2. High: "current" bookings include stays long past

- **URL:** `/current_bookings`.
- **What happens:** `current_bookings()` loads `TourPackage::whereIn('status', [23])->get()`, every confirmed hotel package, with **no date filter** and no ordering.
  - All **8 real rows are stays from 2023-10-27 to 2024-04-20**.
  - The QA booking with a stay 60 days ago (557) is also listed.
  - Rows come out in ID order, not by stay date.

**Possible fix:** `->where('time_from', '>=', today())->orderBy('time_from')`, and move older stays to a past-bookings view.

---

## B3. High: delete from the bookings list sends a GET, which returns 405

- **URL:** red delete button on `/current_bookings`, from `component.action_buttons` with `routePrefix => 'tour_package'`.
- **What happens:** `tour_package` maps to the `POST` method, so `submitDeleteRequest()` navigates with `window.location.href` (a **GET**) to `/tour_package/{id}/delete`. That route only accepts `POST|DELETE`, so `GET /tour_package/564/delete` returns **405**. It's the same shared-component bug as C2.

**Possible fix:** see C2.

---

## B4. Medium: the Tour column is blank for most bookings

- **URL:** `/current_bookings`.
- **What happens:** the tour name comes from `TourPackage::getTour()`, which reads only the **first tour day** (`packages_tour_days`) and excludes soft-deleted tours. It ignores the package's own `tour_id`.
  - **6 of the 8 real bookings** (200, 201, 202, 203, 213, 222) show a blank Tour.
  - QA 558 (no tour days) and 559 (tour deleted) are blank too.

**Possible fix:** fall back to `tour_id`, and show "Deleted tour #id" for trashed tours (`Tour::withTrashed()`).

---

## B5. Medium: "Payments Made" column shows the payment policy

- **URL:** `/current_bookings`.
- **What happens:** the column header is **"Payments Made"**, but it shows `payment_policy`, the deposit rule from the latest offer (e.g. "30% deposit due 14 days before arrival."). It says nothing about payments actually made.

**Possible fix:** rename the column to "Payment Policy", or show real payment data (`tour_packages.paid` / invoices).

---

## B6. Medium: policy text is badly formatted

- **URL:** `/current_bookings`.
- **What happens:** the text is built by plain string concatenation in `current_bookings()`:
  - real booking **213** shows **"10percentage deposit due 12 days before arrival."**: `deposit_type` holds the word `percentage` and is appended straight after the number;
  - zero values give **"0 days before arrival: 0 can be cancelled free of charge."** and **"0 deposit due 0 days before arrival."** instead of "N/A";
  - "7 days before arrival: 50% can be cancelled free of charge" doesn't say whether 50% is the fee or the refund.

**Possible fix:** map `percentage` to `%` and `fixed` to the currency, treat 0/empty as "N/A", and agree the wording with the business ("Free cancellation until 7 days before arrival; 50% fee after").

---

## B7. Medium: 103 queries for 15 rows, and no pagination

- **URL:** `/current_bookings`.
- **What happens:** for every package the loop calls `service()` (hotel lookup), `City::find`, `latestHotelOffer` (plus its two policies), `getTour()` (tour days, then tour) and `getStatusName()` (`Status::find`). That was **103 queries for 15 rows**, about 7 per row, and every row is rendered at once.

**Possible fix:** eager-load (`with(['latestHotelOffer.cancellation_policiy', 'latestHotelOffer.payment_policiy', 'tourDays'])`), pre-load hotels, cities and statuses in one query each, and paginate.

---

## B8. Low: empty cells are blank

- **URL:** `/current_bookings`.
- **What happens:** missing city, tour and so on are rendered as empty cells (`''`). CLAUDE.md asks for an ASCII fallback such as `{{ $value ?: '-' }}`.

---

## B9. Low: CSV export and sample row

- **URL:** `/current_bookings` (Export CSV button, `offerExportTable()`).
- **What happens:** same as C8. The export includes the Actions column's button text, and when the list is empty the page shows a fake "Sample Grand Hotel" (ID `S-201`) row, which also gets exported.

---

## B10. Low: deleting a hotel package right after creating it crashes the activity log

- **URL:** any package delete that runs on a freshly created model (found while seeding QA data).
- **What happens:** `app/Helper/Trackable.php:302`:

```php
if($model->attributes['tour_id']){
```

  This throws **"Undefined array key tour_id"** when the model was created in the same request without `tour_id`. Models loaded from the database have the key, so normal UI deletes are unlikely to hit it.

**Possible fix:** `if (!empty($model->tour_id)) {`.

---

# Part 9: `/past_offers`

The page is 403 for every user (O1), so all 6 pages were also rendered in-process with `OfferController@past_offers` as `qa.admin`, skipping only the route middleware. 4 extra QA tours (ids 144-147) were added: finished 70 days ago, cancelled in the past, and two soft-deleted past tours.

**Query used** (from the query log):

```sql
select * from `tours`
where (`status` = 46 or `retirement_date` < now() + 7 days) and `tours`.`deleted_at` is null
order by `id` desc limit 10 offset 0
```

## O1. High: no one can open `/past_offers`

- **URL:** `/past_offers` (route `past_offers.index`, `routes/web.php:251`).
- **What happens:** `OfferController` uses `permissions.required`, which needs `past_offers.index`. It doesn't exist.

| User | `GET /past_offers` |
|------|--------------------|
| `qa.admin` (admin) | 403 |
| `qa.agent` (user) | 403 |
| guest | 302 to `/login` |

**Possible fix:** same as C1 and B1.

---

## O2. High: running and future tours are listed as past offers

- **URL:** `/past_offers` (all pages, `?page=1` to `?page=6`).
- **What happens:** `past_offers()` uses `where('status', 46)->orWhere('retirement_date', '<', now()->addWeek())`. The list shows **58 rows**, but only **51** non-deleted tours have actually ended. The 7 extra:

| Tour | Why it's wrong |
|------|----------------|
| [QA] Current Prague Tour | still running (ends in 5 days) |
| [QA] Offer Running Ends In 3 Days | still running |
| [QA] Invalid Range Tour (dep > ret) | ends in 5 days |
| [QA] Offer Requested Future | status Requested, departs in **40 days** |
| [QA] Quotation Paris | status Requested, departs in **45 days** |
| QA Quotation Tour #1126 | status Requested, departs in **30 days** |

- **Cause:** "Requested" (46) tours are treated as past whatever their dates, and the one-week buffer moves running tours into "past" (the mirror image of C3).

**Possible fix:** `where('retirement_date', '<', today())`, and show Requested tours in their own list or on current offers.

---

## O3. High: delete from the past offers list sends a GET, which returns 405

- **URL:** red delete button on `/past_offers` (11 per page, `data-delete-method="POST"`, `data-delete-url=".../tour/{id}/delete"`).
- **What happens:** same shared-component bug as C2. `submitDeleteRequest()` navigates with a GET to `/tour/{id}/delete`, which only accepts `POST|DELETE`, so it returns **405**.

---

## O4. Medium: some tours appear in neither current nor past offers

- **URLs:** `/past_offers` and `/current_offers`.
- **What happens:** the two filters don't cover all tours:
  - a future tour with `status = NULL` ("[QA] Offer No City No Status", departs in 30 days) is excluded from current offers (C4) and doesn't match the past filter either, so it is **in neither list**;
  - a tour with NULL dates would also be in neither list (see T17, where `updateCalendar` nulled tour 133's dates).

**Possible fix:** build both lists from one shared, complementary condition (e.g. `retirement_date >= today()` vs `< today()`, NULLs handled explicitly).

---

## O5. Medium: rows are sorted by ID, not by date

- **URL:** `/past_offers`.
- **What happens:** `->orderBy('id', 'desc')`. Page 1 return dates come out as 2026-10-07, 2026-08-18, 2026-12-10, 2026-10-30, 2026-11-29, … in no date order. The most recently finished tours aren't first.

**Possible fix:** `->orderBy('retirement_date', 'desc')`.

---

## O6. Medium: search, sort and CSV export only cover the current page

- **URL:** `/past_offers` (search box "Search past offers...", sort headers, Export CSV button).
- **What happens:** `offerFilterTable()`, `offerSortTable()` and `offerExportTable()` work in the browser on the 10 rows on screen.
  - Searching for a tour that is on page 4 from page 1 finds nothing.
  - The CSV export contains only 10 rows.
  - Only the ID and Tour Name columns are sortable.

**Possible fix:** do search and sort on the server (query parameters kept by `->withQueryString()`), and export from the server.

---

## O7. Low: empty cells show `�`

- **URL:** `/past_offers`.
- **What happens:** `resources/views/offers/past_offers.blade.php` is saved as Windows-1252, and the fallbacks are byte `0x97` (shown as `�` on a UTF-8 page). Same as C7.

**Possible fix:** replace the fallbacks with `'-'` and save the file as UTF-8.

---

## O8. Low: CSV export and sample row

- **URL:** `/past_offers` (Export CSV button).
- **What happens:** same as C8. The Actions column's button text is exported, and when the list is empty a fake "Sample Rome Spring Offer" row (ID `S-101`, the same sample as current offers) is shown and exported.

---

## O9. Low: extra tour query per row

- **URL:** `/past_offers`.
- **What happens:** besides the paginated query, each page runs `select * from tours where id = ? limit 1` once per row (8-10 per page), from the row/action-button helpers. Small at 10 per page, but avoidable.

**Possible fix:** eager-load what the action buttons need, or pass the already-loaded model.

---

# Part 10: `/cancellation_policies`

The page is 403 for every user (CP2), so its content was also tested by rendering `OfferController@cancellation_policies` in-process as `qa.admin`, skipping only the route middleware. Data: the real policies plus QA offers 103-107 (`hotel_offers.ref = 'QA-POLICY'`) with policies 41-45, and orphan policy 46 (offer `99999999`).

**Rendered rows (excerpt):**

| Policy ID | Policy text | Hotel | Status | Stay | Tour | Delete button points to |
|-----------|-------------|-------|--------|------|------|-------------------------|
| 29 | 30 days before arrival: 45percentage can be cancelled free of charge. | Cervantes Hotel | Offered with Option | 2023-11-10 | ROOM TESTIG TOUR #1108 | `/tour/20/delete` |
| 32 | 100 days before arrival: 45percentage … | Prieure Hotel | Offered with Option | 2024-06-24 | Cola Test #0620 | `/tour/65/delete` |
| 33 | 80 days before arrival: 30percentage … | Prieure Hotel | Offered with Option | 2024-06-24 | Cola Test #0620 | `/tour/65/delete` |
| 38 | 99 days before arrival: 10% … | [QA] Booking Future Hotel | Offered with Option | 2026-11-08 | No tour linked | — |
| 39 | 7 days before arrival: 50% … | [QA] Booking Future Hotel | Offered with Option | 2026-11-08 | No tour linked | — |
| 41 | 14 days before arrival: 25percentage … | [QA] Booking Future Hotel | Offered with Option | 2026-11-08 | QA Edited Without Permission #1106 | `/tour/133/delete` |
| 42 | 3 days before arrival: 150fixed … | [QA] Booking Without Tour | Offered No rooms blocked | 2026-11-16 | No tour linked | — |
| 43 | 10 days before arrival: 20% … | *(blank: package soft-deleted)* | Offered with Option | *(blank)* | QA Edited Without Permission #1106 | `/tour/133/delete` |
| 46 | 33 days before arrival: 33% … | *(blank: offer missing)* | *(blank)* | *(blank)* | No tour linked | — |

## CP1. Critical: delete (and edit/show) on a policy row targets the whole tour

- **URL:** action buttons on each row of `/cancellation_policies`. The view includes `component.action_buttons` with `'item' => $offer->tour, 'routePrefix' => 'tour'`.
- **What happens:** the buttons are built for the **tour**, not for the policy:
  - Delete points to `/tour/{tour_id}/delete`. Real policies 32 and 33 both point to `/tour/65/delete`; QA policies 41, 43 and 45 to `/tour/133/delete`.
  - Show and Edit open `/tour/{id}` and `/tour/{id}/edit`.
- **Impact:** a user who means to remove one cancellation policy would delete the **entire tour**, including (per T7) its client invoices and quotations. It fails today only because the button sends a GET that returns 405 (C2). **Fixing C2 without fixing this turns it into real data loss.**

**Possible fix:** give the rows policy (or offer) actions. Link to the offer page (`/offers/hotel_offers/{package_id}` or similar), and add a dedicated, permission-checked delete for the policy if one is needed. Never reuse the tour buttons here.

---

## CP2. High: no one can open `/cancellation_policies`

- **URL:** `/cancellation_policies` (route `cancellation_policies.index`, `routes/web.php:253`).
- **What happens:** `cancellation_policies.index` doesn't exist (no `*cancel*` permissions at all).

| User | `GET /cancellation_policies` |
|------|------------------------------|
| `qa.admin` (admin) | 403 |
| `qa.agent` (user) | 403 |

**Possible fix:** same as C1, B1 and O1.

---

## CP3. High: policies from outdated offers are listed next to current ones

- **URL:** `/cancellation_policies`.
- **What happens:** `OfferCancellationPolicies::with('hotelOffer')->get()` lists **every** policy of **every** offer. When a hotel sends a new offer, the old policy stays on the list.
  - "[QA] Booking Future Hotel" appears **4 times**, with 99 days/10%, 7 days/50%, 14 days/25% and 2 days/5%.
  - Real "Prieure Hotel" appears twice: 100 days/45% and 80 days/30%.
  - Real package 93 has 5 offers.
- Users can't tell which policy is in force. (`/current_bookings` already uses only `latestHotelOffer`.)

**Possible fix:** show only the policy of each package's latest offer, or group by package with older offers collapsed.

---

## CP4. High: policies of deleted bookings and orphan policies show as blank rows

- **URL:** `/cancellation_policies`.
- **What happens:** there is no check that the offer or package still exists:
  - policy 43 (package soft-deleted) shows with **no hotel, city or stay date**, but still with a tour delete button;
  - policy 46 (its offer, id 99999999, doesn't exist) is a near-empty row with only "33 days before arrival: 33% …".

**Possible fix:** `whereHas('hotelOffer', fn($q) => $q->whereHas('package'))` (with soft deletes respected), and clean up orphan policies.

---

## CP5. Medium: policy text is badly formatted

- **URL:** `/cancellation_policies`.
- **What happens:** the text is `days.' days before arrival: '.percentage.type.' can be cancelled free of charge.'`:
  - **all 5 real policies** have type `percentage` and read "45**percentage**", "20**percentage**", …;
  - a fixed amount reads "150**fixed**", with no currency;
  - zeros read "0 days before arrival: 0 can be cancelled free of charge.";
  - the sentence doesn't say whether the percentage is the fee or the refund.

**Possible fix:** share one formatter with B6 (map `percentage`/`%` to `%`, `fixed` to the offer currency, 0 to "N/A"), and agree the wording with the business.

---

## CP6. Medium: SIN / DOU / TRI columns are always empty

- **URL:** `/cancellation_policies`.
- **What happens:** the controller sets `'SIN' => '', 'DOU' => '', 'TRI' => ''` with the comment "Room type data - would need more processing". The three room-type columns are empty on every row. `$room_types` is loaded but not used for them.

**Possible fix:** fill them from the offer's room prices/quantities, or remove the columns.

---

## CP7. Medium: Tour column blank when the offer has no tour or the tour is deleted

- **URL:** `/cancellation_policies`.
- **What happens:** the tour comes only from `hotel_offers.tour_id` via `Tour::find()`.
  - Offers with `tour_id = 0` show "No tour linked" even when the package belongs to a tour (QA 38, 39, 40, 42).
  - Policy 44 (tour 135, soft-deleted) shows blank.

**Possible fix:** fall back to the package's `tour_id` or tour days, and show deleted tours with `withTrashed()` and a "(deleted)" label.

---

## CP8. Medium: wasted work, no pagination, no date filter

- **URL:** `/cancellation_policies`.
- **What happens:**
  - the controller runs a `$tours` query (upcoming tours, 10 rows) that the view **never uses**;
  - each row then runs `TourPackage::find`, `Tour::find`, `service()` and `City::find`: **67 queries for 14 rows**;
  - there is no pagination and no date filter, so policies for stays in 2023-2024 are listed with current ones.

**Possible fix:** remove the unused `$tours` query, eager-load (`with('hotelOffer.package', 'hotelOffer.tour')`), filter to upcoming stays, and paginate.

---

## CP9. Low: Option Date blank for most rows

- **URL:** `/cancellation_policies`.
- **What happens:** `hotel_offers.option_date` is empty for most offers, so the column is mostly blank, with no `-` fallback.

---

## CP10. Low: CSV export and sample row

- **URL:** `/cancellation_policies` (Export CSV button).
- **What happens:** same as C8. Button text from the Actions column is exported, and a fake sample row (ID `S-301`) is shown and exported when the list is empty.

---

## CP11. Low: view file isn't UTF-8

- **URL:** `/cancellation_policies` (`resources/views/offers/cancellation_policies.blade.php`).
- **What happens:** the file is saved in a legacy encoding ("Non-ISO extended-ASCII"), like `current_offers` and `past_offers` (C7, O7). No `�` fallback appeared in this render, but the file should be converted to UTF-8 so it doesn't produce broken characters.

---

# Part 11: `/task`

Tested as `qa.admin` (admin), `qa.agent` (role `user`, which has all `task.*`), `qa.created` (no task permissions at all) and a guest. QA tasks 272-280 ("[QA] T …") were created; only those were edited or deleted.

## K1. Critical: users with no task permissions can edit any task

- **URLs:** inline editing on `/task` sends **`POST /task/{id}/update-field`**; calendar drag on `/home` sends **`POST /task/{id}/updateCalendar`**.
- **What happens:** `TaskController` uses `permissions.required`, which takes the permission from the route name. These two routes have **no name**, so nothing is checked.

**Result as `qa.created` (no `task.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /task`, `/task/272`, `/task/272/edit` | 403 (correct) |
| `POST /task/272/update-field` (`field=content`) | 200, **task renamed** to "[QA] T Edited by user without task perms" |
| `POST /task/272/updateCalendar` | 200 `true`, **deadline moved** to 2026-11-05 08:00 |

**Possible fix:** name these routes, or give them `'permissions' => 'task.edit'`.

---

## K2. Critical: stored XSS on the task page

- **URL:** `/task/{id}` (`resources/views/task/show.blade.php`).
- **What happens:** the show view prints with `{!! !!}`:

```blade
{!!$task->content!!}
{!!$task->tourModel ? $task->tourModel->name : 'Without Tour'!!}
{!!$task->assign!!}
{!!$task->dead_line!!}
```

- **Steps to reproduce:** create a task with title `[QA] T <img src=x onerror=alert('xss-task-show')>` (task 278) and open `/task/278`. The page contains `<td>[QA] T <img src=x onerror=alert('xss-task-show')></td>`, which runs for every viewer.
- The task list (`/task`) and the edit form escape the same title correctly. (The task header dropdown has its own XSS, H4.)

**Possible fix:** use `{{ }}` for all four values.

---

## K3. Critical: users never see tasks assigned to them on their dashboard

- **URLs:** `/task/create` (`POST /task`) and `/home` (task widget).
- **What happens:**
  - `TaskController@store` sets `$task->assign = Auth::user()->id`, the **creator**. The chosen people go into `task_user` (`assigned_users`).
  - The `/home` task widget (`AppController@dashboard`) lists tasks with `->where('assign', $user->id)`, so it shows tasks a user **created**, not tasks **assigned** to them.
- **Evidence:**
  - the admin created "[QA] T Valid task" assigned to `qa.agent`, and `qa.agent`'s `/home` To-Do doesn't show it;
  - `qa.agent` has **9** tasks in `task_user`, but only **1** with `assign = qa.agent`.

**Possible fix:** on the dashboard, use `whereHas('assigned_users', fn($q) => $q->where('users.id', $user->id))` (optionally `orWhere('assign', $user->id)`).

---

## K4. High: personal tasks are visible to and editable by every user

- **URLs:** `/task`, `/task/{id}`, `/task/{id}/edit`, `POST /task/{id}/update-field`.
- **What happens:** `TaskController@index` lists **all** tasks of all users, including `task_type = 1` (Personal). Show, edit and update don't check ownership.
- **Evidence:** the admin's Personal task 279 ("[QA] T Admin personal task") was listed for `qa.agent`. The agent could open `/task/279` and `/task/279/edit` (200) and **renamed it** through `update-field`.

**Possible fix:** limit Personal tasks to their creator and assignees (in the list and on show/edit/update), and scope the list for non-admins.

---

## K5. High: a bad assignee leaves a half-created task

- **URL:** `/task/create`, which sends `POST /task`.
- **What happens:** `store()` saves the task first, then loops the assignees with `User::findOrFail($user_id)`. With `assigned_user[]=999999` the user gets a **404 page**, but task **277** ("[QA] T Bad assignee") was already saved, with no assignees and no success message.

**Possible fix:** validate `assigned_user.* => exists:users,id` before saving, and wrap create in a transaction.

---

## K6. High: inline edit and AJAX status change save invalid values

- **URLs:** inline editing on `/task` sends `POST /task/{id}/update-field`; status drag/drop sends AJAX `POST /task/{id}/update` with `newStatus`.
- **What happens:** neither path validates the value:

| Request | Response | Saved |
|---------|----------|-------|
| `update-field` `status=999999` | 200 (`"name":"Unknown"`) | `999999` |
| `update-field` `priority=abc` | 200 | `0` |
| `update-field` `story_points=-5` | 200 | `-5` |
| `update-field` `content=` (empty) | 200 | `""` (empty title) |
| `update` AJAX `newStatus=999999` | `done` | `999999` |
| `update-field` `field=assign` | 400 "Invalid field" (correct) | — |

- **Side effect:** the list's three groups use `whereHas('status', …)`, so a task with a non-existent status **disappears from To-Do, Completed and Aborted** (task 272 no longer appeared on `/task`).

**Possible fix:** per-field rules in `updateField` (`content` required/max, `status` `exists:status,id` with `type=task`, `priority` boolean, `story_points`/`estimated_sp` integer `min:0`), and the same `status` rule for `newStatus`.

---

## K7. High: calendar move can wipe the deadline

- **URL:** dragging a task on the `/home` calendar sends `POST /task/{id}/updateCalendar`.
- **What happens:** `updateCalendarTask()` writes `date . ' ' . date_time` straight into `start_time` and `dead_line`, with no validation:

| Request | Result |
|---------|--------|
| no `date` / `date_time` | 200 `true`, deadline **`0000-00-00 00:00:00`** |
| `date=not-a-date`, `date_time=xx` | 200 `true`, deadline `0000-00-00 00:00:00` |
| `POST /task/999999/updateCalendar` | **500** (`Task::find($id)->update` on null) |

**Possible fix:** validate `date` (`required|date`) and `date_time` (`required|date_format:H:i`), use `findOrFail`, and protect the route (K1).

---

## K8. Medium: create saves invalid date, status, task type and tour

- **URL:** `/task/create`, which sends `POST /task`.
- **What happens:** `validateTask()` only checks that fields are present (`required`):

| Input | Saved |
|-------|-------|
| `end_date=not-a-date`, `end_time=99:99` | task 273, `dead_line = 0000-00-00 00:00:00` |
| `status=999999` | task 274, status `999999` (invisible in all lists, see K6) |
| `task_type=99` | task 275, task_type `99` (crashes its page, see K9) |
| `tour=99999999` | task 276, tour `99999999` |

**Possible fix:** `end_date => date`, `end_time => date_format:H:i`, `status => exists:status,id`, `task_type => in:1,2`, `tour => nullable|exists:tours,id`.

---

## K9. Medium: task page crashes for an unknown task type

- **URL:** `/task/{id}`, e.g. `/task/275`.
- **What happens:** `show.blade.php` prints `\App\Task::$taskTypes[$task->task_type]`. For `task_type = 99` the key doesn't exist, so the page returns **500**.

**Possible fix:** `{{ \App\Task::$taskTypes[$task->task_type] ?? 'Unknown' }}`, and validate `task_type` (K8).

---

## K10. Medium: `notification_click` with a missing ID gives 500

- **URL:** `/task/{id}?notification_click={notificationId}` (links from the notification dropdown).
- **What happens:** `show()` does `Notification::find(...)` and then `$notification->click = true` without a null check. `/task/272?notification_click=999999` returns **500**. A valid ID marks **any** user's notification as read (same as T16).

**Possible fix:** look it up through `Auth::user()->notifications()` and skip when it isn't found.

---

## K11. Medium: task list sorted by creation date, not deadline

- **URL:** `/task`.
- **What happens:** all three groups use `->orderBy('created_at', 'desc')`, so overdue and soon-due tasks aren't on top of To-Do. (The `/home` widget sorts by `dead_line`.)

**Possible fix:** order To-Do by `dead_line` ascending (NULLs last).

---

## K12. Low: some task routes need permissions that don't exist

- **URLs:** `POST|DELETE /task/{id}/delete/{tab}` (route `task_tab.destroy`) and `GET /task/{id}/deleteMsg` (route `task.deleteMsg`).
- **What happens:** neither `task_tab.destroy` nor `task.deleteMsg` exists as a permission, so both return 403 for everyone. Remove the routes if they are unused, or map them to `task.destroy`.

---

## K13. Low: helper routes have no permission check

- **URLs:** `GET /task/statuses/list?taskId={id}` and `GET /task/getTasksBlock`.
- **What happens:** both are unnamed, so any logged-in user passes. `qa.created` (no task permissions) got 200 from `/task/statuses/list?taskId=272`.

---

## K14. Low: empty deadlines show as "now"

- **URLs:** `/task`, `/task/{id}`.
- **What happens:** same cause as H6. `Task::getDeadLineAttribute` turns NULL into the current time, so tasks without a deadline look due now and overdue.

---

## K15. Low: task pages are slowish

- **URLs and timings:** `/task` 0.79 s (272 KB), `/task/create` 0.89 s. `create()` loads every tour and every user for the dropdowns.

---

# Part 12: `/supplier_search`

Tested as `qa.admin`, `qa.agent` (role `user`), `qa.created` (no supplier permissions) and a guest. 5 QA suppliers were added: hotels 7136-7138, restaurant 3699, event 2842, all "[QA] Supplier …", with encrypted test portal passwords on the hotels. Because the results endpoint is 403 for everyone (SS4), the search logic was also tested by calling `SupplierSearchController@show` in-process as `qa.admin`.

**Route protection:**

| Route | Used by | Protection |
|-------|---------|------------|
| `GET /supplier_search` | the page | `supplier_search` (exists; admin, user, Super User, Accountant) |
| `GET /supplier_show` | search results (DataTables AJAX) | `supplier_show`, **doesn't exist**, so 403 for all |
| `GET /supplier_criteria` | criteria lookup | `criterias_for_search`, **doesn't exist**, so 403 for all |
| `GET /table_service_list` | service list in tour packages | **unnamed: no permission check** |
| `GET /booking/offers/{id}/{supplier}` | booking/offer pages | **no `auth` at all** |
| `POST /TMS-Supplier/add_comment/{id}` | supplier portal | **no `auth` at all** |

## SS1. Critical: any logged-in user can download every supplier, including portal passwords

- **URL:** `GET /table_service_list?service_type_id={type}&service_id={id}` (`SupplierSearchController@generateTableServiceList`, `routes/web.php:537`).
- **What happens:** the route has no name, so `permissions.required` checks nothing. It returns every supplier of the chosen type, serialized with `toArray()` (all columns). The `Hotel` model hides nothing (`$hidden = []`).
- **Evidence as `qa.created` (no supplier permissions):**
  - `service_type_id=0` returned **recordsTotal 6891** (all hotels), with columns including `work_email`, `contact_email`, `work_phone`, `contact_phone` and **`password`**;
  - sorted by id desc, the QA hotels came back with `password: "eyJpdiI6…"`, the encrypted supplier portal password. The 3 real hotels with portal passwords are exposed the same way.

**Possible fix:** name the route and require `tour_package.create`/`edit` (or `supplier_search`); return only the columns the table shows; add `protected $hidden = ['password']` to `Hotel` (and any other supplier model with credentials).

---

## SS2. Critical: supplier endpoints that work with no login

- **URLs:**
  - `GET /booking/offers/{package_id}/{supplier}` (`BookingRequestController@offersData`)
  - `POST /TMS-Supplier/add_comment/{id}` (`TMSSupplier\OfferController@addComment`, `routes/web.php:816`)
- **What happens:** neither route has `auth` (or `supplierauth`).

| Request (guest, no login) | Result |
|---------------------------|--------|
| `GET /booking/offers/93/1` (real package) | 200 JSON: 5 offers with currency, city tax, half board, FOC, porterage, statuses |
| `GET /booking/offers/556/1` (QA package) | 200 JSON: 4 offers; the XSS status is returned raw |
| `POST /TMS-Supplier/add_comment/103` with `comment=qa` | 200: **a row was inserted** into `supplier_comments` for real package 103 |

- **`addComment` also:**
  - returns `"<li><strong>{supplier_name}</strong> said: <p>{content}</p></li>"` built by string concatenation, with no escaping (XSS);
  - returns the **latest row in the whole table** (`orderBy('id','desc')->first()`), which can be another supplier's comment;
  - crashes if the package doesn't exist (`$package->name` on null).
- The test comment (`supplier_comments` id 10, content "qa", on real package 103 "Chez Edouard Restaurant") was **deleted immediately** after the test.

**Possible fix:** put `/booking/offers/*` behind `auth` (+ a permission), and `TMS-Supplier/add_comment` behind `supplierauth`, checking that the package belongs to the logged-in supplier. Escape the output and return the inserted row (`insertGetId`).

---

## SS3. Critical: stored XSS in the supplier search results

- **URL:** `/supplier_search` results table (DataTables, data from `GET /supplier_show`).
- **What happens:** the DataTables columns in `resources/views/supplier_search/index.blade.php` (`nameService`, `address_first`, `country`, `city`, `work_phone`, `contact_name`) have no `render` escaping, so DataTables inserts them as HTML. The server returns them raw. For QA hotel 7137:
  - `nameService`: `[QA] Supplier Hotel <img src=x onerror=alert("xss-supplier")> (Hotel)`
  - `contact_name`: `<script>alert("xss-contact")</script>`
- The `<img onerror>` would run for every user who searches. Supplier data also comes from imports and the supplier portal.

**Possible fix:** add `render: $.fn.dataTable.render.text()` to every data column (or escape on the server).

---

## SS4. High: search never returns results

- **URLs:** `/supplier_search` loads (200 for admin and user), but its AJAX calls fail:

| Request | admin | user |
|---------|-------|------|
| `GET /supplier_show?service=Hotel&searchname=…` | 403 | 403 |
| `GET /supplier_criteria` | 403 | — |

- **Cause:** the route names `supplier_show` and `criterias_for_search` are used as permissions, and neither exists.

**Possible fix:** give both routes `'permissions' => 'supplier_search'`.

---

## SS5. High: search results return full supplier records

- **URL:** `GET /supplier_show` (results for `/supplier_search`).
- **What happens:** `dataTableResponse()` serializes each model with `$model->toArray()`. The JSON for QA hotel 7137 contained **`password` (YES)** plus all contact emails and phones. This is hidden today only by SS4; once that's fixed, every user with `supplier_search` (admin, user, Super User, Accountant) receives it.

**Possible fix:** return only the displayed columns (`only(['id','name','address_first','country','city','work_phone','contact_name'])`) and hide `password` on the models.

---

## SS6. High: supplier portal passwords are reversible

- **URL:** TMS supplier login, `POST /TMS-Supplier/login` (`TMSSupplier\LoginController@Supplierauth`); data in `hotels.password`.
- **What happens:** passwords are stored with `Crypt::encryptString`, and login decrypts them with `Crypt::decryptString($hotel->password)` and compares. All 3 real stored passwords are encrypted, not hashed (none look like bcrypt), so anyone with `APP_KEY` plus the leaked values (SS1, SS5) gets the plain-text passwords.

**Possible fix:** store `Hash::make()` and verify with `Hash::check()`, migrating existing passwords at next login or by reset.

---

## SS7. High: `restaurants.id` has no AUTO_INCREMENT

- **URL:** any restaurant create (e.g. `/restaurant/create`); found while seeding QA data.
- **What happens:** `restaurants.id` is `int unsigned, PRIMARY` with **no `auto_increment`** (the only table in the database like this). A new restaurant inserted without an id gets **id 0**, and the next one fails with a duplicate primary key.
- The QA restaurant got id 0 and was moved to 3699 so it doesn't block real use.

**Possible fix:** `ALTER TABLE restaurants MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT;` in a migration.

---

## SS8. Medium: "All services" loads every supplier per request

- **URL:** `GET /supplier_show?service=All` (search with "All" selected).
- **What happens:** `show()` loads every matching supplier of every type into PHP, de-duplicates and sorts in memory, and only then cuts the page. It also sets `ini_set('memory_limit', '800M')` and `set_time_limit(0)`.

| Search | Rows loaded | Time | Peak memory |
|--------|-------------|------|-------------|
| All, empty name | 10,742 | 1.74 s | ~98 MB |
| Hotel, name `_` | 6,891 | 1.26 s | ~82 MB |

  Every DataTables page change or sort repeats the full load.

**Possible fix:** paginate and sort in SQL (or a UNION query), and remove the memory/time overrides.

---

## SS9. Medium: `_` and `%` aren't escaped in name search

- **URL:** `GET /supplier_show?service=Hotel&searchname=…`.
- **What happens:** the search uses `like '%' . $searchName . '%'` without escaping `%`/`_`. `searchname=_` matches **all 6,891 hotels**, and `searchname=100%` also matches "3100 Kulmhotel Gornergrat".

**Possible fix:** escape with `addcslashes($searchName, '%_\\')`.

---

## SS10. Medium: unknown `service_type_id` gives 500

- **URL:** `GET /table_service_list?service_type_id=99&service_id=0`.
- **What happens:** `$this->tour_package->servicesTypes[$request->service_type_id]` isn't checked, so the request returns **500**.

**Possible fix:** validate `service_type_id` against the keys of `servicesTypes`.

---

# Part 13: `/event`

Tested as `qa.admin` (admin, all `event.*`), `qa.created` (no event permissions) and a guest. QA events "QA E …" (ids 2843-2845 and 987600-987603) were created; only those were edited or deleted.

## E1. Critical: users with no event permissions can edit any event

- **URL:** `POST /event/{id}/update` (unnamed route; the edit form at `/event/{id}/edit` uses the named `PUT /event/{event}`).
- **What happens:** `EventController` uses `permissions.required`, which takes the permission from the route name. This route has none, so every logged-in user passes.

**Result as `qa.created` (no `event.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /event`, `/event/2843`, `/event/2843/edit` | 403 (correct) |
| `POST /event/2843/update` | 200, **event renamed** to "QA E Edited Without Permission" |
| `GET /event/2843/deleteMsg` (AJAX) | 200 (unnamed too) |

**Possible fix:** name the route or add `'permissions' => 'event.edit'` (and `event.destroy` for `deleteMsg`), or remove the duplicate route.

---

## E2. High: mass assignment on create and update

- **URLs:** `/event/create` (`POST /event`), `/event/{id}/edit` (`PUT /event/{id}`).
- **What happens:** `App\Event` has `protected $guarded = [];` and the controller saves `$request->except(['attach', 'place_id', 'criterias'])`, so any column can be set.
- **Evidence:** `POST /event` with `id=987600` and `created_at=2001-01-01 00:00:00` saved event **987600** with that `created_at`. The table's AUTO_INCREMENT then continued at 987601+.

**Possible fix:** save `$request->validated()` (or `only([...])`) and set `$fillable` on `Event`.

---

## E3. High: event forms never show validation errors

- **URLs:** `/event/create` and `/event/{id}/edit`.
- **What happens:** `resources/views/event/create.blade.php` and `edit.blade.php` have **no `$errors` output**. Every rejected create redirected back to `/event/create` with no message:
  - empty name;
  - `work_email=not-an-email`, `work_phone=abc`, `website=notaurl`;
  - duplicate `work_email`;
  - `rate=999999`;
  - names with `'` or accents (E8).
- The user can't tell why the event wasn't saved.

**Possible fix:** add the standard error block and `old()` values to both forms.

---

## E4. High: attachments accept any file type

- **URLs:** `/event/create` and `/event/{id}/edit` (attachment field `attach[]`).
- **What happens:** `StoreEventRequest`/`UpdateEventRequest` have no rule for `attach`, and `FileTrait::addFile` stores any upload. An SVG (`evil.svg`, containing `<script>`) was stored as `storage/app/public/uploads/2OY7VFTS…svg`, `image/svg+xml` (event 987602). Wherever `/storage` is linked (production), it is served from the app's own domain, so the script would run when the file is opened.

**Possible fix:** `'attach.*' => 'file|max:10240|mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx'` (as on tours).

---

## E5. High: event page crashes for a missing ID

- **URL:** `/event/{id}`, e.g. `/event/999999`.
- **What happens:** `show()` calls `$event->getCriterias()` **before** `if($event == null) return abort(404);`, so a missing event gives **500**. (`/event/999999/edit` correctly returns 404.)

**Possible fix:** move the null check up, or use `firstOrFail()`.

---

## E6. Medium: criteria IDs not validated

- **URLs:** `/event/create`, `/event/{id}/edit` (field `criterias`, comma-separated).
- **What happens:** the rule is only `nullable|string`. `criterias=999999,abc` saved `services_has_criterias` rows with `criteria_id` **999999** and **0** for event 2844.

**Possible fix:** split, cast and validate each id with `exists:criterias,id` (and the event criteria type).

---

## E7. Medium: city lookup by name ignores the country

- **URLs:** `/event/create`, `/event/{id}/edit` (city field via `component.city_form`).
- **What happens:** `CitiesHelper::changeCityNameToID()` does `City::where('name', $cityName)->first()`. With `country=AT`, `city=Atlantis`, the event got city **26405 "Atlantis", country ZA** (South Africa). Common names (e.g. "Paris", "Springfield") can resolve to the wrong country.

**Possible fix:** match on name **and** country, and prefer the city id from the autocomplete.

---

## E8. Medium: valid names rejected

- **URLs:** `/event/create`, `/event/{id}/edit`.
- **What happens:** `ValidationService::businessNameRules` uses `/^[a-zA-Z0-9\s\-\.\&\(\)]+$/` and `nameRules` uses `/^[a-zA-Z\s\-\.\']+$/`. Both rejected:
  - event name `QA E Musée d'Orsay Tour` (apostrophe + `é`);
  - contact name `José Núñez` (accents).
- Real venue and contact names are often like this, and E3 hides the reason.

**Possible fix:** allow Unicode letters and `'` (`/^[\pL\pN\s\-\.\&\(\)'’,\/]+$/u`).

---

## E9. Medium: delete button in the list opens the event instead of deleting it

- **URL:** red delete button on `/event` (`component.action_buttons`, `routePrefix => 'event'`).
- **What happens:** `event` isn't in the component's delete map, so the delete route falls back to `route('event.destroy')` = `/event/{id}`, with the default method `POST`. `submitDeleteRequest()` then **navigates with GET** to `/event/{id}`, which is the **show** page (200), so nothing is deleted. Sending a real `DELETE /event/{id}` works (soft delete, see passed).

**Possible fix:** same as C2. Submit POST/DELETE with the CSRF token instead of navigating.

---

## E10. Medium: event pages are slow

- **URLs and timings:** `/event` 1.21 s (176 KB, 15 per page), `/event/create` 1.54 s. `create()` loads all event criteria and rates; the index joins countries and cities on every request.

---

## E11. Low: update wipes city and criteria when the field isn't sent

- **URLs:** `PUT /event/{id}`, `POST /event/{id}/update`.
- **What happens:** `setCityGeneral()` turns a missing `city` into `NULL`, and `update()` always deletes the event's criteria before re-adding the posted ones. An update without those fields (API or other callers) cleared event 2843's city and criteria. The UI form normally sends both.

**Possible fix:** only touch `city` and criteria when they are present in the request.

---

## E12. Low: attachments can't be opened locally

- **URL:** attachment links on `/event/{id}`, e.g. `GET /storage/uploads/2OY7VFTS…svg` → **404**.
- **What happens:** in this checkout `public/storage` is a **32-byte plain file**, not a symlink (a git symlink checked out on Windows), so nothing under `/storage` is served locally.

**Possible fix:** run `php artisan storage:link` locally (production with a real symlink is unaffected).

---

# Part 14: `/restaurant`

Tested as `qa.admin` (admin), `qa.created` (no restaurant permissions) and a guest. QA restaurants 3700-3706 ("QA R …") and tour package 565 "[QA] Booking Restaurant Ref" (type 4, pointing at restaurant 3701) were created. Only QA rows were changed.

## RS1. Critical: the "in use" delete check deletes the record it should protect (app-wide)

- **URLs:** delete on `/restaurant` (`DELETE /restaurant/{id}`, `POST|DELETE /restaurant/{id}/delete`). The same check runs for **`/event`** and for guides, transfers, flights, cruises, drivers, criteria and rates.
- **What happens:** controllers call `$deleteModel->check($model, 'Restaurant')`, which should only *report* that a record is still used by tours. But the end of `app/Library/Services/DeleteModel.php::check()` is:

```php
if ($ret !== NULL) {   // $ret is set only when the record IS in use
    $model->delete();
}
return $ret;
```

  So when the record **is** in use, `check()` soft-deletes it and returns the message. The controller then shows "Delete … It used in N tours!" as if nothing happened. When it is **not** in use, `check()` returns NULL and the controller deletes it normally. **Records still used by tours are always deleted**, and without the controller's cleanup of criteria, comments, files or Google Places.
- **Evidence:**
  - restaurant 3701 is referenced by tour package 565 (type 4);
  - `DELETE /restaurant/3701` returned 200 and the restaurant got `deleted_at`;
  - calling the controller in-process gave flash message "Delete QA R Second Restaurant! It used in 1 tours!" **and** `deleted_at` set;
  - the query log showed a single `update restaurants set deleted_at … where id = 3701` coming from `check()`;
  - reproduced 3 times; 3701 was restored afterwards.
- **Impact:** any supplier used in real tours can be deleted through the normal delete button (once RS11/C2 is fixed, or via `DELETE`). Tour packages are left pointing at deleted suppliers.

**Possible fix:** remove the `$model->delete()` block from `check()`. `check()` should only return the message (`Bus` already returns early).

---

## RS2. Critical: new restaurants' criteria and attachments are saved against restaurant 0

- **URL:** `/restaurant/create`, which sends `POST /restaurant`.
- **What happens:**
  - `restaurants.id` has **no AUTO_INCREMENT** (SS7). `store()` sets `$data['id'] = latest + 1` and calls `Restaurant::create($data)`.
  - Because the model is `incrementing`, Eloquent then overwrites `$restaurant->id` with `lastInsertId()`, which is **0** for a column without auto-increment.
  - The criteria loop (`service_id = $restaurant->id`) and `addFile()` (`restaurant_id = $restaurant->id`) therefore save against **restaurant 0**.
- **Evidence:**
  - restaurant 3700 created with `criterias=9,17` and a PNG: the restaurant has **no** criteria and **no** files;
  - `services_has_criterias` has rows with `service_id = 0`, `service_type = 4` (criteria 9, 17, 999999, 0 from this run), and `files` has 2 rows with `restaurant_id = 0`;
  - **3 of the `service_id = 0` restaurant criteria rows were created before today** (ids 30, 32, 35), so real restaurants have already lost criteria this way.

**Possible fix:** add AUTO_INCREMENT to `restaurants.id` (SS7) and stop setting the id manually. Then decide where the 3 real orphaned rows belong.

---

## RS3. Critical: restaurant creation breaks after the newest restaurant is soft-deleted

- **URL:** `/restaurant/create`, which sends `POST /restaurant`.
- **What happens:** `store()` computes the new id as `Restaurant::latest('id')->first()->id + 1`. `Restaurant` uses `SoftDeletes`, so `latest()` **ignores soft-deleted rows**.
  - After soft-deleting the highest restaurant (3705), the next create computed id 3705 again and failed with `SQLSTATE[23000]: 1062 Duplicate entry '3705' for key 'PRIMARY'`. The user is sent back to the form with no message (RS6), and every later attempt fails the same way.
  - Two users creating restaurants at the same moment also compute the same id.

**Possible fix:** same as RS2 (database AUTO_INCREMENT). At minimum use `withTrashed()->max('id') + 1` inside a locked transaction.

---

## RS4. Critical: users with no restaurant permissions can edit any restaurant

- **URL:** `POST /restaurant/{id}/update` (unnamed route, `routes/web.php:73`; the edit form uses the named `PUT /restaurant/{restaurant}`).

**Result as `qa.created` (no `restaurant.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /restaurant`, `/restaurant/3701/edit` | 403 (correct) |
| `POST /restaurant/3703/update` | 302, **restaurant renamed** to "QA R Edited Without Permission" |

**Possible fix:** name the route or add `'permissions' => 'restaurant.edit'`, or remove it (the form doesn't use it). `GET /restaurant/{id}/deleteMsg` is unnamed too.

---

## RS5. High: stored XSS on the restaurant page

- **URL:** `/restaurant/{id}` (`resources/views/restaurant/show.blade.php`).
- **What happens:** the show view prints raw HTML:

```blade
{!!$restaurant->address_first!!}   {{-- line 47 --}}
{!!$restaurant->comments!!}        {{-- line 132 --}}
{!!$restaurant->int_comments!!}    {{-- line 138 --}}
```

  Restaurant 3704, with `address_first = <img src=x onerror=alert('xss-rest-addr')>` and `comments = <script>alert('xss-rest-comment')</script>`, rendered **2 raw** payloads on `/restaurant/3704`. The list and the edit form escape them.

**Possible fix:** use `{{ }}` (or `nl2br(e(...))` for multi-line comments).

---

## RS6. High: restaurant forms never show validation errors

- **URLs:** `/restaurant/create` and `/restaurant/{id}/edit`.
- **What happens:** neither view outputs `$errors`. Each of these redirected back with no message:
  - empty name;
  - duplicate name;
  - `work_email=nope`, `work_phone=abc`, `website=nope`;
  - "QA R Café de l'Opéra" (RS10);
  - re-using a deleted restaurant's name (RS9);
  - the duplicate-key failure (RS3).

**Possible fix:** add the standard error block and `old()` values (same as E3).

---

## RS7. High: mass assignment on create and update

- **URLs:** `/restaurant/create` (`POST /restaurant`), `/restaurant/{id}/edit` (`PUT /restaurant/{id}`).
- **What happens:** `App\Restaurant` has `$guarded = []`, and the controller saves `$request->except('attach', 'place_id', 'criterias')`. `created_at=2001-01-01 00:00:00` was saved on restaurant 3703. (`id` is overwritten by `store()`, but any other column can be set; on update `id` is not overwritten.)

**Possible fix:** save validated fields only and set `$fillable`.

---

## RS8. High: attachments accept any file type

- **URL:** `/restaurant/create` (`attach[]`), which sends `POST /restaurant`.
- **What happens:** no rule on `attach`. "QA R SVG Attach" was created with an SVG attachment containing `<script>`. Same as E4.

---

## RS9. Medium: a deleted restaurant's name can never be reused

- **URL:** `/restaurant/create`, which sends `POST /restaurant`.
- **What happens:** `StoreRestaurantRequest` uses `unique:restaurants,name`, which also counts soft-deleted rows. After restaurant 3700 ("QA R Valid Restaurant Edited") was deleted, creating a new restaurant with that name was refused, with no visible message (RS6).

**Possible fix:** `Rule::unique('restaurants', 'name')->whereNull('deleted_at')`.

---

## RS10. Medium: valid names rejected

- **URLs:** `/restaurant/create`, `/restaurant/{id}/edit`.
- **What happens:** the same name rules as events (E8). "QA R Café de l'Opéra" was refused.

---

## RS11. Medium: delete button sends a GET, which returns 405

- **URL:** red delete button on `/restaurant` (`data-delete-url="/restaurant/{id}/delete"`, `data-delete-method="POST"`).
- **What happens:** `submitDeleteRequest()` navigates with GET for `POST`, and `/restaurant/{id}/delete` only accepts `POST|DELETE`, so it returns 405 (same as C2). **Fix RS1 before C2**, or the button will start deleting in-use restaurants.

---

## RS12. Medium: criteria IDs not validated

- **URL:** `/restaurant/create` (`criterias`), which sends `POST /restaurant`.
- **What happens:** `criterias=999999,abc` saved criteria rows **999999** and **0** (both under restaurant 0, see RS2). Same as E6.

---

## RS13. Medium: restaurant pages are slow

- **URLs and timings:** `/restaurant` 1.17 s (176 KB), `/restaurant/create` 1.07 s.

---

# Part 15: `/guide`

Tested as `qa.admin` (admin), `qa.created` (no guide permissions) and a guest. QA guides "QA G …" (ids 21-23 and 987700-987704) and tour package "[QA] Booking Guide Ref" (type 2) were created. `GuideController` follows the same pattern as `EventController`, so most issues match `/event`.

## G1. Critical: deleting a guide that's used in a tour deletes it anyway

- **URLs:** delete on `/guide` (`DELETE /guide/{id}`, `POST|DELETE /guide/{id}/delete`).
- **What happens:** `GuideController@destroy` calls `DeleteModel::check($guide, 'Guide')`, which soft-deletes the guide **because** it is in use (RS1), then returns "Delete … It used in 1 tours!".
- **Evidence:** guide 987703 is referenced by tour package "[QA] Booking Guide Ref" (`type = 2`, `reference = 987703`). After `DELETE /guide/987703` (200), the guide's `deleted_at` was set.

**Possible fix:** fix `DeleteModel::check()` (RS1).

---

## G2. Critical: users with no guide permissions can edit any guide

- **URL:** `POST /guide/{id}/update` (unnamed; the edit form uses the named `PUT /guide/{guide}`).

**Result as `qa.created` (no `guide.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /guide`, `/guide/21/edit` | 403 (correct) |
| `POST /guide/21/update` | 302, **guide renamed** to "QA G Edited Without Permission" |

- `GET /guide/{id}/deleteMsg` is unnamed too.

**Possible fix:** name the route or add `'permissions' => 'guide.edit'`, or remove it.

---

## G3. High: guide page crashes for a missing ID

- **URL:** `/guide/{id}`, e.g. `/guide/999999`.
- **What happens:** `GuideController@show` calls `$guide->getCriterias()` before `if($guide == null) return abort(404);`, so a missing guide gives **500**. (`/guide/999999/edit` returns 404.) Same as E5.

---

## G4. High: mass assignment on create and update

- **URLs:** `/guide/create` (`POST /guide`), `/guide/{id}/edit` (`PUT /guide/{id}`).
- **What happens:** `App\Guide` has `$guarded = []`, and the controller saves `$request->except('attach', 'place_id', 'criterias')`. `id=987700` and `created_at=2001-01-01 00:00:00` were saved, and the guides AUTO_INCREMENT continued at 987701+. Same as E2.

---

## G5. High: guide forms never show validation errors

- **URLs:** `/guide/create`, `/guide/{id}/edit`.
- **What happens:** neither view outputs `$errors`. Each of these redirected back with no message: empty name, duplicate `work_email`, `work_email=nope` / `work_phone=abc` / `website=nope`, and the names in G7. Same as E3.

---

## G6. High: attachments accept any file type

- **URL:** `/guide/create` (`attach[]`), which sends `POST /guide`.
- **What happens:** no rule on `attach`. "QA G SVG Attach" was saved with an SVG containing `<script>`. Same as E4.

---

## G7. Medium: guide names can't contain digits or accents

- **URLs:** `/guide/create`, `/guide/{id}/edit`.
- **What happens:** the guide `name` uses `ValidationService::nameRules` (`/^[a-zA-Z\s\-\.\']+$/`, a person-name rule):

| Name | Result |
|------|--------|
| `QA G Guide 2` | rejected (digit) |
| `QA G José Núñez` | rejected (accents) |
| `QA G O'Brien` | accepted |

  Guide records are often agencies or numbered guides, and many guide names have accents. Combined with G5, the user isn't told why.

**Possible fix:** use `businessNameRules` with Unicode support (see E8), or allow `\pL` and digits.

---

## G8. Medium: `contact_name` has no validation rule

- **URLs:** `/guide/create`, `/guide/{id}/edit`.
- **What happens:** `StoreGuideRequest` and `UpdateGuideRequest` have no `contact_name` rule (events and restaurants use `nameRules`), so any value is saved, e.g. `<script>alert('xss-guide-contact')</script>`. It is escaped on output (see passed), but length and content aren't checked.

**Possible fix:** add `'contact_name' => ValidationService::nameRules(false, 191, 2)` (with the Unicode fix).

---

## G9. Medium: criteria IDs not validated

- **URL:** `/guide/create` (`criterias`), which sends `POST /guide`.
- **What happens:** `criterias=999999,abc` saved `services_has_criterias` rows with criteria **999999** and **0** for the guide. There are currently **no guide criteria at all** in the `criterias` table. Same as E6.

---

## G10. Medium: delete button sends a GET, which returns 405

- **URL:** red delete button on `/guide` (`data-delete-url="/guide/{id}/delete"`, `data-delete-method="POST"`).
- **What happens:** same as C2 and RS11. **Fix G1/RS1 first**, or the button will start deleting in-use guides.

---

# Part 16: `/images/index`

The page shows one image per supplier category (Events, Restaurants (Lunch), Restaurants (Dinner), Hotels, Guides, Flights, Cruises; table `attachmenttypes`) and uploads a replacement through `POST /images/savefile` (`AttachmentController@saveFile`, called by `public/js/attachments.js`). Both routes are 403 for everyone (I1), so `saveFile()` was also tested in-process as `qa.admin`. **Only QA tour 136 was used as the upload target.** The 7 real category images were not touched, because each upload deletes the target's previous image.

## I1. High: no one can open the images page or upload

- **URLs:** `/images/index` (route `images.index`) and `POST /images/savefile` (route `images.savefile`), `routes/web.php:511-512`.
- **What happens:** both route names are used as permissions, and neither `images.index` nor `images.savefile` exists.

| User | `GET /images/index` |
|------|---------------------|
| `qa.admin` (admin) | 403 |
| `qa.agent` (user) | 403 |

**Possible fix:** create the permissions and assign them, or point both routes at an existing permission (e.g. `settings.index` or an admin-only one).

---

## I2. High: uploading several files at once keeps only the last one

- **URL:** `POST /images/savefile` (the controller accepts `files[]`; the page sends one file, but the API takes many).
- **What happens:** inside the loop over files, for **each** file the controller deletes **all** of the model's existing attachments (and unlinks their files) before saving the new one ("Replace image"). With several files, each one deletes the previous.
- **Evidence:** uploading 3 PNGs to QA tour 136 in one request returned `"files": [{"id":36}, {"id":37}, {"id":38}]` with success, but only **38** was still in the database (36 and 37 and their files were deleted during the same request).

**Possible fix:** delete the old attachments once, before the loop (and only for single-image models), or reject more than one file.

---

## I3. High: uploading a tour image deletes all of the tour's other attachments

- **URL:** `POST /images/savefile` with `model=Tour` (allowed by the `model => in:Attachmenttype,Tour` rule).
- **What happens:** the "replace" step removes **every** attachment of the tour (`$model->attachments()->get()` and `delete()`), not just the image being replaced. Tours already have **25** attachments (tour 78 has 2, created by `TourController` at lines 793-802), so one upload to a tour removes all the others.

**Possible fix:** for tours, add the image instead of replacing, or only replace attachments of the same purpose/collection.

---

## I4. High: category images point to a hard-coded old domain

- **URL:** `/images/index` (`<img class="pic" src="{{ $attachmentImage->url }}">`).
- **What happens:**
  - all 7 category images have `url = http://dev.eetstravel.com/uploads/…jpg` and `path = /home/tms/public_html/public/uploads/…` (absolute paths from another server). None of these files exist in this environment, so every card shows a broken image;
  - new uploads store `url = asset($storedPath)`, a full absolute URL including the current host (in-process tests stored `http://localhost/uploads/attachments/…`), so every image breaks when the domain or scheme changes (e.g. http to https).

**Possible fix:** store only the relative `path` (`uploads/attachments/…`) and build the URL at render time with `asset($path)`; migrate the 7 existing rows to relative paths and copy their files.

---

## I5. Medium: upload failures return the raw exception message

- **URL:** `POST /images/savefile`.
- **What happens:** the `catch (\Exception $e)` returns `['error' => 'File upload failed: ' . $e->getMessage()]` with 500, so file-system paths or SQL details reach the browser (and are shown in the toast by `attachments.js`).

**Possible fix:** log the exception and return a generic message.

---

## I6. Low: on edit pages, an upload changes every image

- **URL:** any page whose URL contains "edit" that includes `public/js/attachments.js`.
- **What happens:** in the success handler, the `edit` branch runs:

```js
self.closest('.file-caption-main').find('.file-caption-name').html(that.files[0].name);
$('.pic').attr('src', uploadedFile.url);
```

  - `$('.pic')` updates **every** `.pic` image on the page, not only the one that was uploaded;
  - the chosen file name is inserted with `.html()`, so a file named like `<img src=x onerror=…>.png` runs script in the uploader's own browser (self-XSS).
- The `/images/index` branch is correct: it updates only the card that was uploaded to.

**Possible fix:** scope to the closest card, and use `.text()` for the file name.

---

## I7. Low: two upload inputs per card

- **URL:** `/images/index` (`resources/views/attachment/index.blade.php`, lines 41 and 46).
- **What happens:** each card has a hidden `<input name="fileToUpload[]" class="fileToUpload hide_file">` **and** a visible `<input name="files[]" class="fileToUpload …">`, both with the same `data-model`/`data-id` and both bound to the same `change` handler. It works, but it is confusing and easy to break; keep one input.

---

# Part 17: `/bus/calendar`

The page (`BusController@calendar`, permission `bus_calendar`) draws an amCharts Gantt of buses. It loads its data from `GET /api/bus_days` and saves through `POST /api/add_day` (create trip), `POST /bus_day/update`, `POST /bus_day/update/tour` and `POST /bus_day/delete` (all in `public/js/lib/chart_bus.js`). Tested as `qa.admin`, a **"view only"** user (`qa.created` temporarily given the `view only` role) and a guest. QA buses 35-36 ("[QA] Bus Alpha/Beta"), QA drivers 25-26 and trips "[QA] Trip …" were created.

**Route protection:**

| Route | Protection |
|-------|------------|
| `GET /bus/calendar` | `bus_calendar` (admin, user, **view only**, Super User, Accountant) |
| `GET /api/bus_days` | unnamed: any logged-in user |
| `POST /api/add_day` | unnamed: any logged-in user |
| `POST /bus_day/update`, `/bus_day/update/tour`, `/bus_day/delete` | unnamed: any logged-in user |

## BC1. Critical: one bus day with an unknown status breaks the whole calendar

- **URLs:** `GET /api/bus_days` (calendar data for `/bus/calendar`); the bad row came from `POST /api/add_day`.
- **What happens:**
  - `addApiBusDays()` doesn't validate `status`, so `status=999999` was saved on bus day 280 ("[QA] Trip Bad Refs").
  - `getApiBusDays()` then does `Status::find($item->status_id)` and uses `'color' => $status->color` (`BusController.php:929`) with no null check.
- **Result:** `GET /api/bus_days` returned **500** ("Attempt to read property … on null", `BusController.php` line 930) for **every** user. The calendar shows nothing for anyone until the row is fixed.
- The QA row was changed to status 18 after the test, so the calendar works again (200, 0.55 s). Any real bus day whose status is deleted, or created through the API with a bad status, will cause the same outage.

**Possible fix:** validate `status` (`exists:status,id` with `type = bus`) on create/update, and use `optional($status)->color ?? '#ccc'` in `getApiBusDays()`.

---

## BC2. Critical: "view only" users can create, edit and delete bus schedules

- **URLs:** `POST /api/add_day`, `POST /bus_day/update`, `POST /bus_day/delete` (all unnamed, so `permissions.required` checks nothing).
- **What happens:** the "view only" role has only `bus.index`, `bus.show` and `bus_calendar`, but the write endpoints accept any logged-in user:

| Request as a "view only" user | Result |
|-------------------------------|--------|
| `GET /bus/calendar` | 200 (expected) |
| `POST /api/add_day` | 200: **trip "[QA] Trip By View Only" created** |
| `POST /bus_day/update` | 200 `true`: **trip day edited** (bus and status changed) |
| `POST /bus_day/delete` (`trip_mode=0`) | 200 `true`: **the whole 400-day trip deleted** (400 → 0 rows) |

**Possible fix:** give the write routes explicit permissions (`bus.create`, `bus.edit`, `bus.destroy`), and hide the create/edit/delete controls for users without them.

---

## BC3. Critical: stored XSS in the calendar tooltip

- **URLs:** `/bus/calendar` (tooltip on hover), data from `GET /api/bus_days`.
- **What happens:**
  - `POST /api/add_day` accepted `name_trip=[QA] Trip <img src=x onerror=alert('xss-trip')>`, and `GET /api/bus_days` returns it raw in `tour`;
  - `chart_bus.js:326` (`balloonFunction`) builds the tooltip as an HTML string:

```js
"<i class='fa fa-bus'></i>&nbsp;<span …>" + graph.customData.isTour + ":" + graph.customData.tour +
"</b><br>Bus: " + item.category + "<br>Cities:" + city + "<br><br>Driver:" + graph.customData.driver_name + …
```

  - amCharts renders the balloon as HTML, so the trip name (and bus names, driver names and cities) runs as markup when any user hovers the bar.

**Possible fix:** escape every value before concatenating (e.g. an `escapeHtml()` helper), and validate `name_trip` on the server.

---

## BC4. High: bus, driver and status IDs not validated; "ghost" trips

- **URLs:** `POST /api/add_day`, `POST /bus_day/update`.
- **What happens:** `validateDayTrip()` checks only `name_trip`, `country_begin`, `city_begin` and the dates. `bus_trip`, `drivers_trip[]` and `status` aren't checked:
  - `bus_trip=999999`, `drivers_trip[]=999999`, `status=999999` → saved (bus day 280);
  - no `bus_trip` at all → saved with `bus_id = NULL` (bus day 281, "[QA] Trip No Bus").
- Trips whose bus doesn't exist (or is empty) are **never shown** on the calendar (it only lists existing buses), but they still exist and count in the busy checks.

**Possible fix:** `bus_trip => required|exists:buses,id`, `drivers_trip.* => exists:drivers,id`, `status => required|exists:status,id`.

---

## BC5. High: no limit on trip length

- **URL:** `POST /api/add_day`.
- **What happens:** the only date rule is `end_date after_or_equal:start_date`. A trip from +30 to +429 days created **400 `bus_days` rows** in one request (plus 400 driver links). There is no upper bound, so a multi-year range inserts thousands of rows and runs the busy checks over every day.

**Possible fix:** add a maximum (e.g. `before_or_equal:start_date + 60 days`).

---

## BC6. High: update with an unknown status crashes in the activity log

- **URL:** `POST /bus_day/update` (edit trip modal).
- **What happens:** `status=999999` on a whole-trip update returned **500** from `app/Helper/Trackable.php:230` ("Attempt to read property \"name\" on null"). The activity logger looks up the status name without a null check. The change was rolled back, but the user gets a generic error instead of a validation message.

**Possible fix:** validate `status` (BC4) and null-check in `Trackable`.

---

## BC7. Medium: past trips can't be viewed

- **URL:** `GET /api/bus_days`.
- **What happens:** `getApiBusDays()` always loads from the **1st of the current month** to **6 months after the end of this month**, with no parameters. The QA trip 60 days in the past ("[QA] Trip Past") never appears, and there is no way to browse earlier months or plan beyond 6 months.

**Possible fix:** accept `from`/`to` from the chart's visible range.

---

## BC8. Medium: trip create echoes the whole request

- **URL:** `POST /api/add_day`.
- **What happens:**
  - on success it returns `response()->json($request)`, the posted form, instead of the created trip/bus days;
  - the busy messages are hard-coded English ("The Bus is Busy from … to …", "The Driver is Busy") while `/bus_day/update` uses `trans()`.

**Possible fix:** return `{success: true, trip_id, bus_day_ids}` and use translation keys.

---

## BC9. Medium: calendar data built with heavy per-row queries

- **URL:** `GET /api/bus_days`.
- **What happens:** for every bus day, `getApiBusDays()` queries the bus, drivers, status and comments. For tour bus days it then loads every tour day's packages and, per hotel package, runs `Hotel::where('name', …)->orWhere('name', 'like', '%…%')`. It also uses `groupBy('tour_package_id')->groupBy('bus_id')->get()` while selecting all columns (fails under MySQL `ONLY_FULL_GROUP_BY`). Today it takes 0.5 s for 25 segments, but it grows with every tour.

**Possible fix:** eager-load relations, resolve hotel cities by id instead of by name `LIKE`, and select explicit columns.

---

## BC10. Low: calendar page loads every bus, driver, tour and transfer

- **URL:** `GET /bus/calendar` (206 KB, 0.65 s).
- **What happens:** `calendar()` passes `Bus::all()`, `Driver::all()`, `Tour::all()` and `Transfer::all()` to the view for the create-trip modal dropdowns. This grows with every tour.

**Possible fix:** load the dropdown options on demand (AJAX search).

---

# Part 18: `/transfer` (Bus Companies)

Tested as `qa.admin`, `qa.created` (no transfer permissions) and a guest. QA bus companies "QA T …" (ids 177-178 and 987800-987804), tour package "[QA] Booking Transfer Ref" (type 3), "[QA] Bus Fleet" and "[QA] Driver Fleet" were created. `TransferController` follows the same pattern as `EventController` and `GuideController`, so most issues match `/event` and `/guide`.

## TR1. Critical: deleting a bus company that's used in a tour deletes it anyway

- **URLs:** delete on `/transfer` (`DELETE /transfer/{id}`, `POST|DELETE /transfer/{id}/delete`).
- **What happens:** `TransferController@destroy` calls `DeleteModel::check($transfer, 'Transfer')`, which soft-deletes the record **because** it is in use (RS1).
- **Evidence:** bus company 987803 is referenced by tour package "[QA] Booking Transfer Ref" (`type = 3`). After `DELETE /transfer/987803` (200), its `deleted_at` was set.

**Possible fix:** fix `DeleteModel::check()` (RS1).

---

## TR2. Critical: users with no transfer permissions can edit any bus company

- **URL:** `POST /transfer/{id}/update` (unnamed; the edit form uses `PUT /transfer/{transfer}`).

**Result as `qa.created` (no `transfer.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /transfer`, `/transfer/177/edit` | 403 (correct) |
| `POST /transfer/177/update` | 302, **bus company renamed** to "QA T Edited Without Permission" |

- `GET /transfer/{id}/deleteMsg` is unnamed too.

**Possible fix:** name the route or add `'permissions' => 'transfer.edit'`, or remove it.

---

## TR3. High: bus company page crashes for a missing ID

- **URL:** `/transfer/{id}`, e.g. `/transfer/999999`.
- **What happens:** `TransferController@show` calls `$transfer->getCriterias()` before `if($transfer == null) return abort(404);`, so a missing ID gives **500**. (`/transfer/999999/edit` returns 404 via `findOrFail`.) Same as E5 and G3.

---

## TR4. High: mass assignment on create and update

- **URLs:** `/transfer/create` (`POST /transfer`), `/transfer/{id}/edit` (`PUT /transfer/{id}`).
- **What happens:** `App\Transfer` has `$guarded = []`, and the controller saves `$request->except('attach', 'place_id', 'criterias')`. `id=987800` and `created_at=2001-01-01 00:00:00` were saved, and the transfers AUTO_INCREMENT continued at 987801+. Same as E2.

---

## TR5. High: bus company forms never show validation errors

- **URLs:** `/transfer/create`, `/transfer/{id}/edit`.
- **What happens:** neither view outputs `$errors`. These were all rejected with no visible message: empty name, duplicate `work_email`, `work_email=nope` / `work_phone=abc` / `website=nope`, and "QA T Bus Müller's" (TR8). Same as E3.

---

## TR6. High: attachments accept any file type

- **URL:** `/transfer/create` (`attach[]`), which sends `POST /transfer`.
- **What happens:** no rule on `attach`; "QA T SVG Attach" was saved with an SVG containing `<script>`. Same as E4.

---

## TR7. Medium: deleting a bus company leaves its buses and drivers active

- **URLs:** delete on `/transfer`; the leftovers show on `/bus` and `/bus/calendar`.
- **What happens:**
  - buses and drivers belong to a bus company via `buses.transfer_id` / `drivers.transfer_id` (31 buses and 20 drivers are linked today);
  - `DeleteModel::check($transfer, 'Transfer')` only counts tour packages and `destroy()` doesn't touch buses or drivers;
  - after deleting bus company 987804, its bus "[QA] Bus Fleet" stayed active and its driver "[QA] Driver Fleet" remained, both still pointing at the deleted company. `/bus` (200) and the bus calendar keep showing that bus.

**Possible fix:** block deleting a bus company that still has buses/drivers (show the counts), or detach/soft-delete them together.

---

## TR8. Medium: valid names rejected

- **URLs:** `/transfer/create`, `/transfer/{id}/edit`.
- **What happens:** the name uses `businessNameRules` (`/^[a-zA-Z0-9\s\-\.\&\(\)]+$/`), so "QA T Bus Müller's" (umlaut and apostrophe) was refused. Same as E8.

---

## TR9. Medium: criteria IDs not validated

- **URL:** `/transfer/create` (`criterias`), which sends `POST /transfer`.
- **What happens:** `criterias=999999,abc` saved criteria rows **999999** and **0** (service type 3). There are currently no transfer criteria in the `criterias` table. Same as E6.

---

## TR10. Medium: delete button sends a GET, which returns 405

- **URL:** red delete button on `/transfer` (`data-delete-url=".../transfer/{id}/delete"`, `data-delete-method="POST"`).
- **What happens:** same as C2, RS11 and G10. **Fix TR1/RS1 first**, or the button will start deleting in-use bus companies.

---

# Part 19: `/bus`

Tested as `qa.admin`, `qa.created` (no bus permissions) and a guest. QA buses "QA Bus …" (ids 38-42) and bus day "[QA] Trip Bus Guard" were created. `BusController` CRUD is better written than the other supplier controllers: it saves only `name`, `bus_number` and `transfer_id`, `show()` checks for a missing bus first, the create form displays validation errors, and the `Bus` case in `DeleteModel` returns early, so the delete guard works.

## BU1. Critical: users with no bus permissions can edit any bus

- **URL:** `POST /bus/{id}/update`. This is the **edit form's own action** (`resources/views/bus/edit.blade.php:23`: `action='{!! url("bus")!!}/{!!$bus->id!!}/update'`), and the route is unnamed, so `permissions.required` checks nothing.

**Result as `qa.created` (no `bus.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /bus`, `/bus/41/edit` | 403 (correct) |
| `POST /bus/41/update` | 200, **bus renamed** to "QA Bus Edited Without Permission" |

- `GET /bus/{id}/deleteMsg` is unnamed too.

**Possible fix:** give the route `'permissions' => 'bus.edit'` (or point the form at `PUT /bus/{bu}`).

---

## BU2. High: saving a bus shows a raw JSON page

- **URLs:** `/bus/create` (form posts to `POST /bus`) and `/bus/{id}/edit` (form posts to `POST /bus/{id}/update`).
- **What happens:** both forms are plain HTML `<form method="POST">` (no AJAX; `global-validation.js` only validates on submit). `BusController@store` and `@update` always `return response()->json(['route' => route('bus.index')])`. After a successful save the browser shows:

```
{"route":"http:\/\/127.0.0.1:8010\/bus"}
```

  instead of returning to the bus list. (Validation errors still redirect back correctly.)

**Possible fix:** return `redirect()->route('bus.index')` for normal requests and JSON only for AJAX (`$request->expectsJson()`), as `destroy()` already does.

---

## BU3. High: buses can be attached to a deleted bus company

- **URLs:** `/bus/create`, `/bus/{id}/edit` (field `transfer_id`), which send `POST /bus` and `POST /bus/{id}/update`.
- **What happens:** the rule `transfer_id => nullable|exists:transfers,id` doesn't exclude soft-deleted rows. Bus 39 "QA Bus Deleted Company" was created with `transfer_id = 987804`, a bus company deleted in the `/transfer` test (see TR7).

**Possible fix:** `Rule::exists('transfers', 'id')->whereNull('deleted_at')`.

---

## BU4. Medium: a deleted bus's number can never be reused

- **URL:** `/bus/create` (`bus_number`), which sends `POST /bus`.
- **What happens:** `unique:buses,bus_number` also counts soft-deleted buses. After deleting bus 40, creating a new bus with its number was refused ("has already been taken").

**Possible fix:** `Rule::unique('buses', 'bus_number')->whereNull('deleted_at')` (and the same with `ignore($id)` on update).

---

## BU5. Medium: delete button sends a GET, which returns 405

- **URL:** red delete button on `/bus` (`data-delete-url=".../bus/{id}/delete"`, `data-delete-method="POST"`).
- **What happens:** same as C2. `GET /bus/40/delete` returns **405**. A real `POST /bus/{id}/delete` works correctly (see passed).

---

## BU6. Medium: bus number has no format check and is shown raw in the bus calendar

- **URLs:** `/bus/create` (`POST /bus`); shown on `/bus/calendar` via `GET /api/bus_days`.
- **What happens:** `bus_number` is only `required|string|max:50`. `<img src=x onerror=alert('xss-busno')>` was accepted (bus 42). It is escaped on `/bus`, `/bus/{id}` and `/bus/{id}/edit`, but `getApiBusDays()` builds each calendar row label as `name(bus_number)`, and `chart_bus.js` puts that label (`item.category`) into the **unescaped** tooltip HTML (BC3).

**Possible fix:** restrict `bus_number` (e.g. `regex:/^[A-Za-z0-9\- ]+$/`) and fix BC3.

---

## BU7. Low: `capacity` and `comments` are validated but never saved

- **URLs:** `/bus/create`, `/bus/{id}/edit`.
- **What happens:** `StoreBusRequest`/`UpdateBusRequest` validate `capacity` (1-500) and `comments`, but `store()`/`update()` save only `name`, `bus_number` and `transfer_id`, the `buses` table has no such columns, and the forms don't show the fields. `capacity=9999` is rejected for nothing; `capacity=50` is silently dropped.

**Possible fix:** remove the unused rules, or add the columns and fields.

---

## BU8. Low: umlauts rejected in bus names

- **URLs:** `/bus/create`, `/bus/{id}/edit`.
- **What happens:** `businessNameRules` refused "QA Bus Müller". Unlike other suppliers, the form shows the error. Same root cause as E8.

---

# Part 20: `/driver`

Tested as `qa.admin`, `qa.created` (no driver permissions) and a guest. QA drivers "QA Driver …" (ids 28-36) and bus day 688 "[QA] Trip Driver Guard" were created. **`App\Driver` has no `SoftDeletes`, so driver deletes are permanent.** The edit form uses the named, protected `PUT /driver/{driver}` (there is no unnamed update route), and the controller saves only `name`, `phone`, `email` and `transfer_id`.

## DR1. Critical: deleting a driver assigned to trips deletes them permanently

- **URLs:** delete on `/driver` (`POST|DELETE /driver/{id}/delete`, `DELETE /driver/{driver}`).
- **What happens:** `DriverController@destroy` calls `DeleteModel::check($driver, 'Driver')`, which counts `transfer_to_drivers` + `trip_to_drivers` rows and, when the driver **is** in use, calls `$model->delete()` (RS1). Because `Driver` has no soft deletes, this is a **hard delete**.
- **Evidence:**
  - driver 30 was assigned to bus day 688 (`trip_to_drivers`);
  - `POST /driver/30/delete` returned 200 with the "cannot delete" flash path;
  - afterwards the `drivers` row **no longer exists**, and 1 `trip_to_drivers` row still points at driver 30.
- **Impact:** drivers on real trips can be permanently removed, leaving trips and transfers pointing at a missing driver. Unlike suppliers, this cannot be undone by clearing `deleted_at`.

**Possible fix:** fix `DeleteModel::check()` (RS1); consider adding `SoftDeletes` to `Driver`.

---

## DR2. Critical: deleting an unused driver always returns 500

- **URL:** `POST /driver/{id}/delete` (delete button on `/driver`).
- **What happens:** for a driver that is not in use, `destroy()` runs `deleteById($id)` and `removeFile($driver)`, then:

```php
Comment::query()->where('reference_type', Comment::$services['driver'])->...   // DriverController.php:215
```

  `Comment::$services` has no `'driver'` key (it has `bus`, `transfer`, `BusDay`, …), so the request crashes with **500** "Undefined array key \"driver\"" **after** the driver was already permanently deleted.
- **Evidence:** `POST /driver/35/delete` returned 500, and driver 35 no longer exists.
- **Impact:** every successful driver delete shows an error to the user (who may retry or report a failure), while the data is already gone.

**Possible fix:** add a `driver` entry to `Comment::$services` (or remove the line), and run the comment cleanup before the delete in a transaction.

---

## DR3. High: stored XSS on the driver page

- **URL:** `/driver/{id}` (`resources/views/driver/show.blade.php`).
- **What happens:** the show view prints raw HTML:

```blade
{!!$driver->name!!}  {!!$driver->phone!!}  {!!$driver->email!!}  {!!@$driver->transfer->name!!}
```

  and `name`/`phone` have no format rules (DR7). Driver 36, with name `QA Driver <img src=x onerror=alert('xss-driver')>` and phone `<script>alert('xss-phone')</script>`, rendered **2 raw** payloads on `/driver/36`. `/driver/36/edit` and the `/driver` list escape them.

**Possible fix:** use `{{ }}` for all four values.

---

## DR4. High: any logged-in user can list a bus company's drivers and buses

- **URLs:** `GET /driver_bus_transfer/api/{transfer_id}` and `GET /driver_bus_transfer/table/api/{transfer_id}` (`BusController@getDriverAndBusTransfer*`, unnamed routes).
- **What happens:** no permission is checked. `qa.created` (no `driver.*`/`bus.*`/`transfer.*`) got **200** with an HTML list of company 5's drivers (e.g. "QA Driver Valid Edited") and buses. `/driver_bus_transfer/api/999999` also returns 200 (empty) instead of 404.

**Possible fix:** give the routes a permission (e.g. `tour_package.edit` or `bus.index`) and 404 for unknown companies.

---

## DR5. High: saving a driver shows a raw JSON page

- **URLs:** `/driver/create` (form posts to `POST /driver`), `/driver/{id}/edit` (form `PUT /driver/{id}`).
- **What happens:** both are plain form submits, but `store()` and `update()` always return `response()->json(['route' => route('driver.index')])`. After saving, the browser shows `{"route":"http:\/\/127.0.0.1:8010\/driver"}`. Same as BU2.

**Possible fix:** redirect for normal requests; JSON only when `expectsJson()`.

---

## DR6. High: unknown company ID causes a database error

- **URL:** `/driver/create` (`transfer_id`), which sends `POST /driver`.
- **What happens:** `store()`/`update()` validate only `name`, `phone` and `email`. `transfer_id=999999` reaches the database and fails on the `drivers_transfer_id_foreign` foreign key (`SQLSTATE[23000] 1452 Cannot add or update a child row …`, logged). The user gets a generic error instead of a field message.

**Possible fix:** add `'transfer_id' => ['nullable', Rule::exists('transfers', 'id')->whereNull('deleted_at')]`.

---

## DR7. Medium: very weak validation

- **URLs:** `/driver/create`, `/driver/{id}/edit`.
- **What happens:** the rules are only `name => required`, `phone => nullable`, `email => nullable|email`:

| Input | Result |
|-------|--------|
| 300-character name | saved, silently cut to 191 characters (driver 32) |
| `phone=abc` | saved (driver 29) |
| same email as driver 28 | saved (driver 30), so **duplicate emails** |
| `<img …>` in name, `<script>` in phone | saved (DR3) |

**Possible fix:** `name => required|max:191` with a name regex, `phone =>` the shared `phoneRules`, `email => unique:drivers,email,{id}`.

---

## DR8. Medium: drivers can be attached to a deleted bus company

- **URL:** `/driver/create` (`transfer_id`), which sends `POST /driver`.
- **What happens:** driver 34 "QA Driver Deleted Company" was saved with `transfer_id = 987804` (a soft-deleted bus company; the foreign key only checks that the row exists). Same as BU3; see also TR7.

---

## DR9. Medium: delete button sends a GET, which returns 405

- **URL:** red delete button on `/driver` (`data-delete-url=".../driver/{id}/delete"`, `data-delete-method="POST"`).
- **What happens:** same as C2. `GET /driver/28/delete` returns **405**. **Fix DR1 and DR2 first.** Once the button works, it will permanently delete in-use drivers and then show a 500.

---

## DR10. Low: unused `getButton()` builds routes with the wrong parameter

- **URL:** none (helper `DriverController::getButton()`).
- **What happens:** it calls `route('driver.show', ['id' => $id])` and `route('driver.edit', ['id' => $id])`, but the resource parameter is `{driver}`, so these would build `/driver?id=…`-style URLs. It isn't called anywhere found; remove it or fix the parameter name.

---

## DR11. Low: four finance tables use the `latin1` character set

- **URLs:** invoice and office-fee screens that write to these tables (found while checking driver encoding).
- **What happens:** `information_schema` shows `client_invoices`, `invoices_tours`, `office_fees` and `supplier_invoices` with collation `latin1_swedish_ci`, while the rest of the database (and the connection) is `utf8mb4`. Characters outside Latin-1 (e.g. `č`, `ő`, `ł`, `€`) in client names or notes on invoices are stored incorrectly. (The `drivers` table is `utf8mb4`; the "Jos? N??ez" seen while testing came from the test shell's encoding, not the app.)

**Possible fix:** convert the four tables to `utf8mb4` in a migration (`ALTER TABLE … CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`), after checking existing data.

---

# Part 21: `/status`

Tested as `qa.admin`, `qa.created` (no status permissions) and a guest. Only QA statuses "QA Status …" (ids 68-77) were created, edited or deleted. QA task 272 and QA hotel offer 103 were temporarily pointed at QA statuses and restored afterwards. `App\Status` has **no soft deletes** (deletes are permanent). `destroy()` does check usage before deleting.

**Status IDs the code depends on** (from `app/`):

| ID | Name (type) | Used in code for |
|----|-------------|------------------|
| 1, 4 | Pending, Go Ahead (tour) | tour colours / dashboard |
| 2, 7, 8 | Pending, Completed, Abort (task) | task colours, quick-create |
| 6, 39, 46 | Cancelled, Confirmed, Requested (tour) | archive list (`whereIn('status', [6, 39])`), offers, client portal |
| 9, 23 | Confirmed (service_in_tour / hotel) | "all services confirmed" checks |
| 17 | Confirmed (bus) | transfer confirmation |
| 10, 34 | Allotment Reserved/Used (hotel) | agreements |

## ST1. Critical: users with no status permissions can edit any status

- **URL:** `POST /status/{id}/update` (unnamed; the edit form at `/status/{id}/edit` uses the named `PUT /status/{status}`).

**Result as `qa.created` (no `status.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /status`, `/status/69/edit` | 403 (correct) |
| `POST /status/69/update` | 302, **status renamed** to "QA Status Edited Without Permission" and its colour cleared |

- **Impact:** any user can rename or retype the statuses above, breaking tour, task, hotel and bus workflows app-wide.

**Possible fix:** give the route `'permissions' => 'status.edit'`, or remove it.

---

## ST2. High: stored XSS on the status page

- **URL:** `/status/{id}` (`resources/views/status/show.blade.php`).
- **What happens:** the view prints `{!!$status->name!!}` (line 27), `{!!$status->sort_order!!}` (line 39) and `{!!$status->color!!}` (line 47). Status 74, with name `QA Status <img src=x onerror=alert('xss-status-name')>` and colour `<img src=x onerror=alert('xss-status-color')>`, rendered the payload raw on `/status/74`. `/status/74/edit` and the `/status` list escape it.

**Possible fix:** use `{{ }}`, and validate `color` (ST10).

---

## ST3. High: a status in use can be switched to another type

- **URL:** `/status/{id}/edit`, which sends `PUT /status/{id}`.
- **What happens:** `update()` validates only `name`, `type` and `sort_order`, and doesn't check usage (only `destroy()` does).
- **Evidence:**
  - QA task 272 was set to task status 68;
  - changing status 68's type from `task` to `tour` saved;
  - task 272 then carried a "tour" status, and the status disappeared from the task form's status list (task statuses dropped to 7).
- The same would happen to real statuses (e.g. retyping task status 2 "Pending").

**Possible fix:** block type changes for statuses that are in use (reuse the `$usage` map from `destroy()`), or make `type` read-only after creation.

---

## ST4. High: new task statuses can never be "Completed" or "Aborted"

- **URLs:** `/status/create`, `/status/{id}/edit`.
- **What happens:** the task lists (`/task`, `/home`) group tasks by `status.is_completed` / `status.is_aborted`. The status form has no such fields, and `store()`/`update()` don't read them. Posting `is_completed=1&is_aborted=1` still saved `0/0` (status 75). Any task status created in the UI always counts as To-Do.

**Possible fix:** add "Completed"/"Aborted" checkboxes for task statuses and save them (they are already in `$fillable`).

---

## ST5. High: status type not validated

- **URL:** `/status/create` (`type`), which sends `POST /status`.
- **What happens:** `type => required` only. `type=nonexistent` was saved (status 70). Valid types are the rows of `status_types` (`tour, task, service_in_tour, hotel, bus, offer`), and there are also 2 existing statuses of type `discard`. A status with an unknown type is listed on `/status` but never offered in any dropdown.

**Possible fix:** `'type' => 'required|exists:status_types,type'`.

---

## ST6. High: statuses the code depends on by ID are not protected

- **URLs:** `/status/{id}/edit` (`PUT /status/{id}`), delete on `/status`.
- **What happens:** the IDs in the table above are hard-coded throughout `app/` (e.g. `TourController.php:656` `whereIn('status', [6, 39])`, `:521` `status == 23 || status == 9`, `:1961` `status == 17`, `TMSClient/LoginController.php:120` `whereIn('status', [6, 46])`). Nothing stops renaming them, changing their type (ST3), or deleting them when they happen to be unused, which silently breaks those features.

**Possible fix:** mark system statuses (e.g. an `is_system` flag) and block delete and type changes for them; longer-term, replace magic IDs with named constants or flags.

---

## ST7. Medium: offer statuses in use are deleted anyway

- **URL:** delete on `/status` (`POST /status/{id}/delete`).
- **What happens:** for type `offer`, `destroy()` checks `hotel_offers.tms_status = id`, but offers store their status as **text** in `hotel_offers.status` (e.g. "Offered with Option").
- **Evidence:** QA offer 103 had `status = "QA Status Offer Text"`. Deleting offer status 77 returned 200 and the status was **permanently deleted**, while offer 103 still showed "QA Status Offer Text" (a value no longer in the list). Offer 103 was restored afterwards.

**Possible fix:** also check `hotel_offers.status = name` for offer statuses (or store offer statuses by id).

---

## ST8. Medium: duplicate statuses allowed

- **URL:** `/status/create`, which sends `POST /status`.
- **What happens:** there is no uniqueness rule. Two statuses "QA Status Tour" with type `tour` were both saved, so dropdowns show the same label twice with different IDs.

**Possible fix:** `Rule::unique('status')->where('type', $request->type)` on `name`.

---

## ST9. Medium: sort order rounded and long names silently cut

- **URL:** `/status/create`, which sends `POST /status`.
- **What happens:**
  - `sort_order => required|numeric` accepts decimals and negatives: `-5.5` was stored as **-6** (integer column);
  - a 300-character name was stored as 191 characters (no `max`).

**Possible fix:** `sort_order => required|integer|min:0`, `name => required|max:191`.

---

## ST10. Medium: color is free text used inside `style` attributes

- **URL:** `/status/create` (`color`), which sends `POST /status`.
- **What happens:** no rule on `color`. `red;background-image:url(https://example.invalid/x)` was saved (status 73). Status colours are written into inline styles across the app (e.g. `style="background-color: {{ $status->color }}"` on `/status/{id}`, offer and tour badges, the bus calendar), and printed raw on the show page (ST2).

**Possible fix:** `'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{3,8}$/']`.

---

## ST11. Low: delete button sends a GET, which returns 405

- **URL:** red delete button on `/status` (`data-delete-url=".../status/{id}/delete"`, `data-delete-method="POST"`).
- **What happens:** same as C2. `GET /status/{id}/delete` returns 405. A real POST works, and the in-use check is correct (see passed).

---

## ST12. Low: AJAX show returns a task URL

- **URL:** `GET /status/{id}` with `X-Requested-With: XMLHttpRequest`.
- **What happens:** `show()` returns `URL::to('task/' . $id)`. `/status/2` returned `http://127.0.0.1:8010/task/2` instead of the status URL (copy-paste from `TaskController`).

**Possible fix:** return `URL::to('status/' . $id)`.

---

# Part 22: `/room_types`

Tested as `qa.admin`, `qa.created` (no room-type permissions) and a guest. QA room types "QA Room …" (ids 17-22) were created; only those were edited or deleted. The code-rename check (RT3) was run inside a database transaction and **rolled back**; real room type 2 still has code `DOU`. `RoomTypesController@destroy` checks 6 tables before deleting (see passed).

## RT1. Critical: users with no room-type permissions can edit any room type

- **URL:** `POST /room_types/{id}/update` (unnamed; the edit form uses the named `PUT /room_types/{room_type}`).

**Result as `qa.created` (no `room_types.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /room_types`, `/room_types/17/edit` | 403 (correct) |
| `POST /room_types/17/update` | 302, **room type renamed** to "QA Room Edited Without Permission" |

- **Impact:** with RT3, any user could rename `DOU`/`SIN` and break pricing for every package with room types.

**Possible fix:** give the route `'permissions' => 'room_types.edit'`, or remove it.

---

## RT2. Critical: SVG images with scripts are accepted and served publicly

- **URLs:** `/room_types/create` and `/room_types/{id}/edit` (field `image`), which send `POST /room_types` / `PUT /room_types/{id}`. The stored file is served at `/assets/img/svg/{file}`.
- **What happens:**
  - the rule is `image => nullable|file|mimes:svg,png,jpg,jpeg,gif,webp|max:2048`, so **SVG is explicitly allowed**;
  - `storeImage()` keeps the `.svg` extension and moves the file into `public/assets/img/svg/`;
  - room type 22 was created with an SVG containing `<script>alert("qa-svg-xss")</script>`, and `GET /assets/img/svg/1793195440_6ae1fdb0740c8.svg` returned **200 `image/svg+xml` without login**, with the `<script>` intact.
- Opening that URL (e.g. from a link) runs the script on the application's own domain.

**Possible fix:** drop `svg` from the allowed types (or sanitize SVGs and serve them with `Content-Disposition: attachment` / a strict `Content-Security-Policy`).

---

## RT3. High: changing the `DOU`/`SIN` code crashes package price calculations

- **URLs:** `/room_types/{id}/edit` (`PUT /room_types/{id}`, or RT1); the crash happens wherever package prices are shown (tour packages, offers, quotations).
- **What happens:** `app/TourPackage.php` looks up room types **by code**:

```php
$doubleRoom = RoomTypes::where(['code' => 'DOU'])->first();   // getDoublePrice(), line 311
...
return $item->room_type_id == $doubleRoom->id;                // line 315
$doubleRoom = RoomTypes::where(['code' => 'SIN'])->first();   // getSinglePrice(), line 325
```

  and `$roomsPeopleCount` hard-codes `SIN, DOU, TRI, DFS, TWN, SUE`.
- **Evidence (transaction, rolled back):** for package 62 (3 room rows), `getDoublePrice()` returned 0 normally. After renaming room type 2's code `DOU` to `DBL` it threw `ErrorException: Attempt to read property "id" on null (TourPackage.php:315)`, which is a **500** in the app. Nothing in the room-type form prevents such a rename.

**Possible fix:** make `code` read-only for system room types (or reference them by id), and null-check `$doubleRoom`/`$singleRoom`.

---

## RT4. High: stored XSS on the room-type page

- **URL:** `/room_types/{id}` (`resources/views/room_types/show.blade.php`).
- **What happens:** the view prints `{!!$room_types->name!!}`, `{!!$room_types->code!!}` and `{!!$room_types->sort_order!!}`. Room type 21 (name `QA Room <img src=x onerror=alert('xss-room')>`, code `<script>alert('xss-code')</script>`) rendered **2 raw** payloads on `/room_types/21`. `/room_types/21/edit` and the list escape them.

**Possible fix:** use `{{ }}`.

---

## RT5. High: duplicate codes and names allowed

- **URL:** `/room_types/create`, which sends `POST /room_types`.
- **What happens:** `validateRoomTypes()` only requires `name`, `code` and a numeric `sort_order`, with no uniqueness:
  - room type 18 "QA Room Dup DOU" was saved with code **`DOU`**, so two room types now had `DOU` (2 "Double" and 18). Which one `TourPackage` uses (RT3) depends on database order;
  - two room types named "QA Room Valid" were both saved.

**Possible fix:** `code => required|max:10|unique:room_types,code,{id}` and `name => required|max:191|unique:room_types,name,{id}`.

---

## RT6. Medium: long names silently cut

- **URL:** `/room_types/create`, which sends `POST /room_types`.
- **What happens:** a 300-character name was stored as 191 characters (room type 20) with no error (`name` has no `max`).

---

## RT7. Medium: delete button sends a GET, which returns 405

- **URL:** red delete button on `/room_types`.
- **What happens:** same as C2. `GET /room_types/17/delete` returns **405**. A real POST works, and the in-use check is correct (see passed).

---

## RT8. Medium: room-type deletes are permanent

- **URL:** delete on `/room_types` (`POST /room_types/{id}/delete`).
- **What happens:** `App\RoomTypes` has no `SoftDeletes`. Unused room type 18 was permanently removed. Historic prices or reports that stored the room-type name elsewhere can no longer be traced back.

**Possible fix:** add soft deletes, or keep the hard delete with a clearer confirmation.

---

## RT9. Low: `sort_order` accepts decimals and negatives

- **URL:** `/room_types/create`, which sends `POST /room_types`.
- **What happens:** `sort_order => required|numeric`, the same as ST9. Use `integer|min:0`.

---

## RT10. Low: unused `getButton()` builds routes with the wrong parameter

- **URL:** none (`RoomTypesController::getButton()`).
- **What happens:** it calls `route('room_types.show', ['id' => $id])` while the resource parameter is `{room_type}`. It isn't called anywhere found. Same as DR10.

---

# Part 23: `/rate`

Rates are the star ratings (e.g. "4****", "5*****+") used by hotels, events, restaurants, guides and bus companies. Tested as `qa.admin`, `qa.created` (no rate permissions) and a guest. QA rates "QA Rate …" (ids 16-20) were created; QA hotel 7136 and QA event 2842 were temporarily pointed at QA rates and then cleared. **`App\Rate` has no soft deletes, so rate deletes are permanent.**

**Current usage of rates** (rows with a `rate` set): hotels 8, events 55, restaurants 12, guides 28, transfers 18.

## RA1. Critical: deleting a rate used by hotels deletes it permanently

- **URLs:** delete on `/rate` (`POST|DELETE /rate/{id}/delete`, `DELETE /rate/{rate}`).
- **What happens:** `RateController@destroy` calls `DeleteModel::check($rate, 'Rate')`. When `$rate->hotels->count() > 0` it sets the "is used by hotels!" message **and** calls `$model->delete()` (RS1). Because `Rate` has no soft deletes, the rate is removed for good.
- **Evidence:** QA rate 17 was set on QA hotel 7136 (`hotels.rate = 17`). `POST /rate/17/delete` returned 200 (the "in use" message path), the `rates` row **no longer exists**, and hotel 7136 still had `rate = 17`.

**Possible fix:** fix `DeleteModel::check()` (RS1); consider soft deletes for rates.

---

## RA2. Critical: rates used by events, restaurants, guides or bus companies are never checked

- **URL:** delete on `/rate` (`POST /rate/{id}/delete`).
- **What happens:** the `Rate` case in `DeleteModel::check()` only counts `$model->hotels`. Events (55), restaurants (12), guides (28) and transfers (18) also store a `rate`, but they're ignored.
- **Evidence:** QA rate 16 was set on QA event 2842. `POST /rate/16/delete` deleted it **with no warning**; `/event/2842` still opens (200), but its rate now points at a missing row.

**Possible fix:** check all five tables (`hotels`, `events`, `restaurants`, `guides`, `transfers`) before deleting.

---

## RA3. Critical: users with no rate permissions can edit any rate

- **URL:** `POST /rate/{id}/update`. This is the **edit form's own action** (`resources/views/rate/edit.blade.php`: `action='{!! url("rate")!!}/{!!$rate->id!!}/update'`), and the route is unnamed.

**Result as `qa.created` (no `rate.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /rate`, `/rate/20/edit` | 403 (correct) |
| `POST /rate/20/update` | 302, **rate renamed** to "QA Rate Edited Without Permission" |

**Possible fix:** give the route `'permissions' => 'rate.edit'`, or point the form at `PUT /rate/{rate}`.

---

## RA4. High: stored XSS on the rate page

- **URL:** `/rate/{id}` (`resources/views/rate/show.blade.php`).
- **What happens:** the view prints `{!!$rate->name!!}`, `{!!$rate->mark!!}` and `{!!$rate->sort_order!!}`. Rate 20 (name `QA Rate <img src=x onerror=alert('xss-rate')>`, mark `<script>alert('xss-mark')</script>`) rendered **2 raw** payloads on `/rate/20`.
- `/rate/20/edit`, the `/rate` list and the rate dropdowns on `/event/create` and `/hotel/create` escape it.

**Possible fix:** use `{{ }}`.

---

## RA5. High: rate type not validated

- **URLs:** `/rate/create` (`POST /rate`), `/rate/{id}/edit` (`POST /rate/{id}/update`).
- **What happens:** `validateRate()` only requires `rate_type`. `rate_type=999999` was saved (rate 17). Valid types are the rows of `rate_types` (1 hotelstart, 2 interate, 3 noterate, 4 servicerate, 5 tourrate). Existing data is already inconsistent: rate 11 has `rate_type = "NULL"` (the string), and rates 3-7 and 9 have `rate_type = NULL`.

**Possible fix:** `'rate_type' => 'required|exists:rate_types,id'`, and clean up the existing `"NULL"`/null values.

---

## RA6. Medium: duplicate rate names allowed

- **URL:** `/rate/create`, which sends `POST /rate`.
- **What happens:** two rates named "QA Rate Valid" were both saved. Rate dropdowns on hotels, events and others then show the same label twice.

**Possible fix:** `'name' => 'required|max:191|unique:rates,name,{id}'`.

---

## RA7. Medium: long names silently cut

- **URL:** `/rate/create`, which sends `POST /rate`.
- **What happens:** a 300-character name was stored as 191 characters (rate 19), with no `max` rule.

---

## RA8. Medium: delete button sends a GET, which returns 405

- **URL:** red delete button on `/rate`.
- **What happens:** same as C2. `GET /rate/20/delete` returns **405**. **Fix RA1 and RA2 first**, or the working button will permanently delete rates in use.

---

## RA9. Low: sort order and helper

- **URLs:** `/rate/create` (`POST /rate`); `RateController::getButton()`.
- **What happens:** `sort_order => required|numeric` accepts decimals and negatives (same as ST9), and the unused `getButton()` calls `route('rate.show', ['id' => $id])` while the resource parameter is `{rate}` (same as DR10).

---

# Part 24: `/currency_rate`

Tested as `qa.admin`, `qa.created` (no currency-rate permissions) and a guest. QA currency rates with currencies starting "QA" (ids 14-21) were created; only those were edited or deleted. `App\CurrencyRate` has no soft deletes. Table `currency_rates`: `currency varchar(191)`, `rate double(8,2)`, `date date`.

## CR1. Critical: users with no currency-rate permissions can edit any rate

- **URL:** `POST /currency_rate/{id}/update`. This is the **edit form's own action** (`resources/views/currency_rate/edit.blade.php`: `action='{!! url("currency_rate")!!}/{!!$currency_rate->id!!}/update'`), and the route is unnamed.

**Result as `qa.created` (no `currency_rate.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /currency_rate`, `/currency_rate/14/edit` | 403 (correct) |
| `POST /currency_rate/14/update` with `rate=999` | 302, **rate changed from 1.09 to 999** |

**Possible fix:** give the route `'permissions' => 'currency_rate.edit'`, or point the form at `PUT /currency_rate/{currency_rate}`.

---

## CR2. High: currency rates are not used anywhere in the app

- **URL:** `/currency_rate` (the whole module).
- **What happens:** searching `app/` for `CurrencyRate::` and `currency_rates` finds only:
  - `CurrencyRateController` itself;
  - `PermissionHelper` (permission names);
  - `Console/Commands/BaseInputSampleData.php` (sample data).

  No price, invoice, offer or report code reads these rates, so editing them has **no effect** on the app.
- **The data confirms it:** the latest real rates are from **2012-11-16** (HUF 284.18, LTL 3.45, GBP 0.77, BGN 1.96), plus test rows "bit coin" (2025-12-26) and "Bit coin" (2025-09-30).

**Possible fix:** decide whether conversions should use this table (and wire it into price/invoice code), or remove the page and menu item so users don't maintain unused data.

---

## CR3. High: rates rounded to 2 decimals and silently capped

- **URLs:** `/currency_rate/create` (`POST /currency_rate`), `/currency_rate/{id}/edit` (`POST /currency_rate/{id}/update`).
- **What happens:** the `rate` column is `double(8,2)`:

| Entered | Stored |
|---------|--------|
| `1.0825` | `1.08` |
| `1000000000` | `999999.99` (no error) |

  Exchange rates normally need 4-6 decimals, and the validation (`numeric`) doesn't warn about either case.

**Possible fix:** change the column to `decimal(18,6)` and add `'rate' => 'required|numeric|gt:0|max:999999'`.

---

## CR4. High: stored XSS on the currency-rate page

- **URL:** `/currency_rate/{id}` (`resources/views/currency_rate/show.blade.php`).
- **What happens:** the view prints `{!!$currency_rate->currency!!}`, `{!!$currency_rate->rate!!}` and `{!!$currency_rate->date!!}`. Rate 21 with currency `QA<img src=x onerror=alert('xss-ccy')>` rendered raw on `/currency_rate/21`. `/currency_rate/21/edit` and the list escape it.

**Possible fix:** use `{{ }}`, and validate `currency` (CR6).

---

## CR5. Medium: rate `0` and negative rates accepted

- **URL:** `/currency_rate/create`, which sends `POST /currency_rate`.
- **What happens:** `rate => required|numeric`, so `rate=0` (id 15) and `rate=-5` (id 16) were saved. A zero rate would cause a division by zero if rates were used for conversion (CR2).

**Possible fix:** `'rate' => 'required|numeric|gt:0'`.

---

## CR6. Medium: currency is free text

- **URL:** `/currency_rate/create`, which sends `POST /currency_rate`.
- **What happens:** `currency => required` only. "QA dollar ccy" was saved (id 19). Real data has both "bit coin" and "Bit coin". The app has a `currencies` table with ISO codes (AUD, BGN, BRL, CAD, CHF, CNY, CZK, DKK, EEK, EUR, …) that isn't used for validation.

**Possible fix:** a dropdown of `currencies`, validated with `exists:currencies,code`.

---

## CR7. Medium: duplicate currency + date allowed

- **URL:** `/currency_rate/create`, which sends `POST /currency_rate`.
- **What happens:** two QAUSD rates (1.08 and 2.00) were saved for the same date. If rates were used, which one applies would depend on database order.

**Possible fix:** `Rule::unique('currency_rates')->where('currency', …)->where('date', …)` (ignoring the current id on update).

---

## CR8. Medium: far-future dates accepted

- **URL:** `/currency_rate/create`, which sends `POST /currency_rate`.
- **What happens:** `date => required|date`, so a rate dated **2099-01-01** (id 20) was saved.

**Possible fix:** `before_or_equal:today` (or a sensible window).

---

## CR9. Low: delete button sends a GET, which returns 405

- **URL:** red delete button on `/currency_rate`.
- **What happens:** same as C2. `GET /currency_rate/14/delete` returns 405. A real `POST /currency_rate/{id}/delete` works and is permanent; there is no usage check, but nothing uses the rates (CR2).

---

# Part 25: `/currencies`

Tested as `qa.admin`, `qa.created` (no currency permissions) and a guest. QA currencies "QA Currency …" (ids 18-22) were created; only those were edited or deleted. QA package 556 and QA offer 103 were temporarily set to QA currency 18 and cleared afterwards. `App\Currencies` has no soft deletes.

**Where currencies are referenced:**

| Column | Rows | Stored as |
|--------|------|-----------|
| `tour_packages.currency` | 324 | currency **id** |
| `hotel_offers.currency` | 42 | currency **id** (`BookingRequestController@offersData` does `Currencies::find($offer->currency)->name ?? $offer->currency`) |
| `client_invoices.currency` | 25 | currency **code/text** |

## CU1. Critical: deleting a currency in use deletes it permanently with no check

- **URLs:** delete on `/currencies` (`POST|DELETE /currencies/{id}/delete`, `DELETE /currencies/{currency}`).
- **What happens:** `CurrenciesController@destroy` does `findOrFail()->delete()` with no usage check, on a table without soft deletes.
- **Evidence:**
  - QA package 556 and QA offer 103 were set to `currency = 18` (QA currency);
  - `POST /currencies/18/delete` returned 200, and the `currencies` row no longer exists;
  - both rows still had `currency = 18`, and `GET /booking/offers/556/1` then returned **`"currency":"18"`** (the bare id) instead of a name.
- With 324 packages and 42 offers referencing currencies, deleting a real currency (e.g. EUR) would leave hundreds of records showing ids instead of names.

**Possible fix:** block delete when `tour_packages`/`hotel_offers` (and invoices by code) still reference the currency; consider soft deletes.

---

## CU2. Critical: users with no currency permissions can edit any currency

- **URL:** `POST /currencies/{id}/update`. This is the **edit form's own action** (`resources/views/currencies/edit.blade.php`: `action='{!! url("currencies")!!}/{!!$currencies->id!!}/update'`), and the route is unnamed.

**Result as `qa.created` (no `currencies.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /currencies`, `/currencies/22/edit` | 403 (correct) |
| `POST /currencies/22/update` | 302, **currency renamed** to "QA Currency Edited Without Permission", and code changed to `QXX` |

- Changing a real code (e.g. `EUR`) would break anything that matches by code (`client_invoices.currency`).

**Possible fix:** give the route `'permissions' => 'currencies.edit'`, or point the form at `PUT /currencies/{currency}`.

---

## CU3. High: stored XSS on the currency page

- **URL:** `/currencies/{id}` (`resources/views/currencies/show.blade.php`).
- **What happens:** the view prints `{!!$currencies->name!!}`, `code`, `symbol`, `cent` and `symbol_cent` raw. Currency 22 (name `QA Currency <img src=x onerror=alert('xss-cur')>`, symbol `<script>alert('xss-sym')</script>`) rendered **2 raw** payloads on `/currencies/22`. The `/currencies` list and the edit page escape them.

**Possible fix:** use `{{ }}`.

---

## CU4. High: duplicate currency codes allowed

- **URL:** `/currencies/create`, which sends `POST /currencies`.
- **What happens:** `validateCurrencies()` only requires `name`, `code` and `symbol`. "QA Currency Dup EUR" (id 19) was saved with code **`EUR`**, so two currencies now had `EUR` (10 "Euro" and 19). Currency dropdowns show both, and anything that looks up by code gets an arbitrary one.

**Possible fix:** `'code' => 'required|size:3|unique:currencies,code,{id}'`.

---

## CU5. Medium: currency code not validated as an ISO code

- **URL:** `/currencies/create` (`code`), which sends `POST /currencies`.
- **What happens:** "qa money 123" was saved as a code (id 20).

**Possible fix:** `'code' => ['required', 'regex:/^[A-Z]{3}$/']`.

---

## CU6. Medium: `cent` / `symbol_cent` not validated

- **URL:** `/currencies/create`, which sends `POST /currencies`.
- **What happens:** neither field has a rule. `symbol_cent=abc` and a 300-character `cent` were saved (id 21). Existing data uses `symbol_cent` as a number ("100").

**Possible fix:** `cent => nullable|max:10`, `symbol_cent => nullable|integer|min:1`.

---

## CU7. Medium: delete button sends a GET, which returns 405

- **URL:** red delete button on `/currencies`.
- **What happens:** same as C2. `GET /currencies/22/delete` returns 405. **Fix CU1 first**, or the working button will permanently delete currencies in use.

---

## CU8. Low: create page loads every currency without using them

- **URL:** `GET /currencies/create`.
- **What happens:** `create()` passes `Currencies::all()` to the view, which doesn't need it.

---

## CU9. Low: unused `getButton()` has a copy-pasted parameter name

- **URL:** none (`CurrenciesController::getButton($id, $currency_rate)`).
- **What happens:** the second parameter is named `$currency_rate` (copied from `CurrencyRateController`). The route parameter itself (`currency`) is correct. Clean-up only.

---

# Part 26: `/tour_package` (found while testing `/currencies`)

## TP1. Critical: tour package edit page crashes for packages with an empty `pax_free`

- **URL:** `GET /tour_package/{id}/edit`, e.g. `/tour_package/266/edit` (real package) and `/tour_package/556/edit` (QA).
- **What happens:** the page returns **500** "Unsupported operand types: int + string" at `resources/views/tour_package/edit.blade.php:299`:

```blade
@if($peopleCount != $tourPackage->pax + $tourPackage->pax_free)
```

  but `TourPackage::getPaxFreeAttribute()` (`app/TourPackage.php:167`) returns a display string:

```php
return "+{$value}";
```

  When `pax_free` is NULL the accessor returns just `"+"`, which can't be added to an integer.
- **Scale:** **75 of 391** non-deleted tour packages have `pax_free = NULL` (and 66 have `pax = NULL`).

| Package | `pax` / `pax_free` | `/tour_package/{id}/edit` |
|---------|--------------------|---------------------------|
| 46 | 20 / 2 | 200 |
| 200 | 24 / 2 | 200 |
| **266** | 1 / NULL | **500** |
| 556 (QA) | 10 / NULL | **500** |

**Possible fix:** keep the raw value for maths (`$tourPackage->getRawOriginal('pax_free') ?? 0`, or `(int) $tourPackage->getAttributes()['pax_free']`) and move the `"+"` formatting into the view; default `pax`/`pax_free` to 0.

---

# Part 27: `/criteria`

Criteria are supplier features (e.g. Wi-Fi, parking) attached to hotels, events, restaurants, guides, transfers, flights and cruises through `services_has_criterias` (35 links in real data). Tested as `qa.admin`, `qa.created` (no criteria permissions) and a guest. QA criteria "QA Crit …" (ids 19-23) were created and attached to QA restaurant 3703. **`App\Criteria` has no soft deletes.** Criteria types: 1 hotel, 2 event, 3 restaurant, 4 guide, 5 transfer, 6 flight, 7 cruise.

## CT1. Critical: criterion names are printed raw on supplier pages

- **URLs:** created at `/criteria/create` (`POST /criteria`); executes on supplier pages such as **`/restaurant/{id}`**.
- **What happens:** these views print the attached criteria with `{!!$criteria->name!!}`:

| View | Page |
|------|------|
| `resources/views/restaurant/show.blade.php` (line 147) | `/restaurant/{id}` |
| `resources/views/cruises/show.blade.php` (line 112) | `/cruises/{id}` |
| `resources/views/flight/show.blade.php` (line 115) | `/flight/{id}` |
| `resources/views/reporting/event_show.blade.php` | event report |
| `resources/views/reporting/guide_show.blade.php` | guide report |
| `resources/views/reporting/hotel_show.blade.php` | hotel report |
| `resources/views/reporting/restaurant_show.blade.php` | restaurant report |

- **Evidence:** criterion 23 "QA Crit `<img src=x onerror=alert('xss-crit')>`" (restaurant type) was attached to QA restaurant 3703. `/restaurant/3703` rendered the payload **raw**; `/restaurant/3703/edit` and `/restaurant/create` escape it.
- **Impact:** whoever can create or edit a criterion (including anyone, via CT3) can run script on supplier pages that every user opens.

**Possible fix:** use `{{ $criteria->name }}` in all 7 views, and validate criterion names.

---

## CT2. Critical: deleting a criterion in use deletes it permanently

- **URLs:** delete on `/criteria` (`POST|DELETE /criteria/{id}/delete`, `DELETE /criteria/{criterion}`).
- **What happens:** `CriteriaController@destroy` calls `DeleteModel::check($criteria, 'Criteria')`, which counts `services_has_criterias` rows and, when the criterion **is** used, calls `$model->delete()` (RS1). With no soft deletes, this is permanent.
- **Evidence:** criterion 19 was attached to QA restaurant 3703. `POST /criteria/19/delete` returned 200, the criterion is **gone**, and 1 `services_has_criterias` row still points at it. (`/restaurant/3703` still opens.)

**Possible fix:** fix `DeleteModel::check()` (RS1), and remove or block links when deleting.

---

## CT3. Critical: users with no criteria permissions can edit any criterion

- **URL:** `POST /criteria/{id}/update`. This is the **edit form's own action** (`resources/views/criteria/edit.blade.php`: `action='{!! url("criteria")!!}/{!!$criteria->id!!}/update'`), and the route is unnamed.

**Result as `qa.created` (no `criteria.*` permissions):**

| Request | Result |
|---------|--------|
| `GET /criteria`, `/criteria/23/edit` | 403 (correct) |
| `POST /criteria/22/update` | 302, **criterion renamed** to "QA Crit Edited Without Permission" (icon cleared) |

**Possible fix:** give the route `'permissions' => 'criteria.edit'`, or point the form at `PUT /criteria/{criterion}`.

---

## CT4. High: stored XSS on the criteria page

- **URL:** `/criteria/{id}` (`resources/views/criteria/show.blade.php`).
- **What happens:** the view prints `{!!$criteria->name!!}`, `{!!$criteria->short_name!!}` and `{!!$criteria->icon!!}`. Criterion 23 rendered the payload raw on `/criteria/23`. `/criteria/23/edit` and the `/criteria` list escape it.

**Possible fix:** use `{{ }}`.

---

## CT5. High: criteria type not validated

- **URL:** `/criteria/create` (`criteria_type`), which sends `POST /criteria`.
- **What happens:** `criteria_type => required` only. `criteria_type=999999` was saved (criterion 20). Supplier forms load criteria with `criteria_types.service_type = '<supplier>'`, so a criterion with an unknown type never appears anywhere.

**Possible fix:** `'criteria_type' => 'required|exists:criteria_types,id'`.

---

## CT6. High: the type of a criterion in use can be changed

- **URL:** `/criteria/{id}/edit`, which sends `POST /criteria/{id}/update`.
- **What happens:** criterion 19 (restaurant type) was attached to restaurant 3703; changing its `criteria_type` to 1 (hotel) was accepted. The criterion then disappears from restaurant forms while still linked to the restaurant (and would appear on hotel forms).

**Possible fix:** block type changes when `services_has_criterias` rows exist.

---

## CT7. Medium: duplicate criteria allowed

- **URL:** `/criteria/create`, which sends `POST /criteria`.
- **What happens:** two criteria "QA Crit Valid" of type 3 were both saved, so supplier forms show the same option twice.

**Possible fix:** `Rule::unique('criterias', 'name')->where('criteria_type', $request->criteria_type)`.

---

## CT8. Medium: criteria list has no pagination

- **URL:** `GET /criteria`.
- **What happens:** `index()` loads every criterion (`->get()`) and renders `component.icon_for_criteria` once per row. Fine at 14 rows (0.63 s), but it grows linearly.

**Possible fix:** `->paginate(25)` like the other lists.

---

## CT9. Medium: delete button sends a GET, which returns 405

- **URL:** red delete button on `/criteria`.
- **What happens:** same as C2. `GET /criteria/23/delete` returns 405. **Fix CT2 first**, or the working button will permanently delete criteria in use.

---

# Part 28: `/email`

The email area has two parts: a built-in IMAP client (`EmailController`, `/email/...`) and SnappyMail webmail with single sign-on (`SnappyMailController`, `/email/webmail`, `/email/configure`). Tested as `qa.admin` and `qa.created` (no email permissions). **To avoid touching real mail, no email was sent and no real mailbox was logged into.** The only mailbox configured was a fake QA address on `qa.admin` (never connected, and cleared afterwards). Credentials were checked from the database **without printing any values**.

**Route protection:** every `/email/*` web route uses only `auth` (no `permissions.required`). The `/api/v1/users/{userId}/email*` routes use `api.permissions` (`email.main`) and reject a mismatched `userId`.

## EM1. Critical: the built-in email client can't work (IMAP library missing)

- **URLs:** every mailbox page of the built-in client, e.g. `GET /email/remove/{id}/{folder}`, `GET /email/attachmentList/{folder}/{id}`, `GET /email/another`, `GET /email/mail/{id}/{folder}`; also the console command `ParseEmails`.
- **What happens:** `app/Http/Controllers/EmailController.php` (lines 17-27) and `app/Console/Commands/ParseEmails.php` (lines 12-15) `use Ddeboer\Imap\…`, but `ddeboer/imap` is **not in `composer.json`** and **not in `vendor/`**. The log shows `Class "Ddeboer\Imap\Server" not found`, and those pages return **500** for every user. (The PHP `imap` extension is loaded, but the library isn't.) Only SnappyMail webmail works.

**Possible fix:** `composer require ddeboer/imap` (with a version matching the code), or remove the built-in client and point the menu to webmail.

---

## EM2. Critical: four users' mailbox passwords are stored in plain text

- **URL:** mailbox fields on the user edit form (`/users/{id}/edit`, `POST /users/{id}`), stored in `users.email_password`.
- **What happens:** of the 8 users with a mailbox configured:

| User ids | `email_password` | `email_password_encrypted` |
|----------|------------------|----------------------------|
| 1 | encrypted | 1 (correct) |
| 5, 20, 74 | encrypted | **0** (see EM4) |
| **23, 34, 65, 66** | **plain text** (lengths 16, 16, 10, 8) | 0 |

- Anyone with database or backup access can read these real mailbox passwords. (`email_password` is hidden from JSON via `$hidden`, but stored in plain text.)

**Possible fix:** encrypt the 4 values in a one-off migration (and set the flag); ask those users to change their mailbox passwords.

---

## EM3. High: `email.main` permission is not enforced on any `/email` page

- **URLs:** `/email`, `/email/configure`, `/email/addFolderForm`, `/email/parseEmails` and every other `/email/*` route.
- **What happens:** a permission `email.main` exists (and the API checks it), but `EmailController` and `SnappyMailController` only use `auth`. `qa.created` (no `email.main`) got **200** on `/email`, `/email/configure`, `/email/addFolderForm` and `/email/parseEmails`.

**Possible fix:** add `permissions.required` with `email.main` to the `/email` route group.

---

## EM4. High: three users can't log into webmail

- **URL:** `GET /email/webmail` (`SnappyMailController@sso`).
- **What happens:** `getEmailPassword()` only decrypts when `email_password_encrypted` is true. `UserController@update` saves `Crypt::encryptString($request->email_password)` but **never sets `email_password_encrypted = true`** (`SnappyMailController@saveConfiguration` does). Users **5, 20 and 74** therefore have an encrypted password with the flag off, so SSO sends the encrypted string as their password and webmail login fails.

**Possible fix:** set `email_password_encrypted = true` in `UserController@update`, and fix the 3 rows.

---

## EM5. High: webmail password falls back to the server-wide `IMAP_PASSWORD`

- **URL:** `GET /email/webmail`.
- **What happens:** `SnappyMailController::getEmailPassword()` ends with:

```php
return $user->email_password ?? env('IMAP_PASSWORD', '');
```

  So a user who has an `email_login` but no `email_password` is signed in with the **server's own IMAP password**. It is empty locally (`IMAP_USERNAME`/`IMAP_PASSWORD` not set), but on a server where they are set, any user could set their own `email_login` to the shared mailbox (e.g. through U1/U2) and open it.

**Possible fix:** remove the `env('IMAP_PASSWORD')` fallback and require a per-user password.

---

## EM6. High: emails can be deleted with a plain GET link

- **URL:** `GET /email/remove/{id}/{currentFolder?}` (`EmailController@removeEmail`).
- **What happens:** the action moves a message to Trash, but it is a GET request with no CSRF protection. Any link or `<img src>` on a page a logged-in user visits could delete their emails. Verified from the code, since IMAP can't run here (EM1).

**Possible fix:** make it `POST`/`DELETE` with `@csrf`.

---

## EM7. Medium: three email pages crash on undefined view variables

- **URLs and errors (from `storage/logs`):**

| Request | Error |
|---------|-------|
| `GET /email/getComposeForm` | `compact(): Undefined variable $mail` |
| `GET /email/getMoveToForm` | `foreach() argument must be of type array|object, null given` (`email/move_to_form.blade.php`) |
| `GET /email/template` | `Undefined variable $content` (`email/mail_template.blade.php`) |

  All three return **500** (for users with or without a mailbox).

**Possible fix:** pass default values (`$mail = null`, `$folders = []`, `$content = ''`) or guard the views.

---

## EM8. Medium: pages needing a mailbox redirect without explaining why

- **URLs:** `GET /email/folder/INBOX` and `GET /email/search` redirect to `/email`; `GET /email/readAll` redirects to `/home`, with no message, for users without a mailbox.

**Possible fix:** show "Please configure your mailbox" with a link to `/email/configure`.

---

## EM9. Low: webmail status endpoint open to any user

- **URL:** `GET /email/webmail/status`.
- **What happens:** returns `{"installed":true,"url":"/mail","sso_configured":true}` to any logged-in user. It isn't sensitive, but it is unrestricted (see EM3).

---

# Part 28b: `/email/webmail` (SnappyMail)

**Source URL:** these issues come from **`http://127.0.0.1:8010/email/webmail`** and the pages it leads to (`/email/webmail/direct`, `/email/webmail/admin`, `/email/webmail/status`, `/email/configure` and SnappyMail itself under `/snappymail/`). The URL as first requested, **`/email/webemail`**, does not exist and returns **404** for every user (`email/{page?}` only matches numbers), so it was taken to mean `/email/webmail`.

Tested as `qa.admin` (admin, no mailbox), `qa.created` (no email permissions) and a guest. **No email was sent and no real mailbox was logged into.** For EM14 a fake mailbox (`qa.mailbox@eets.test`) was saved on `qa.created` through `/email/configure` and **cleared afterwards**. Secret values (SSO key, admin password, hashes) were checked for presence and format only, **never printed**.

## EM10. Critical: SnappyMail config and data files download with no login

- **Found from:** `/email/webmail` → SnappyMail at `/snappymail/`.
- **URLs (all 200, as a guest):**

| Request | Bytes returned | Contains |
|---------|----------------|----------|
| `GET /snappymail/data/_data_/_default_/configs/application.ini` | 9452 (whole file) | admin login `admin`, admin password **bcrypt hash**, TOTP field, every SnappyMail setting |
| `GET /snappymail/data/_data_/_default_/domains/helpjoo.com.json` (also `default.json`, `dev.json`, `gmail.com.json`, …) | 2593 | IMAP/SMTP host and port settings |
| `GET /snappymail/data/_data_/_default_/cache/00/4a/004ac9…` | 10 | SnappyMail cache entries (SSO tokens are cached here) |

- **Why:** SnappyMail's `data/` folder sits inside the public web root and is protected only by `.htaccess` (`Require all denied`). `php artisan serve` ignores `.htaccess`, and so does **nginx**, which the deploy workflow reloads (`.github/workflows/deploy.yml:205`). Unless the production nginx config denies `/snappymail/data/`, production is exposed the same way. (`SALT.php` returns 0 bytes because it runs as PHP.)
- **Also in git:** `public/snappymail/data/` is committed (`application.ini`, `SALT.php`, domain files, and 6 cache files, even though `.gitignore` now ignores the cache folder). Everyone with repo access has the admin hash and salt.
- **Impact:** the admin hash can be cracked offline. Combined with EM11, that gives full control of SnappyMail (domains, IMAP/SMTP servers, plugins).

**Possible fix:** move SnappyMail's data folder outside `public/` (SnappyMail supports a custom data path through `include.php`), or add `location ^~ /snappymail/data { deny all; }` to nginx. Remove `public/snappymail/data/` from git (`git rm --cached`), add it to `.gitignore`, and change the SnappyMail admin password.

---

## EM11. High: the SnappyMail admin panel is open to anyone

- **Found from:** `/email/webmail/admin`.
- **What happens:** `SnappyMailController@admin` only lets the `admin` role through: `qa.created` is sent back to `/home`. But it just redirects to SnappyMail's own admin page, which is reachable directly: `GET /snappymail/?admin` returns the admin login page (**200**) to `qa.created` **and to a guest**. `application.ini` has `allow_admin_panel = On` and the predictable login `admin`.
- **Impact:** the Laravel check gives no protection. Anyone on the internet can try passwords against the panel, or crack the leaked hash (EM10).

**Possible fix:** set `allow_admin_panel = Off` and turn it on only when needed, or limit `/snappymail/?admin` by IP or behind the Laravel `auth` + admin check in nginx. Also change `admin_login` from `admin`.

---

## EM12. High: endless redirect loop for users without a mailbox

- **URL:** `GET /email/webmail` (`SnappyMailController@sso`).
- **What happens:** with no `email_login`, the controller does `redirect()->back()`. Laravel's "back" is the `Referer` header or, without one, the previous GET URL in the session, which is `/email/webmail` itself. Opening the URL directly (bookmark, typed URL, reload, or a new tab without a referrer) loops:

```
GET /email/webmail -> 302 Location: /email/webmail
GET /email/webmail -> 302 Location: /email/webmail
… curl stopped after 10 redirects (browser: ERR_TOO_MANY_REDIRECTS)
```

  Reproduced as `qa.admin`. With a `Referer` (clicking the menu) it goes back to that page instead.
- The same `redirect()->back()` is used in the `catch` block, so an SSO failure loops the same way.

**Possible fix:** redirect to `route('snappymail.configure')` (or `/home`) explicitly instead of `back()`.

---

## EM13. Medium: "Direct" and "Admin" webmail links go to a 404

- **URLs:** `GET /email/webmail/direct` → `302 /mail` → **404**; `GET /email/webmail/admin` (as admin) → `302 /mail?admin` → **404**; `GET /email/webmail/status` reports `"url":"/mail"`.
- **Why:** `.env` has `SNAPPYMAIL_URL=/mail`, but SnappyMail lives in `public/snappymail` and nothing maps `/mail` to it here (only `snappymail-apache.conf` does, as an Apache `Alias`, while the server runs nginx). `sso()` has a fallback (`webmailUrl()`, which switches to `/snappymail`), but `direct()`, `admin()` and `status()` read `config('snappymail.url')` directly. Also, `admin_url` `/mail/?admin` loses its slash and becomes `/mail?admin`.
- `sso()` also always loads `public_path('snappymail/index.php')`, whatever `SNAPPYMAIL_URL` says.

**Possible fix:** use `webmailUrl()` in `direct()`, `admin()` and `status()` (and build the admin URL from it), or set `SNAPPYMAIL_URL=/snappymail`.

---

## EM14. Medium: mailbox credentials aren't checked, and the "saved" message is lost

- **URLs:** `POST /email/configure` → `302 /email/webmail` → `302 /snappymail/?Sso&hash=…`.
- **What happens:**
  - Any address and any 6+ character password is saved; nothing tests an IMAP login first. A wrong password is only discovered on the SnappyMail login error page, with nothing pointing back to `/email/configure`.
  - The controller sets `->with('success', 'Email configuration saved successfully')`, but the next stop is outside Laravel (SnappyMail), so the toast never appears.
  - The webmail menu opens in a new tab (`target="_blank"`), but saving the configuration takes over the current tab.

**Possible fix:** try an IMAP connection (`imap_open` with `OP_HALFOPEN`, the extension is loaded) before saving and return a validation error on failure. Redirect back to `/email/configure` with the success toast and an "Open webmail" button.

---

## EM15. Medium: "not configured" error doesn't help the user

- **URL:** `GET /email/webmail` with no mailbox set.
- **What happens:** the toast says "Email credentials not configured. Please contact administrator." But users can configure it themselves at `/email/configure`, and the message has no link. Because the menu link opens a **new tab**, the user ends up with a second copy of the page they were on, showing that toast (when it doesn't loop, EM12).

**Possible fix:** redirect to `/email/configure` with an info toast ("Set up your mailbox to use webmail"), or hide or disable the menu item until a mailbox is set.

---

## EM16. Low: the configure page doesn't follow the UI rules

- **URL:** `GET /email/configure` (`resources/views/snappymail/configure.blade.php`).
- **What happens:**
  - It uses Bootstrap 3 / AdminLTE markup (`col-md-offset-2`, `box box-primary`, `box-footer`, `btn-default`, `fa fa-*` icons) inside the Tabler / Bootstrap 5 layout, so the offset, card and Cancel button are unstyled.
  - Save comes first and Cancel is on its right. The rule is Back on the left and Save at the end.
  - After a validation error, the email field shows the saved value (`$user->email_login`), not what was typed (`old('email_login')`). Errors do show as a toast.
  - The IMAP server/port/encryption box is read-only information, but it looks like part of the form.

**Possible fix:** rebuild with Tabler `card`, `btn-secondary`/`btn-primary` and `ti` icons; Back on the left, Save at the end; `old('email_login', $user->email_login)`.

---

## EM17. Low: webmail config flags ignored, dead code, mailbox address in logs

- **Found from:** `/email/webmail` (`app/Http/Controllers/SnappyMailController.php`, `config/snappymail.php`).
- **What happens:**
  - `features.sso_enabled`, `direct_login`, `admin_access`, `user_config`, `security.require_https`, `session_timeout`, `max_attachment_size` and `logging.*` are set in `.env` but **never read**. Setting `SNAPPYMAIL_ADMIN_ACCESS=false` doesn't disable `/email/webmail/admin`.
  - `generateSSOHash()` is unused (left over from the old SSO) and still builds an unencrypted password payload.
  - Every SSO writes the user's mailbox address to `laravel.log` (`SnappyMail SSO attempt {"user_id":…,"email":…}`).

**Possible fix:** honour the flags or remove them, delete `generateSSOHash()`, and log only `user_id`.

---

## EM18. Low: mail "View all" link is broken

- **Found from:** `/email` (header mail dropdown in `resources/views/scaffold-interface/layouts/modernapp.blade.php:110`, used by `email/index.blade.php`).
- **What happens:** `<a href="snappymail.eu">` is a relative link, so it opens `/email/snappymail.eu` (**404**), not webmail.

**Possible fix:** `href="{{ route('snappymail.sso') }}"`.

---

# Part 29: `/comment`

Comments are the discussion threads shown on tours, tasks, suppliers, statuses, clients, invoices and more (`comments.reference_type` per `Comment::$services`: 1 tour, 2 task, …, 17 client, 22 client_invoices, 23 invoices). The thread HTML is loaded by `POST /comment/generate-comments`. Tested as `qa.admin`, `qa.agent` (role `user`, which has all `comment.*`) and `qa.created` (no comment permissions). QA comments ("QA Comment …") were added to QA tour 136 (`reference_type=1`). To check raw rendering, a harmless `<b>QA-bold-marker</b>` was used instead of a script.

## CO1. Critical: comment HTML is rendered raw in every comment thread

- **URLs:** any record's comment section (e.g. the tour page for tour 136), loaded via `POST /comment/generate-comments`; comments are created with `POST /comment`.
- **What happens:** `resources/views/comments/show_tree_view_all.blade.php:15` prints `{!! $comment->content !!}`, and `comments/childs.blade.php:23` prints `{!! $child->content !!}`. The comment `QA Comment <b>QA-bold-marker</b>` came back from `generate-comments` with the `<b>` tag **intact (not escaped)**.
- **Impact:** any user who can comment can put markup, including script, into a thread that every user viewing that tour, task, supplier, client or invoice will render.

**Possible fix:** escape (`{{ }}`), or, if rich text is wanted, sanitize with an allow-list (e.g. HTML Purifier) on save and output.

---

## CO2. Critical: any regular user can edit or delete anyone's comments

- **URLs:** `PUT /comment/{id}` (edit) and `POST|DELETE /comment/{id}/delete` (delete), used from any record's comment section.
- **What happens:** `CommentController@update` and `@destroy` don't check who wrote the comment, and the `user` role has `comment.edit` and `comment.destroy`. `update()` also saves `$request->except('attach','attach_bus')`, so `author_id` can be changed.

**Evidence as `qa.agent` (role `user`) on an admin's comment:**

| Request | Result |
|---------|--------|
| `PUT /comment/136` with `content=… (edited by agent)` and `author_id=180` | 200, **text replaced and author changed to the agent** |
| `POST /comment/136/delete` | 200, **the comment, its reply and the reply's reply were all deleted** |

  (`qa.created`, without `comment.edit`, correctly got 403 on the `PUT`.)

**Possible fix:** only the author (or an admin) may edit or delete; never accept `author_id` from the request.

---

## CO3. Critical: users with no comment permissions can read any record's thread

- **URL:** `POST /comment/generate-comments` (unnamed route, `routes/web.php:486`).
- **What happens:** `getComments()` returns the full rendered thread for any `reference_type` + `reference_id`, with no permission check and no check that the user may see that record.
- **Evidence as `qa.created` (no `comment.*` permissions):**
  - `reference_type=1, reference_id=136` returned the QA tour's thread (200);
  - `reference_type=17, reference_id=21` returned a **real client's** comment thread (200, 1,210 bytes).
- Comments on clients and invoices (types 17, 22, 23) can be read by anyone logged in.

**Possible fix:** require `comment.show` **and** the permission for the referenced module (e.g. `clients.show`) before returning a thread.

---

## CO4. High: mass assignment on comments

- **URLs:** `POST /comment`, `PUT /comment/{id}`.
- **What happens:** `App\Comment` has `$guarded = []`. `store()` saves `$request->except(['_token','attach','attach_bus','id_comment'])` (after forcing `author_id`), and `update()` saves `$request->except('attach','attach_bus')`.
- **Evidence:** `POST /comment` with `id=987990`, `status=9`, `created_at=2001-01-01 00:00:00`, `author_id=1` saved **id 987990, status 9, created_at 2001-01-01**. `author_id` was correctly forced to 179 on create, but **can be changed on update** (CO2).

**Possible fix:** `$request->only(['content', 'reference_type', 'reference_id', 'parent'])` with validation; set `$fillable`.

---

## CO5. High: comment on a non-existent record is saved, then returns 500

- **URLs:** `POST /comment`, then `GET /comment/{id}`.
- **What happens:** nothing validates `reference_type`/`reference_id`. `POST /comment` with `reference_type=999`, `reference_id=999999` returned **500**, but the comment **was saved** (id 987991). Opening it (`GET /comment/987991`) also returns **500**, because `show()` calls `$comment->service()` for an unknown type.

**Possible fix:** validate `reference_type` against `Comment::$services` and that the referenced record exists, before saving.

---

## CO6. Medium: deleting a missing comment reports success

- **URL:** `POST /comment/{id}/delete`.
- **What happens:** `destroy()` uses `where('id', $id)->delete()` without `findOrFail`. `POST /comment/999999/delete` returns 200 and flashes "Comment #999999 deleted".

---

## CO7. Medium: comment deletes are permanent, including every reply

- **URL:** `POST /comment/{id}/delete`.
- **What happens:** `destroy()` and `deleteComments()` hard-delete the comment and recursively all replies (no soft deletes). With CO2, any regular user can wipe a whole discussion that can't be restored.

---

## CO8. Medium: comment attachments have no file-type rule

- **URL:** `POST /comment` (`attach[]`, `attach_bus[]`).
- **What happens:** `store()`/`update()` call `addFile()` with no validation on the files (same as E4). Verified from the code; no file was uploaded in this run.

---

## CO9. Low: comment helper routes are unnamed

- **URLs:** `GET /comment/{id}/delete_msg`, `POST /comment/generate-comments`.
- **What happens:** both are unnamed, so `permissions.required` can't check them (CO3).

---

# Part 30: `/clients`

Tested as `qa.admin` and `qa.created` (no client permissions). QA clients "QA Client …" (ids 25, 987995-987997) were created; QA tour 136 was linked to client 25 and unlinked afterwards. `App\Client` uses soft deletes and hides `password` from JSON. `ClientController@update` already saves only a whitelist of fields and has thorough validation (`UpdateClientRequest`). The client portal (`/TMS-Client/*`, `/client_tour_package`) is protected by `clientauth`: guests are redirected to `/TMS-Client/login`.

## CL1. Critical: users with no client permissions can change client details and the portal password

- **URL:** `POST /clients/{id}/update` (unnamed route; the edit form uses the named `PUT /clients/{client}`).
- **What happens:** the route has no name, so `permissions.required` checks nothing. `qa.created` (no `clients.*` permissions) updated client 25's name and its client-portal password through it.

**Possible fix:** give the route `'permissions' => 'clients.edit'`, or remove it.

---

## CL2. Critical: any logged-in user can read any client's contacts

- **URL:** `GET /api/getClientContacts?clientId={id}&itemCount=0` (`ClientController@getClientContacts`, unnamed, `routes/web.php:84`).
- **What happens:** no permission or ownership check. `qa.created` received a real client's contact list (names, phones, emails; 3,551 bytes) with 200.

**Possible fix:** require `clients.show` (or `clients.edit`) and return 404 for unknown clients.

---

## CL3. High: stored XSS on the client page

- **URL:** `/clients/{id}` (`resources/views/clients/show.blade.php`).
- **What happens:** the view prints `name`, `address`, `work_phone`, `contact_phone`, `work_email`, `contact_email`, `work_fax` and contact fields with `{!! !!}`. `address` is only length-checked, so a harmless `<b>QA-bold-marker</b>` in the address rendered as HTML on `/clients/987996`; `/clients/987996/edit` escapes it.

**Possible fix:** use `{{ }}` for all fields.

---

## CL4. High: mass assignment on client create

- **URL:** `/clients/create`, which sends `POST /clients`.
- **What happens:** `store()` saves `$request->except(['attach','password','files'])` on a model with `$guarded = []`. Posting `id=987995`, `created_at=2001-01-01 00:00:00` and `dep_date=2001-01-01` saved all three. (`update()` is safe: it uses `$request->only([...])`.)

**Possible fix:** use the same `only([...])` whitelist in `store()`.

---

## CL5. High: attachments on client create accept any file type

- **URL:** `/clients/create` (`attach[]`), which sends `POST /clients`.
- **What happens:** `StoreClientRequest` validates `files.*`, but `addFile()` stores `attach[]`. An SVG was saved on client 987997 (`image/svg+xml`). `UpdateClientRequest` already validates `attach.*`.

**Possible fix:** add `'attach.*' => ValidationService::fileRules(false, 5120, 'jpeg,jpg,png,gif,pdf,doc,docx')` to `StoreClientRequest`.

---

## CL6. High: clients in use can be deleted without warning

- **URL:** delete on `/clients` (`POST /clients/{id}/delete`).
- **What happens:** `destroy()` has no usage check. Client 25, linked to QA tour 136, was soft-deleted; the tour kept `client_id = 25`. In real data 1 tour and 25 client invoices reference clients.

**Possible fix:** block (or warn) when tours/invoices reference the client.

---

## CL7. Medium: delete permissions and button

- **URLs:** `DELETE /clients/{client}` (resource), `POST|DELETE /clients/{id}/delete` (legacy), delete button on `/clients`.
- **What happens:**
  - the resource route needs `clients.destroy`, which **doesn't exist**, so the admin got 403;
  - the legacy route uses `client.destroy` (exists, singular);
  - the list button sends a GET to `/clients/{id}/delete`, which returns **405** (C2).

**Possible fix:** use one permission name (`clients.destroy`) and send POST/DELETE.

---

## CL8. Medium: client-portal passwords

- **URLs:** `/clients/create`, `/clients/{id}/edit` (field `password`); stored in `clients.password`.
- **What happens:** 21 of 23 client-portal passwords are encrypted with `Crypt` (reversible); **2 are not encrypted**. New passwords are stored encrypted (client 25: yes).

**Possible fix:** encrypt the 2 remaining values; longer-term, store a one-way hash (`Hash::make`) and verify with `Hash::check` in the TMS-Client login.

---

## CL9. Medium: a valid accented company name was rejected on create

- **URL:** `/clients/create`, which sends `POST /clients`.
- **What happens:** "QA Client Müller & Söhne" was rejected and the page redirected back without a visible reason in this test. The `name` pattern allows letters with accents and `&`, so another rule (or the reused `work_email` in that run) may be the cause. Please retry manually and make sure the form shows the validation error.

---

# Part 31: `/accounting`

`/accounting` manages client invoices (`ClientInvoiceController`, table `client_invoices`, with `invoice_items` and client payments in `transactions` where `pay_to = 'Client'`). Tested as `qa.admin`, `qa.agent` (role `user`) and `qa.created` (no accounting permissions). QA invoices have notes starting "QA Invoice" and use QA tour 136 and QA client 987995. **This module is the best-built one tested:** strict validation, row locking during save and payment, an overpayment guard, and a block on deleting invoices that have payments (see passed).

Route permissions are mapped by `PermissionsRequiredMiddleware` to the `transactions.*` permissions. Admin, user and Accountant hold `transactions.create/show/edit/destroy`. Note that the ordinary `user` role has full create/edit/delete rights on client invoices and payments; please confirm this is intended.

## AC1. High: client invoices are deleted permanently

- **URL:** delete on `/accounting` (`POST|DELETE /accounting/{id}/delete`).
- **What happens:** `App\ClientInvoices` has no `SoftDeletes`. `destroy()` hard-deletes the invoice and all its `invoice_items`. It is only allowed when the invoice has no payments (good), but the financial record still disappears with no audit trail. Verified: an invoice without payments ("QA Invoice nopay") no longer existed after delete.

**Possible fix:** add soft deletes to `client_invoices` and `invoice_items` (or keep a cancelled status instead of deleting).

---

## AC2. Medium: "service transaction" creation is 403 for everyone

- **URL:** `GET /accountingServiceTransaction/create/{tour_id}` (`ClientInvoiceController@serviceTransactionCreate`, route `service_transaction_create`).
- **What happens:** the route requires `service_transaction_create`, which **does not exist** in `permissions`. Admin, user and `qa.created` all got **403** for `/accountingServiceTransaction/create/136`.

**Possible fix:** map the route to `transactions.create`, or create and assign the permission.

---

## AC3. Medium: delete button sends a GET, which returns 405

- **URL:** red delete button on `/accounting` (`data-delete-url=".../accounting/{id}/delete"`, `data-delete-method="POST"`).
- **What happens:** same as C2. The button navigates with GET, and the route accepts only POST/DELETE. A real `POST /accounting/{id}/delete` works (see passed).

---

## AC4. Medium: invoice list has no pagination and many queries

- **URL:** `GET /accounting`.
- **What happens:** `index()` loads every invoice (`->get()`), then for each one runs `Tour::find`, `Client::find` and a `Transaction` sum: **85 queries for 26 invoices**, with no pagination.

**Possible fix:** eager-load `tour`/`client`, use `withSum('transactions as paid', 'amount')`, and paginate.

---

## AC5. Low: quotation lookup has no permission check

- **URL:** `GET /accounting/api/getTourquotation/{tour_id}` (unnamed).
- **What happens:** `qa.created` (no accounting permissions) got 200. The endpoint returns only the id of the tour's confirmed quotation (or an empty response), so the exposure is small.

**Possible fix:** name the route with `transactions.create`.

---

# Part 32: `/invoices`

`/invoices` manages **supplier** invoices (`InvoicesController`, table `supplier_invoices`, 58 rows). Invoices are linked to tours and tour packages through `invoices_tours` (117 rows), and supplier payments are stored in `transactions` with `pay_to = 'Supplier'`. Tested as `qa.admin` and `qa.created` (no invoice permissions). QA invoice numbers start with "QA-SINV" and use QA tour 136. Like `/accounting`, the core safety checks are solid (see passed).

## IN1. High: the extra amount can never be paid

- **URLs:** `/invoices/create` (`POST /invoices`) and the payment form `/add_payment/{id}` (`POST /payment_store/{id}`).
- **What happens:** `store()`, `update()` and `payment_store()` call `InvoicePayments::ensureWithinBalance($rows, $invoice->total_amount, $paid)`, which ignores `extra_amount`.
- **Evidence:** invoice 63 (`total_amount = 100`, `extra_amount = 20`) had 50 paid. Paying the remaining 70 (50 + 70 = 120) was refused with "Payments cannot exceed the invoice balance."; the paid total stayed 50.00.

**Possible fix:** decide whether `extra_amount` is payable. If so, check against `total_amount + extra_amount` (and show that balance in the UI).

---

## IN2. High: duplicate supplier invoice numbers allowed

- **URL:** `/invoices/create`, which sends `POST /invoices`.
- **What happens:** `invoice_no` is `required|string|max:191` with no uniqueness. A second invoice "QA-SINV-1" was saved, so 2 invoices share that number. The same supplier invoice can be entered (and paid) twice.

**Possible fix:** `Rule::unique('supplier_invoices', 'invoice_no')` (optionally scoped per office/supplier).

---

## IN3. High: an invoice can be linked to another tour's services

- **URL:** `/invoices/create` (fields `tour_id[]` and `package_id{tour_id}[]`), which sends `POST /invoices`.
- **What happens:** `validateInvoice()` checks `tour_id.*` exists, but `package_id…` values are not validated at all. An invoice for tour 136 was saved linked to **package 556, which belongs to tour 133** (`invoices_tours.package_id = 556`).

**Possible fix:** validate each package id with `exists:tour_packages,id` **and** that it belongs to the given tour.

---

## IN4. High: package dropdown has no permission check and outputs names unescaped

- **URL:** `GET /supplierdropdown/{tour_id}` (`InvoicesController@supplierdropdown`, unnamed), used by the invoice form to load a tour's services.
- **What happens:**
  - no permission is checked: `qa.created` (no invoice permissions) got 200 for `/supplierdropdown/136`;
  - the `<select>` HTML is built by string concatenation with `$tour->name` and `$package->name` **unescaped**; QA package 562's name with markup came back raw in `/supplierdropdown/133`;
  - an unknown tour (`/supplierdropdown/999999`) gives **500** (`$tour->id` on null).

**Possible fix:** a named route with `invoices.create`; build the options in a Blade view (escaped) or wrap names in `e()`; 404 when the tour is missing.

---

## IN5. Medium: due and received dates can't be entered

- **URL:** `/invoices/create`, which sends `POST /invoices`.
- **What happens:** `store()` always sets `receivedDate = today` and `dueDate = today + 7 days` (invoice 63: received 2026-10-28, due 2026-11-04), and `update()` doesn't touch them. Real supplier due dates can't be recorded.

**Possible fix:** add `received_date`/`due_date` fields (defaulting to these values) and validate `due_date >= received_date`.

---

## IN6. Medium: invoice list is slow

- **URL:** `GET /invoices`.
- **What happens:** 1.45 s and **555 queries for 117 invoice-tour rows**, with no pagination (`index()` builds every row with per-row lookups).

**Possible fix:** eager-load invoices/tours/offices, aggregate payments in one query, paginate.

---

## IN7. Medium: show page crashes for a link to a missing invoice

- **URL:** `GET /invoices/{id}` (link id).
- **What happens:** `show()` does `Invoices::find(...)`, then `Offices::find($invoices->office_id)` **before** `if ($invoices == null) abort(404)`. A temporary `invoices_tours` row pointing at a missing invoice gave **500** on `/invoices/126` (the test row was removed afterwards). `/invoices/999999` (missing link) correctly returns 404.

**Possible fix:** check for null right after `Invoices::find()` (or use `findOrFail`).

---

## IN8. Medium: supplier invoices are deleted permanently

- **URL:** delete on `/invoices` (`POST|DELETE /invoices/{id}/delete`).
- **What happens:** no soft deletes. Deleting the last tour link hard-deletes the invoice (QA invoice 65 was removed). It is only allowed without payments (good), but there is no audit trail. Same as AC1.

---

## IN9. Low: URLs mix invoice-tour link ids and invoice ids

- **URLs:** `/invoices/{id}`, `/invoices/{id}/edit` and `/invoices/{id}/delete` use the **`invoices_tours` link id**, while `/add_payment/{id}` and `/payment_store/{id}` use the **supplier invoice id**. An invoice covering several tours appears as several rows, and editing one row changes only that row's tour.

**Possible fix:** route by invoice id and show linked tours inside one invoice.

---

# Part 33: `/reporting`

`/reporting` lists suppliers (hotels, events, restaurants, guides) with links to a per-supplier report: 5 months of supplier-invoice totals plus the invoice list (`ReportingController`, view `reporting/supplier_show.blade.php`). It is read-only; testing used the QA suppliers from the `/supplier_search` run and created no new data. Tested as `qa.admin`, `qa.agent` (role `user`) and a guest. Only `admin` and `Accountant` hold `reporting.index`.

## RP1. High: every supplier report page is 403 for everyone

- **URLs:** the **View** links on `/reporting`:

| Route | Required permission | Exists? |
|-------|---------------------|---------|
| `GET /reporting/hotel/{id}/show` | `reporting.hotel.show` | no |
| `GET /reporting/restaurant/{id}/show` | `reporting.restaurant.show` | no |
| `GET /reporting/event/{id}/show` | `reporting.event.show` | no |
| `GET /reporting/guide/{id}/show` | `reporting.guide.show` | no |

- **Evidence:** as admin, the first list link `/reporting/hotel/6313/show` returned 403, and so did `/reporting/restaurant/3703/show`, `/reporting/event/2842/show` and `/reporting/guide/21/show`.
- The reports themselves work: rendering `hotel_show(38)`, `hotel_show(7137)` and `restaurant_show(3703)` in-process produced the page with 5 months of totals and the invoice table, with the supplier name escaped. Only the missing permissions block them.

**Possible fix:** give these routes `'permissions' => 'reporting.index'` (or create the four permissions and assign them to admin/Accountant).

---

## RP2. High: supplier search endpoints are 403 for everyone

- **URLs:** `GET /reporting/{reporting}` (`reporting.show`) and `GET /reporting_supplier_show` (`reporting_supplier_show`).
- **What happens:** `reporting.show` exists but **no role** has it; `reporting_supplier_show` doesn't exist. Admin got 403 on `/reporting/1` and `/reporting_supplier_show?service=Hotel`.

**Possible fix:** map both to `reporting.index`, or remove them if the list page replaced them.

---

## RP3. Medium: only admin and Accountant can open the reporting list

- **URL:** `GET /reporting`.
- **What happens:** only `admin` and `Accountant` have `reporting.index`. `qa.agent` (role `user`) got 403; a guest is redirected to `/login`. This may be intended; with RP1, even those two roles can only see the list.

---

## RP4. Medium: unused method queries a column that doesn't exist

- **URL:** none (`ReportingController::getTotalAmountForDateRange()`).
- **What happens:** it runs `Transaction::where('account_id', …)`, but `transactions` has no `account_id` column, so it fails with "SQLSTATE[42S22]: Unknown column 'account_id'". It isn't called by the current page.

**Possible fix:** remove it, or rewrite it against `transactions.invoice_id`/`pay_to`.

---

## RP5. Low: `_` and `%` not escaped in the name search

- **URL:** `GET /reporting?q=…`.
- **What happens:** `where('name', 'like', '%' . $q . '%')`, so `?q=_` matches every supplier. Same as SS9.

---

## RP6. Low: an unknown service filter is silently ignored

- **URL:** `GET /reporting?service=Bogus`.
- **What happens:** values outside `Event/Hotel/Restaurant/Guide` are ignored and all suppliers are listed, instead of none or a validation message.

---

# Part 34: `/activities`

`/activities` is the system activity log (`ActivitiesController@index`, Spatie `activity_log`, 2,721 entries). It is read-only; the log already contained many entries from the earlier QA runs, so **no new data was created**. Tested as `qa.admin`, `qa.agent` (role `user`) and a guest.

## AT1. High: nobody can open the activity log

- **URLs:** `GET /activities`, linked from the dashboard's **"View all activities"** button (`/home` activities widget).
- **What happens:** the route is `Route::get('/', 'ActivitiesController@index')->name('activities_index')` (`routes/web.php:540`). `PermissionsRequiredMiddleware` uses the route name as the permission, so it requires **`activities_index`**, which **doesn't exist**. The roles that should see the log (admin, user, view only, Super User, Accountant) hold **`activities.index`** instead.

| User | `GET /activities` |
|------|-------------------|
| `qa.admin` (admin) | 403 |
| `qa.agent` (user) | 403 |
| guest | 302 to `/login` |

**Possible fix:** add `'permissions' => 'activities.index'` to the route, or rename it `activities.index` (and update the `route('activities_index')` links).

---

## AT2. Medium: activity log has no pagination and loads the log twice

- **URL:** `GET /activities`.
- **What happens:** `index()` runs `Activity::all()` (passed as `$logs`, unused by the table) **and** a `distinct()` join of the whole log with `->get()`. Rendered in-process as admin: **1.60 s, 66 MB peak memory, 2.5 MB of HTML, 2,578 table rows** (23 queries). The log grows with every save, so the page keeps getting slower.

**Possible fix:** drop `Activity::all()`, `->paginate(50)` the query, and add date/user/module filters.

---

## AT3. Medium: "View" buttons point to a hard-coded domain

- **URL:** `/activities` (View button per row); data in `activity_log.properties.link`.
- **What happens:** activity entries store absolute links such as `https://dev.eetstravel.com/tour/190`, so the View buttons always go to `dev.eetstravel.com`, whichever server the app runs on. Same pattern as I4.

**Possible fix:** store relative paths (e.g. `/tour/190`) in `properties.link` and build the URL at render time; migrate existing rows.

---

## AT4. Low: "View" button is built as raw HTML

- **URL:** `/activities`.
- **What happens:** `ActivitiesController@index` builds `"<a class='btn btn-sm btn-warning' href='$link' …>"` by string concatenation, and `resources/views/activities/index.blade.php:40` prints it with `{!! $activity->link_button !!}`. All stored links are currently internal URLs (none contain `javascript:` or quotes), so there is no live problem, but any unexpected `link` value would become markup.

**Possible fix:** render the button in Blade with `href="{{ $activity->link }}"`, and only allow relative/internal links.

---

## AT5. Low: everyone with access sees every user's actions

- **URL:** `GET /activities`.
- **What happens:** the query has no user or module scope. Once AT1 is fixed, all roles with `activities.index` (including "view only" and "user") would see every user's actions across all modules. Please confirm this is intended, or limit the full log to admins.

---

## What passed

**`/home`**
- To-do pagination works (10 per page); Previous/Next now load by AJAX.
- The Completed and Aborted tabs show the right tasks.
- Users only see their own data: a regular user sees only their own tours and tasks. `ApiPermissions` overrides any `userId` sent in the request.
- Text containing HTML is escaped in the task list, announcements, the tour table and chat messages.
- Announcement replies are hidden, and the 5-item limit is respected.

**`/users`**
- Creating with valid data works, and the PNG avatar is saved.
- Create shows errors for missing name, email or password, an invalid email, and a duplicate email (case-insensitive).
- Non-image uploads are rejected, including a PHP file renamed to `.png`.
- A name containing `<script>` is shown escaped in the list and the flash message.
- Self-delete is blocked; a guest is redirected to `/login`; a missing user ID returns 404.

**`/roles`**
- Creating and renaming roles work, through both `/roles/store` / `/roles/update` and the resource routes (`POST /roles`, `PUT /roles/{id}`).
- Empty and whitespace-only names are rejected.
- Missing role IDs return 404 (`/roles/999999`, `/roles/999999/edit`, `DELETE /roles/999999`).
- A role name containing `<script>` is escaped on `/roles`, `/roles/{id}`, `/roles/{id}/edit` and the role dropdown on `/users/{id}/edit`.
- An assigned permission really grants access: a user with only `tour.index` could open `/tour`, and `/task` stayed 403.
- A guest is redirected to `/login`.

**`/permissions`**
- Creating and editing work, through both `/permissions/store` / `/permissions/update` and the resource routes (`POST /permissions`, `PUT /permissions/{id}`).
- Empty name, empty alias and whitespace-only names are rejected.
- Renaming to an existing name gives a proper "The name has already been taken" error.
- Missing IDs return 404 on show, edit and update. Delete of a missing ID shows a clear "No query results" message.
- Deleting an unused permission works. Deleting one in use without `force` is refused with a clear message listing its roles. A forced delete also removes it from those roles (`role_has_permissions` rows: 0).
- An alias containing `<script>` is escaped on `/permissions`, `/permissions/{id}`, `/permissions/{id}/edit`, the permission dropdown on `/roles/{id}/edit` and on `/users/{id}/edit`.
- `GET /permissions/{id}` redirects a guest to `/login`; it is the only protected action.

**`/settings`** (as `qa.created` with the `settings.*` permissions)
- Create, edit and delete work.
- Duplicate names are rejected, including with different letter case.
- `update()` ignores a `name` sent in the request, so a setting's key can't be renamed through edit.
- A description and value containing `<script>` / `<img onerror>` are escaped on `/settings` and `/settings/{id}/edit`.
- `GET /settings/999999/edit` returns 404, and a guest is redirected to `/login`.
- `GET /api/settings/cache/status` returns 200 for a user with `settings.index` and 403 for others. Like all `/api/*` routes, it is still affected by H1.

**`/tour`**
- A valid create (`POST /tour/save`) works: the name gets a `#MMDD` suffix, `external_name` is generated (`EETSAT233`), and 6 tour days, 2 children and the assigned user are saved.
- Create validation rejects, with clear messages: empty required fields, departure in the past, return before departure, more than 2 years ahead, `pax_free > pax`, `pax=0`, `pax=abc`, no assigned user, assigned user `999999`, unknown city ID.
- An SVG in `files[]` and a PHP file renamed to `.png` in `attach[]` are rejected.
- A quotation (`is_quotation=1`) can be created without cities.
- A valid full edit (`POST /tour/{id}/update`) works, and delete soft-deletes the tour.
- `GET /tour/999999` and `/tour/999999/edit` return 404; a user without `tour.*` gets 403 on the `GET` pages.
- Inline edit rejects HTML in `name` and fields outside the allowed list (e.g. `deleted_at`).

**`/current_offers`** (in-process render)
- Valid future tours (including quotations with status "Quotation sent") are listed.
- Requested (46), finished-in-the-past and soft-deleted tours are excluded.
- A tour name containing `<script>` is escaped.
- A guest is redirected to `/login`.
- The render is quick: 0.43 s, 150 KB.

**`/current_bookings`** (in-process render)
- The **latest** hotel offer is used: of two offers on QA 556, the newer one (7 days / 50%, 30% deposit / 14 days) is shown.
- Soft-deleted packages (560) and packages with other statuses (561, Pending) are excluded.
- A package whose hotel no longer exists (564) shows an empty city without an error.
- A hotel name containing `<script>` (562) is escaped.
- Render: 0.67 s, 157 KB.

**`/past_offers`** (in-process render of all pages)
- Pagination works: 10 per page, 6 pages, a working "next" link, 58 rows in total, no duplicates.
- **Soft-deleted tours are correctly excluded** despite the `OR`: the query is grouped as `(status = 46 or retirement_date < ?) and deleted_at is null` (QA tours 146 and 147 don't appear).
- Genuinely finished (144, [QA] Past Vienna Tour) and cancelled past tours (145) are listed.
- A tour name containing `<script>` is escaped.
- A guest is redirected to `/login`.
- 0.15-0.44 s per page.

**`/cancellation_policies`** (in-process render)
- Policy text and offer status containing `<script>` / `<img onerror>` (QA policy 45) are escaped. The only unescaped markup on the page came from the header notifications (H4).
- Real offer statuses display as text ("Offered with Option", "Offered No rooms blocked", "Unavailable").
- Hotel, city and stay date are correct for valid packages (e.g. Salzburg / 2026-11-08 for QA 556).
- A guest is redirected to `/login`.
- Render: 0.73 s, 149 KB.

**`/task`**
- A valid create (`POST /task`) works: the assigned user is synced in `task_user` and gets a notification.
- Required fields are enforced on create and full edit ("The content field is required.", "The Date field is required.", …).
- A full edit (`POST /task/{id}/update`) works and returns the redirect route as JSON.
- Delete (`POST /task/{id}/delete`) works and returns JSON; a missing ID returns 404.
- Inline edit refuses fields that aren't allowed (`field=assign` → 400 "Invalid field").
- `GET /task/999999` and `/task/999999/edit` return 404.
- The task list (`/task`) and the edit form escape a `<img onerror>` title.
- A user without `task.*` gets 403 on `GET /task`, `/task/{id}` and `/task/{id}/edit`.

**`/supplier_search`** (search logic tested in-process)
- Name search is case-insensitive ("supplier hotel alpha", "SUPPLIER HOTEL ALPHA" and "Supplier Hotel Alpha" all find hotel 7136).
- The country filter (`countryalias=AT`), city name (`city_name=Wilten`) and address match (`city_name=Innsbruck`) narrow correctly.
- Hotel, Restaurant and Event searches each find their QA supplier; "All" returns all 5 QA suppliers.
- An invalid or missing `service` gives a clean 422 "Select a valid supplier service."
- A user without `supplier_search` gets 403 on `/supplier_search`; a guest is redirected to `/login`.
- `/supplier_search` page load: about 0.58 s.

**`/event`**
- A valid create (`POST /event`) works: rate, criteria (16) and a PNG attachment are saved.
- A full edit (`PUT /event/{id}`, which the form uses) works and keeps the event's own `work_email` (the unique check ignores the current event).
- Delete (`DELETE /event/{id}`) soft-deletes the event (`deleted_at` set). Missing IDs give 404 on edit, update and delete.
- `<script>alert(1)</script>` as a name is rejected; address and comments containing `<img onerror>` / `<script>` are escaped on `/event/{id}`, `/event/{id}/edit` and the `/event` list.
- Search works (`/event?search=QA`).
- A user without `event.*` gets 403 on `GET /event`, `/event/{id}` and `/event/{id}/edit`; a guest is redirected to `/login`.

**`/restaurant`**
- A valid create works, and consecutive creates get sequential ids (3700, 3701).
- A valid edit (`PUT /restaurant/{id}`) works; renaming to an existing restaurant's name is refused.
- Deleting an **unused** restaurant soft-deletes it (`deleted_at` set).
- Missing IDs: `GET /restaurant/999999` and `/edit` return 404; `PUT` and `DELETE` on 999999 return 404. (Unlike events, the show page checks for a missing ID first.)
- XSS in address and comments is escaped on the `/restaurant` list and the edit form.
- Search works (`/restaurant?search=QA`).
- A user without `restaurant.*` gets 403 on `GET /restaurant` and `/restaurant/{id}/edit`.

**`/guide`**
- A valid create works and saves the PNG attachment (1 `files` row). `guides.id` has AUTO_INCREMENT, so ids are sequential (21, 22, …), unlike restaurants.
- A valid edit (`PUT /guide/{id}`) works; deleting an **unused** guide soft-deletes it.
- Missing IDs return 404 on `/guide/999999/edit`, `PUT` and `DELETE`.
- XSS is escaped everywhere: `<script>` in `contact_name`, `<img onerror>` in the address and `<b onmouseover>` in comments all appear escaped on `/guide/{id}`, `/guide/{id}/edit` and `/guide`.
- Search works (`/guide?search=QA`).
- A user without `guide.*` gets 403 on `GET /guide` and `/guide/{id}/edit`.
- The `/guide` list loads in 0.74-0.99 s (69 queries, mostly from the layout); the first 2.8 s load was a one-off warm-up.

**`/images/index`** (`saveFile()` tested in-process against QA tour 136)
- A single image upload works: a 200 response with the new attachment, and the file saved in `public/uploads/attachments/`.
- Rejected with clear messages: SVG, a PHP file renamed to `.png`, a text file ("Please choose an image file (JPG, PNG, GIF or WEBP)."), no files ("The files field is required."), `model=User` and `model=..\Tour` ("The selected model is invalid."). A missing record returns 404 "Model not found".
- The stored extension comes from the file content (`guessExtension()`), not the client file name, and the name is randomized.
- `attachments.js` sends `files[]` (matching the controller's validation), updates only the uploaded card on `/images/index`, and shows success/error toasts.

**`/bus/calendar`**
- Busy checks work: the same bus on overlapping dates gives `{"bus_busy":true,"bus_busy_message":"The Bus is Busy from 2026-11-01 to 2026-11-03"}`; the same driver on another bus gives "The Driver is Busy".
- Date validation: end before start, a wrong format (`10/11/2026`) and missing name/country/city return clear 422 errors.
- A valid trip creates one bus day per date (3 days for +5..+7) with the bus, driver and status.
- Moving a whole trip to another bus and status (`/bus_day/update`, `trip_mode=0`) and deleting a single day (`/bus_day/delete`, `trip_mode=1`) work.
- A wrong `trip_id` on update returns 404; a missing `trip_mode` on delete returns 422.
- A guest is redirected from `/bus/calendar`, and `/api/bus_days` returns 401.

**`/transfer`**
- A valid create works and saves the PNG attachment (1 `files` row).
- A valid edit (`PUT /transfer/{id}`) works.
- Missing IDs return 404 on `/transfer/999999/edit`, `PUT` and `DELETE`.
- XSS in the address and comments is escaped on `/transfer/{id}`, `/transfer/{id}/edit` and `/transfer`.
- A bus company whose stored city id no longer exists (`city = 99999999`) still opens on show and edit (200).
- Search works (`/transfer?search=QA`); the list loads in 0.88 s and create in 0.79 s.
- A user without `transfer.*` gets 403 on `GET /transfer` and `/transfer/{id}/edit`.

**`/bus`**
- **The delete guard works:** `POST /bus/38/delete` for a bus used in a bus day returned `{"success":false,"message":"Bus QA Bus Valid Edited cannot be deleted because it is used in 1 bus days."}` and the bus was **not** deleted. (Bus is the only supplier where `DeleteModel` behaves correctly, because its case returns early.)
- A normal delete (`POST /bus/40/delete`) soft-deletes the bus; a missing ID returns 404 JSON.
- No mass assignment: `id=987900` and `created_at` were ignored, because the controller uses `$request->only('name', 'bus_number', 'transfer_id')`.
- Validation errors are shown on `/bus/create` ("The name field is required.", "The bus number field is required."). Required fields, a duplicate `bus_number`, `transfer_id=999999` and `capacity=9999` are rejected.
- Missing IDs return 404 on `/bus/999999`, `/bus/999999/edit` and `POST /bus/999999/update`.
- XSS in `bus_number` is escaped on `/bus`, `/bus/{id}` and `/bus/{id}/edit`.
- Search works (`/bus?search=QA`); the list loads in 0.93 s and create in 0.80 s.
- A user without `bus.*` gets 403 on `GET /bus` and `/bus/{id}/edit`.

**`/driver`**
- A valid create (`POST /driver`) and edit (`PUT /driver/{id}`) work. The edit form uses the protected named route, so there is **no update bypass** for drivers.
- No mass assignment: `id=987950` was ignored (`$request->only('name', 'phone', 'email', 'transfer_id')`).
- An empty name and an invalid email are rejected, and the create form shows the errors (`alert-danger` block).
- Missing IDs return 404 on `/driver/999999`, `/driver/999999/edit`, `PUT /driver/999999` and `POST /driver/999999/delete`.
- XSS is escaped on `/driver/{id}/edit` and the `/driver` list.
- Search works (`/driver?search=QA`); the list loads in 0.78 s and create in 0.64 s.
- A user without `driver.*` gets 403 on `GET /driver`, `/driver/{id}` and `/driver/{id}/edit`.

**`/status`**
- **The in-use check works for tour, task, hotel, service and bus statuses:** deleting task status 68 while task 272 used it returned `{"error":true,"message":"Status QA Status Task Done is in use and cannot be deleted"}` and kept the status. An unused status (71) was deleted.
- Empty `name`/`type`/`sort_order` and `sort_order=abc` are rejected.
- Missing IDs return 404 on `/status/999999`, `/status/999999/edit`, `PUT /status/999999` and `POST /status/999999/delete`.
- A valid create and edit (`PUT /status/{id}`) work.
- XSS is escaped on the `/status` list and `/status/{id}/edit`.
- Search works (`/status?search=QA`); the list loads in 0.99 s.
- A user without `status.*` gets 403 on `GET /status` and `/status/{id}/edit`.

**`/room_types`**
- **The in-use check works:** room type 17, used by QA tour 136 (`tour_room_type_hotels`), returned `{"error":true,"message":"Room Type QA Room Valid Edited is used by hotels, seasons, offers or tours and cannot be deleted"}` and was kept. The check covers `hotel_room_types`, `hotel_agreements_room_type_hotels`, `seasons_prices_room_type_hotels`, `prices_room_type_hotels`, `tour_room_type_hotels` and `offer_room_prices`.
- A valid create with a PNG image works; editing with a new image replaces the stored file name.
- A PHP file renamed to `.png` is rejected.
- Empty `name`/`code`/`sort_order` and `sort_order=abc` are rejected.
- Missing IDs return 404 on `/room_types/999999`, `/room_types/999999/edit`, `PUT` and `POST /room_types/999999/delete`.
- XSS is escaped on the `/room_types` list and `/room_types/{id}/edit`.
- Search works (`/room_types?search=QA`); the list loads in 0.78 s.
- A user without `room_types.*` gets 403 on `GET /room_types` and `/room_types/{id}/edit`.

**`/rate`**
- A valid create (`POST /rate`) and edit (`POST /rate/{id}/update`) work.
- Empty `name`/`mark`/`rate_type`/`sort_order` are rejected.
- Missing IDs return 404 on `/rate/999999`, `/rate/999999/edit`, `POST /rate/999999/update` and `POST /rate/999999/delete`.
- XSS is escaped on the `/rate` list, `/rate/{id}/edit`, and the rate dropdowns on `/event/create` and `/hotel/create`.
- Search works (`/rate?search=QA`); the list loads in 0.68 s.
- A user without `rate.*` gets 403 on `GET /rate` and `/rate/{id}/edit`.

**`/currency_rate`**
- A valid create (`POST /currency_rate`) and edit (`POST /currency_rate/{id}/update`) work; the create form includes the date field.
- Empty fields, `rate=abc` and `date=31/12/2026` are rejected.
- Missing IDs return 404 on `/currency_rate/999999`, `/edit`, `POST …/update` and `POST …/delete`.
- XSS is escaped on the `/currency_rate` list and `/currency_rate/{id}/edit`.
- Search works (`/currency_rate?search=QA`); the list loads in 0.63 s.
- A user without `currency_rate.*` gets 403 on `GET /currency_rate` and `/currency_rate/{id}/edit`.

**`/currencies`**
- A valid create (`POST /currencies`) and edit (`POST /currencies/{id}/update`) work.
- Empty `name`/`code`/`symbol` are rejected.
- Missing IDs return 404 on `/currencies/999999`, `/currencies/999999/edit` and `POST /currencies/999999/update`.
- XSS is escaped on the `/currencies` list and `/currencies/{id}/edit`.
- `/tour_package/556` (show) still opens after its currency was deleted (200).
- Search works (`/currencies?search=EUR`); the list loads in 0.65 s.
- A user without `currencies.*` gets 403 on `GET /currencies` and `/currencies/{id}/edit`.

**`/criteria`**
- A valid create (`POST /criteria`) and edit (`POST /criteria/{id}/update`) work.
- Empty `name`/`short_name`/`criteria_type` are rejected.
- Missing IDs return 404 on `/criteria/999999`, `/criteria/999999/edit`, `POST …/update` and `POST …/delete`.
- **The icon field is safe:** `icon = star" onmouseover="alert(1)` is escaped inside `class="fa fa-{{$icon}}"` in `component.icon_for_criteria`.
- XSS is escaped on the `/criteria` list, `/criteria/{id}/edit`, `/restaurant/{id}/edit` and `/restaurant/create`.
- `/restaurant/3703` still opens after one of its criteria was deleted.
- The list loads in 0.63 s; a user without `criteria.*` gets 403 on `GET /criteria` and `/criteria/{id}/edit`.

**`/email`**
- `/email/configure` (`POST`) rejects an invalid email and a password under 6 characters, and stores a valid one **encrypted with `email_password_encrypted = 1`**.
- Webmail SSO (`GET /email/webmail`) redirects to `/snappymail/?Sso&hash=…` with a one-time hash; the password is not in the URL. Without a mailbox it redirects to `/email/configure`.
- `/email/webmail/admin` redirects non-admins away (only the `admin` role reaches SnappyMail's admin).
- `/mail-attachments/{user}/{token}` requires a **signed URL** and an owner match (unsigned gives 403), a strict UUID token and a sanitized download name.
- Built-in attachment download matches the requested name against sanitized attachment names (no file path from the URL).
- The `/api/v1/users/{userId}/email*` endpoints reject another user's `userId` (403 in `ApiPermissions`), though they are still affected by H1.

**`/comment`**
- Creating a comment, a reply (`parent`) and a reply to a reply works; the thread lists them.
- An empty comment (no content, no attachment) is rejected (422).
- `author_id` can't be faked on **create** (`author_id=1` still saved the logged-in user, 179).
- A user without `comment.edit` gets 403 on `PUT /comment/{id}`.
- `GET /comment/999999` returns 404; the comment index (`/comment`) loads in 0.75 s.

**`/clients`**
- A valid create works, and the client-portal password is stored encrypted.
- Empty required fields and a duplicate `work_email` are rejected.
- The edit form (`PUT /clients/{id}`) works, and `update()` saves only whitelisted fields.
- Missing IDs return 404 on `/clients/999999` and `/clients/999999/edit`; the edit page escapes HTML.
- The list loads in 0.68 s.
- The client portal (`/client_tour_package`, `/TMS-Client/*`) redirects guests to `/TMS-Client/login`.

**`/accounting`**
- **Totals are correct:** 2 × 100 with VAT 0.2 plus `extra_cost` 10 gave `amount_receiveable = 250.00`; the invoice number is unique (`INV-<uuid>`).
- **Validation works and saves nothing on error:** currency `GBP` (only EUR/USD/CHF), `vat=5` (must be 0-1), a negative amount, `quantity=0` and an unknown `client_id` were all rejected; 0 invalid invoices were saved.
- **Overpayment is blocked:** paying 150 on a 100 invoice at create, and paying 250 when 200 remained (`POST /inv_payment_store/{id}`), were both refused with "Payments cannot exceed the invoice balance."
- **Paying the exact balance works:** paying the remaining 200 brought the paid total to 250.00, and `/accounting/{id}` shows "Paid".
- **Invoices with payments can't be deleted:** `POST /accounting/30/delete` returned 422 "An invoice with payments cannot be deleted."; an invoice without payments was deleted.
- **Permissions are enforced on every write route**, including `POST /accounting/{id}/update` and `POST /inv_payment_store/{id}`: `qa.created` got 403 on both, and on `GET /accounting` and `/accounting/create`.
- Saves and payments lock the invoice row (`lockForUpdate`) inside a transaction.
- Excel (`/accounting/{id}/excel`) and PDF (`/accounting/{id}/export/pdf`) exports return 200; `/accounting/999999` and `/accounting/999999/edit` return 404.

**`/invoices`**
- A valid create (`POST /invoices`) saves the invoice, the tour/package link and the first payment, then redirects to the tour page.
- **Overpayment on create is blocked:** paying 150 on a 100 invoice gave "Payments cannot exceed the invoice balance."
- Invalid input is rejected and nothing is saved: `office_id=999999`, `tour_id[]=999999`, `total_amount=-5`, `extra_amount=-5`.
- **Invoices with payments can't be deleted:** `POST /invoices/123/delete` returned 422 "An invoice with payments cannot be deleted."; one without payments was deleted with its link.
- **Write routes check permissions:** `qa.created` got 403 on `GET /invoices`, `/invoices/create`, `POST /payment_store/{id}` and `POST /invoices/{id}/update`.
- Saves and payments lock the invoice row inside a transaction.
- Invoice number and note containing HTML are escaped on `/invoices/{id}`, `/invoices/{id}/edit` and `/invoices`.
- `/invoices/999999` and `/add_payment/999999` return 404.

**`/reporting`**
- For admin, `/reporting` lists suppliers of all four types, 50 per page with pagination, in 0.68 s.
- The name search ignores case (`?q=supplier HOTEL alpha` finds hotel 7136), and the type filter works (`?q=Supplier&service=Restaurant` returns only restaurant 3699).
- Supplier names with markup are escaped in the list.
- The supplier report renders correctly when its route permission is bypassed: 5 months of totals and the invoice rows, with no raw markup.
- `qa.agent` (no `reporting.index`) gets 403; a guest is redirected to `/login`.

**`/activities`**
- A guest is redirected to `/login`.
- With the route permission bypassed, the page renders without errors: descriptions are converted to plain text (`ActivityDescription::plainText`) and user names are joined in.
- The query count is low (23); the cost comes from the number of rows (AT2), not N+1 queries.
- No stored activity link contains `javascript:` or quotes (AT4 is preventive).

## Test data

All records are tagged `[QA]` or use `qa.*@eets.test` emails. The password for all QA users is `QaTest#2026`.

| Data | Used for | Details |
|------|----------|---------|
| Users | both | `qa.admin` (admin), `qa.agent` (user), `qa.created`, `qa.long`, `qa.spaces`, `qa.svg`, `qa.byagent` |
| Tours | `/home` | 8: past, current, future, quotation, ends before it starts, script in name, long name, agent-only |
| Tasks | `/home` | 22: pending (paginated), overdue, no deadline, HTML in title, custom status, completed, aborted, private agent task |
| Announcements | `/home` | 7: normal, script in title/body, long, reply |
| Chat messages | `/home` | 2: normal, script |
| Holiday | `/home` | 1 calendar holiday |
| Avatar files | `/users` | 2 in `public/uploads/avatars/` (PNG, malicious SVG) |
| Roles | `/roles` | `[QA] Role Editor (renamed by agent)`, `[QA] Role Renamed PUT`, `[QA] Agent Role`, a `<script>` role, a long-name role, `[QA] Role via resource POST` |

| Permissions | `/permissions` | `qa.perm.alpha.guest`, `qa.perm.renamed.put`, `qa.perm.xss`, a long-name permission, `qa.perm.resource`, `qa perm with spaces!@#` |
| Settings | `/settings` | `qa_setting_days`, `qa_setting_date`, `qa_setting_text`, `qa_setting_xss`, a long-name setting, `qa setting spaces !@#`, `qa_setting_massassign` (id 987654) |
| Role | `/settings` | `[QA] Settings Role` with `settings.*`, assigned to `qa.created` |
| Tours | `/tour` | ids 133-137: "QA Edited Without Permission #1106", "QA Quotation Tour #1126", "QA Many Children #1106" (deleted), "QA Room Types #1106", "QA Created Without Permission #1106", plus their tour days, children, room-type rows and notifications |
| City | `/tour` | id 26468 "[QA] Fake City <script>…", country `ZZ` |
| Hotel bookings | `/current_bookings` | tour packages 556-564 "[QA] Booking …" (future, past, without tour, deleted tour, soft-deleted, pending, `<script>` name, empty policies, missing hotel), hotel 3, with hotel offers and cancellation/payment policies |
| Supplier invoices | `/invoices` | invoice numbers "QA-SINV…" on QA tour 136: invoice 63 "QA-SINV-1" (50.00 paid, linked to package 556), a duplicate "QA-SINV-1"; the "QA-SINV-<b>…" invoice was deleted in the IN8 test; the temporary orphan link for IN7 was removed |
| Client invoices | `/accounting` | notes "QA Invoice …" on QA tour 136 / QA client 987995: invoice 30 "QA Invoice valid" (fully paid, 250.00 in 2 payments); "QA Invoice nopay" was deleted in the AC1 test |
| Clients | `/clients` | ids 25 "QA Client …" (soft-deleted in the CL6 test), 987995 (extra fields), 987996 (HTML marker in address), 987997 (SVG attachment); QA tour 136's client link was cleared afterwards |
| Comments | `/comment` | "QA Comment …" on QA tour 136 (the parent/child/grandchild thread was deleted by the CO2 test), 987990 (extra fields), 987991 (orphan, unknown reference type) |
| Mailbox settings | `/email` | `qa.admin` (179) was set to the fake mailbox `qa.mailbox@eets.test` to test configure/SSO, and **cleared afterwards** (no email sent, no real mailbox opened) |
| Mailbox settings | `/email/webmail` | `qa.created` (181) was set to the fake mailbox `qa.mailbox@eets.test` through `/email/configure` (EM14) and **cleared afterwards** (no email sent, no real mailbox opened) |
| Criteria | `/criteria` | ids 21 (duplicate "QA Crit Valid"), 22 (now "QA Crit Edited Without Permission"), 23 (XSS, attached to QA restaurant 3703); 19 and 20 **permanently deleted** in the tests, with a dangling `services_has_criterias` row for 19 on restaurant 3703 |
| Currencies | `/currencies` | ids 19 "QA Currency Dup EUR" (code `EUR`), 20 (code "qa money 123"), 21 (bad cent fields), 22 (XSS, now "QA Currency Edited Without Permission"); 18 **permanently deleted** by the CU1 test. QA package 556 and QA offer 103 had their `currency` cleared afterwards. |
| Currency rates | `/currency_rate` | ids 14 "QAUSD" (rate changed to 999 by `qa.created`), 15 "QAZERO" (rate 0), 17 "QABIG" (capped 999999.99), 18 (duplicate QAUSD), 19 "QA dollar ccy", 20 "QAFUT" (2099), 21 (XSS); 16 "QANEG" deleted |
| Rates | `/rate` | ids 18 (duplicate "QA Rate Valid"), 19 (300-char name), 20 (XSS, now "QA Rate Edited Without Permission"); 16 and 17 **permanently deleted** by the RA1/RA2 tests. QA hotel 7136 and QA event 2842 had their `rate` cleared afterwards. Real "QA Sample …" rates (13-15) are not test data. |
| Room types | `/room_types` | ids 17 (now "QA Room Edited Without Permission", used by QA tour 136), 19 (duplicate name), 20 (300-char name), 21 (XSS), 22 (SVG with script); 18 (code `DOU`) permanently deleted in the test; images in `public/assets/img/svg/`. The `DOU` rename test was rolled back. |
| Statuses | `/status` | ids 68 "QA Status Task Done", 69 (now "QA Status Edited Without Permission"), 70 (type `nonexistent`), 72 (duplicate "QA Status Tour"), 73 (colour injection), 74 (XSS), 75 "Try Completed", 76 (300-char name); 71 and 77 permanently deleted in the tests. QA task 272 and offer 103 were restored to status 2 / "Offered with Option". Real "QA Sample …" statuses (63-66) are not test data. |
| Drivers | `/driver` | ids 28 (now "QA Driver Valid Edited"), 29 "Bad Phone", 30 "Dup Email" (**permanently deleted** by the DR1 bug), 31 (accented name, garbled by the test shell), 32 (300-char name), 34 "Deleted Company", 35 "Mass" (permanently deleted, DR2), 36 (XSS); bus day 688 "[QA] Trip Driver Guard" with an orphaned `trip_to_drivers` row for driver 30 |
| Buses | `/bus` | ids 38 "QA Bus Valid Edited" (has bus day "[QA] Trip Bus Guard"), 39 "QA Bus Deleted Company" (linked to deleted company 987804), 40 (soft-deleted), 41 (now "QA Bus Edited Without Permission"), 42 "QA Bus XSS" |
| Bus companies | `/transfer` | ids 177 (now "QA T Edited Without Permission"), 178, 987800 "QA T Mass Assign", 987801 "QA T XSS Fields" (city set to 99999999), 987802 "QA T SVG Attach", 987803 "QA T Used In Tour" (deleted by the TR1 bug), 987804 "QA T With Fleet" (deleted); tour package "[QA] Booking Transfer Ref"; "[QA] Bus Fleet", "[QA] Driver Fleet"; criteria rows and PNG/SVG files |
| Buses / trips | `/bus/calendar` | buses 35 "[QA] Bus Alpha", 36 "[QA] Bus Beta"; drivers 25, 26 "[QA] Driver One/Two"; trips/bus days "[QA] Trip Vienna" (one day deleted), "Bad Refs" (status changed to 18 to restore the calendar), "No Bus", XSS name, "Past", "By View Only"; the 400-day "[QA] Trip Long" was deleted by the BC2 test |
| Tour images | `/images/index` | test images uploaded to QA tour 136 only (attachment 38 remains; earlier ones were deleted by the replace behaviour), files in `public/uploads/attachments/` |
| Guides | `/guide` | ids 21 (now "QA G Edited Without Permission"), 22-23, 987700 "QA G Mass Assign", 987701 "QA G XSS Fields", 987702 "QA G SVG Attach", 987703 "QA G Used In Tour" (deleted by the G1 bug), 987704 "QA G To Delete" (soft-deleted); tour package "[QA] Booking Guide Ref"; criteria rows and PNG/SVG files |
| Restaurants | `/restaurant` | ids 3700-3706 "QA R …" (3700 soft-deleted; 3705 soft-deleted to reproduce RS3; 3701 restored after the RS1 tests; 3703 renamed by `qa.created`); tour package 565 "[QA] Booking Restaurant Ref"; criteria rows 47-58 and 2 `files` rows saved under restaurant id 0 (RS2) |
| Events | `/event` | ids 2843 (now "QA E Edited Without Permission"), 2844 "QA E Bad Criteria", 2845 "QA E Unknown City", 987600 "QA E Mass Assign", 987601 "QA E XSS Fields", 987602 "QA E SVG Attach", 987603 "QA E To Delete" (soft-deleted); their criteria rows, `files` rows and stored PNG/SVG |
| Suppliers | `/supplier_search` | hotels 7136 "[QA] Supplier Hotel Alpha", 7137 (XSS name/contact), 7138 "[QA] Supplier Hotel 100% Wild_Card" (encrypted test portal passwords); restaurant 3699 "[QA] Supplier Restaurant Beta" (created as id 0, moved); event 2842 "[QA] Supplier Event Gamma". The guest-created `supplier_comments` row (id 10) was already deleted. |
| Tasks | `/task` | ids 272-279 "[QA] T …" (valid, bad date, bad status, bad type, bad tour, bad assignee, XSS title, admin personal task, now titled "… (changed by agent)"); 280 created and deleted; "New task [QA] …" notifications |
| Hotel offers / policies | `/cancellation_policies` | offers 103-107 (`ref = 'QA-POLICY'`) with cancellation policies 41-45 (`percentage` type, `fixed` type, soft-deleted package, deleted tour, XSS status/type); orphan policy 46 (offer 99999999); earlier QA offers' status changed from `23` to "Offered with Option" |
| Tours | `/past_offers` | ids 144-147: "[QA] Past Finished 70 Days Ago", "[QA] Past Cancelled Tour", "[QA] Past Requested Deleted Tour" (soft-deleted), "[QA] Past Deleted Finished Tour" (soft-deleted) |
| Tours | `/current_offers` | ids 138-143: "[QA] Offer Cancelled Future", "[QA] Offer Running Ends In 3 Days", "[QA] Offer Ends In 8 Days", "[QA] Offer Requested Future", "[QA] Offer Quotation Sent", "[QA] Offer No City No Status" |

To remove all QA data, run the `qa_cleanup.php` script from the QA session. It also deletes the uploaded avatar files, the QA roles and the QA permissions.
- Roles are matched with `BINARY name LIKE '[QA]%'`.
- Permissions are matched with `BINARY name LIKE 'qa.perm%'` plus the one odd-character name.
- Settings are matched with `BINARY name LIKE 'qa\_setting%'` plus the one odd-character name, and the `settings` AUTO_INCREMENT is reset afterwards.
- Tours from the `/tour` run are matched by **id (133-137) and name** together, because a real tour named "QA Sample Section Test Tour" exists.
- Restaurant criteria and files saved under id 0 are removed **only if created today**. The 3 older id-0 criteria rows (30, 32, 35) are real data and are left for the team to decide on (RS2).

So the real `admin`/`user` roles and the 250 real permissions are never touched.
