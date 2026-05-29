# TMS Audit Report — Phase 1 Discovery

**Repo:** `/var/www/html` · **Deploy target:** `https://dev.eetstravel.com` · **Stack:** PHP 8 · Laravel 8 · MySQL · Spatie laravel-permission · Blade + Bootstrap 5 + jQuery + DataTables · Laravel Mix.

This is the read-only Phase 1 deliverable. No code has been changed. Findings are organised into:

1. [Cross-cutting / systemic issues](#cross-cutting--systemic-issues) — bugs that span many modules. Fix these once; many module-level findings become moot.
2. One section per module, each with **Files / Tests / Dead code / Critical / Major / Minor** subsections.
3. [Recommended fix order](#recommended-fix-order) and [open questions for you](#open-questions-for-you-please-resolve-before-phase-2).

> **Severity scale**
>
> - **Critical** — broken flow, data loss / corruption, auth bypass, IDOR, SQL injection, XSS, server crash.
> - **Major** — wrong business logic, broken everyday UX, missing validation, missing server-side permission check, missing pagination on large lists, obvious N+1 queries.
> - **Minor** — polish, inconsistency, dead code, deprecated API.

---

## Cross-cutting / systemic issues

These are repeated across many modules. Each module section below references them but doesn't re-explain.

### CC1. **Critical — permission middleware fails open on any exception** ([app/Http/Middleware/PermissionsRequiredMiddleware.php:63-77](app/Http/Middleware/PermissionsRequiredMiddleware.php#L63))
`try { hasPermissionTo(...) } catch (\Exception $e) { ... return $next($request); }`. Any exception (including Spatie's `PermissionDoesNotExist` when the slug isn't seeded) is swallowed and the request continues. Combined with the fact that **most modules have no permission seeder** (see CC2), this means the gate effectively no-ops for every unseeded route. This is the single highest-impact bug in the audit — it disarms every "Major: missing permission check" finding into "no check happens at all in production."

### CC2. **Critical — no permission seeders for most modules** ([database/seeds/](database/seeds/))
Seeders exist for tours, dashboard, holiday, notification, activities, announcements, calculation, quotation, suppliers-and-bus-and-client, chat-and-supplier-search, flight-cruise, and a generic `PermissionsSeeder`. **No seeders for**: `invoices.*`, `accounting.*`, `transaction.*`, `officeInvoices.*`, `office.*`, `office_balance.*`, `office_earning.*`, `employes-salary.*`, `tour_expenses.*`, `utility_expenses.*`, `taxes.*`, `task.*` (only `TaskSeeder` for data, not permissions), `guestlist.*`, `comment.*`, `comparison.*`, `services.*`, `supplier_search.*`, `template.*`. Combined with CC1, every route in these modules is currently reachable by any authenticated user regardless of role.

### CC3. **Critical — money columns stored as INT or VARCHAR** ([database/schema/mysql-schema.dump](database/schema/mysql-schema.dump))
`supplier_invoices.total_amount`, `client_invoices.amount_receiveable`, `invoice_items.amount`, `invoice_items.total_amount`, `transactions.amount`, `office_balance.total_amount`, `office_utility_expenses.monthly_expense`, `office_employes_salary.employe_salary`, `office_tours.tour_expenses` are all `int(...)`. `office_earnings.revenue`, `office_earnings.profit`, `office_invoices.officeinvoice_amount` are `varchar`. Anything below 1.00 currency unit is rounded to 0 by MySQL on insert; varchar sums coerce string-prefix-to-int. Systematic under-payment / unpaid-balance bugs. Compounded by float-summation in PHP (`$payment_amount += $paidamount` in `InvoicesController:178-188` and `ClientInvoiceController:209-221`).

### CC4. **Critical — 91 destructive routes registered as `GET`** ([routes/web.php](routes/web.php))
Grep `Route::get('...delete...'`: 91 hits. Every one is CSRF-bypass-able, vulnerable to browser/antivirus link prefetch, fires on any `<img src>` an attacker plants, can be triggered from another tab in the same session, etc. Affects tours, invoices, transactions, office sub-resources, tasks, guest lists, quotations, hotels, events, guides, restaurants, transfers, drivers, etc.

### CC5. **Critical — `protected $guarded = []` on 17+ models** (see [app/Tour.php:77](app/Tour.php#L77), [app/Quotation.php](app/Quotation.php), [app/Invoices.php](app/Invoices.php), [app/ClientInvoices.php](app/ClientInvoices.php), [app/Transaction.php](app/Transaction.php), [app/Comparison.php](app/Comparison.php), [app/ComparisonRow.php](app/ComparisonRow.php), [app/GuestList.php](app/GuestList.php), all `app/Office_*.php`, etc.)
Mass-assignment is wide open. Controllers commonly call `Model::create($request->except(['attach']))` or `$model->update($request->except(['attach']))`. An attacker can submit `office_id`, `tour_id`, `client_id`, `paid`, `total_amount`, `invoice_no`, even `id` to overwrite arbitrary rows or attribute money movements to other offices.

### CC6. **Major — no foreign-key constraints, no indexes on FK columns** ([database/migrations/](database/migrations/))
`grep ->foreign` returns **zero hits** across 149 migrations. Some FK columns are indexed (e.g. `tour_packages.tour_id`), but `supplier_invoices`, `client_invoices`, `transactions`, `tasks` have only `PRIMARY` — every `WHERE invoice_id=?` / `WHERE tour_id=?` / `WHERE assign=?` does a table scan. With years of data this is the source of the 5+-second page loads you'll see on tour show / invoices index. Cascading deletes are not enforced anywhere; "orphan transaction whose `invoice_id` no longer matches an invoice" is structurally allowed (and `InvoicesController::destroy` creates exactly this).

### CC7. **Major — virtually no tests** ([tests/](tests/))
Only meaningful tests: `tests/Feature/TourApiTest.php`, `tests/Unit/TourServiceTest.php`. Everything else is example/AdminLTE scaffolding. The Tour API tests cover the modern Sanctum API but the legacy web controllers, invoices, transactions, tasks, exports, vouchers, frontsheet, quotations, guests, offices, landing page have **zero** test coverage. The "Done" criterion in your spec ("at least one happy-path and one failure-path test exist for the module") will require writing the entire test foundation as part of Phase 2.

### CC8. **Major — `Route::resource` plus duplicate `Route::post('/{id}/update')` aliases** ([routes/web.php passim](routes/web.php))
Every CRUD group has both `Route::resource` and an explicit `Route::post('foo/{id}/update', 'FooController@update')`. The duplicate POST shadows the RESTful PUT, defeats method spoofing, and means `_method=PUT` forms work in some places and not others. Also `Route::get('foo/{id}/delete')` shadows `Route::delete()` from the resource. Mixed conventions confuse new developers and cause CC4.

### CC9. **Major — controllers do not use bound repositories** (Tours, Tasks, Hotels, etc.)
`AppServiceProvider` registers Contract → Eloquent bindings for ~15 aggregates, but the controllers either inject the contract and ignore it (`TourController` injects `TourRepository` and never calls it — direct `Tour::query()` everywhere) or never inject it. The repository pattern is dead weight in its current form.

### CC10. **Major — `box-*` (AdminLTE / Bootstrap 4) classes inside Tabler / Bootstrap 5 shell**
A compatibility shim was added to `tabler-app.blade.php` to keep these rendering, but ~171 view files still use `box`, `box-primary`, `box-solid`, `box-tools`, `pull-right`, `data-dismiss`, `input-group-btn`, `nav-stacked`. None of these are Bootstrap 5 classes. Cosmetic, but means every fix that touches a view is also a partial migration.

### CC11. **Major — index pages load entire tables, then paginate in PHP** (Tours, Invoices, Transactions, Quotations, GuestList, Office sub-resources)
Repeated pattern: `Model::all()->get()` or `Model::with(...)->get()` then `->paginate()` on the Collection. None use `Builder::paginate()`. For tables with thousands of rows this is the main source of slow list pages and OOM risk.

### CC12. **Major — `dd(...)` reachable in production**
Confirmed in at least: [InvoicesController.php:314, 473](app/Http/Controllers/InvoicesController.php#L314); [ClientInvoiceController.php:249, 261, 367, 383, 669](app/Http/Controllers/ClientInvoiceController.php#L249); [OfficeInvoiceController.php:98, 112](app/Http/Controllers/OfficeInvoiceController.php#L98). Any of these branches halts the response with a stack-trace dump.

### CC13. **Major — `try { ... } catch (\Exception $e) { }` silent swallow throughout**
`TourController` swallows offices / invoice / billing query failures and logs them via `\Log::warning`, so the tour-show "billing" panel is empty in prod **but the user sees no error**. Similar pattern in `ServicesController`, `GuestListController`, `ExportTrait`. (See module sections for specific lines.)

### CC14. **Major — landing-page whitelist propagated to controllers that don't have the method**
Multiple controllers register `$this->middleware('auth', ['except' => 'landingPage'])` but **have no `landingPage` method** ([InvoicesController:52-53](app/Http/Controllers/InvoicesController.php#L52), [TourExpenseController:43-44](app/Http/Controllers/TourExpenseController.php#L43), [OfficeEarningController:43-44](app/Http/Controllers/OfficeEarningController.php#L43), [BalanceAmountController:43-44](app/Http/Controllers/BalanceAmountController.php#L43), [UtilityExpenseController:43-44](app/Http/Controllers/UtilityExpenseController.php#L43), [EmployesSalaryController:43-44](app/Http/Controllers/EmployesSalaryController.php#L43), [OfficeController:49-50](app/Http/Controllers/OfficeController.php#L49), [OfficeInvoiceController:44-45](app/Http/Controllers/OfficeInvoiceController.php#L44), [TMSClient/TourController.php:33](app/Http/Controllers/TMSClient/TourController.php#L33)). Dead exception clause; suggests landingPage was once intended to be much wider in scope.

### CC15. **Major — `unserialize()` on database column** ([app/Quotation.php:97-105](app/Quotation.php#L97))
`getCalculationAttribute()` runs `unserialize()` on the `calculation` column. The column is populated by `setCalculationAttribute(serialize($request->calculation))` from user input. Classic PHP-object-injection vector — any class with an unsafe `__wakeup` / `__destruct` in the loaded autoload graph becomes reachable.

### CC16. **Major — public `/tour/{id}/landingpage` is fully enumerable** ([routes/web.php:817](routes/web.php#L817))
No auth, no share-token. `Tour::findOrFail($id)` on a sequential auto-increment id. Anyone with a base URL can scrape every tour, including supplier contact phones/faxes, internal status names, and `public/system/App/File/attaches/000/000/<padded_id>/original/<filename>` attachment paths.

---

## Tours

### Files
- **Controllers:** `app/Http/Controllers/TourController.php` (~1700 LOC), `app/Http/Controllers/TourControllerImproved.php` (orphan, not routed), `app/Http/Controllers/Api/TourApiController.php`.
- **Models:** `app/Tour.php`, `app/TourDay.php`, `app/TourPackage.php`, `app/TourRoomTypeHotel.php`, `app/Childrens.php`.
- **Views:** `resources/views/tour/{index,create,edit,show,packages,monthly_chart}.blade.php`, plus `tour/index.blade.php.backup`.
- **Routes:** `routes/web.php:282-308`, `routes/web.php:817` (landing).
- **Repositories:** `app/Repository/Contracts/TourRepository.php`, `app/Repository/TourRepository/EloquentTourRepository.php`.
- **Services:** `app/Services/TourService.php` (modern, unused by controller), `app/Helper/TourPackage/TourService.php` (the one actually used).
- **Requests:** `app/Http/Requests/StoreTourRequest.php`, `app/Http/Requests/UpdateTourRequest.php`, plus duplicate pair under `app/Http/Requests/Tour/`.

### Tests
- `tests/Feature/TourApiTest.php`
- `tests/Unit/TourServiceTest.php`

### Dead code / TODOs
- 100+ lines of commented-out duplicate `store()` ([TourController.php:953-1139](app/Http/Controllers/TourController.php#L953)).
- Commented `hasRole('admin')` checks at [TourController.php:152-160, 216-218, 276-280, 2721-2723](app/Http/Controllers/TourController.php#L152).
- Leftover `// dd(...)` at [TourController.php:1272, 1434, 1844, 2367, 2533](app/Http/Controllers/TourController.php#L1272).
- Method-name typo `createUpdateTourDatesss` at [TourController.php:2389-2407](app/Http/Controllers/TourController.php#L2389).
- 30+ lines commented `cloneTourDays` logic at [TourController.php:2523-2556](app/Http/Controllers/TourController.php#L2523).
- `app/Http/Controllers/TourControllerImproved.php` entire file is dead.
- `app/Services/TourService.php` references relations (`tourPackages`, `responsibleUsers`, `assignedUsers`) and `tour_id` on TourDay that **do not exist** — would crash if invoked.

### Critical
- [Landing-page route is public, no auth] `Route::get('/tour/{id}/landingpage', ...)` is outside any middleware group and the controller's constructor excepts `landingPage` from `auth` / `preventBackHistory` / `permissions.required`. ([routes/web.php:817](routes/web.php#L817), [TourController.php:96-97](app/Http/Controllers/TourController.php#L96))
- [`StoreTourRequest::authorize()` returns true unconditionally] Same in `UpdateTourRequest`. Authorization gate doesn't exist at the request level. ([StoreTourRequest:15-18](app/Http/Requests/StoreTourRequest.php#L15), [UpdateTourRequest:16-19](app/Http/Requests/UpdateTourRequest.php#L16))
- [Tour model uses `$guarded = []`] Mass-assignment open on `Tour`, `Comparison`, `ComparisonRow`. ([app/Tour.php:77](app/Tour.php#L77))
- [`update()` calls `->save()` on an integer when `$request->fieldName == 'status' && fieldValue == 39`] `$tour` is the route id (integer), not a model — fatal error path; only the `else` branch updates `$tour_model`. ([TourController.php:1985-1986](app/Http/Controllers/TourController.php#L1985))
- [`prepareTourPackages` overwrites `$package->status` with the *name* string] Show view then reads `$package->status->id` and produces "Trying to get property of non-object." ([TourController.php:1693-1695](app/Http/Controllers/TourController.php#L1693))
- [`exportPdfVoucher` references undefined `$package` inside the transfers loop] Voucher PDF crashes whenever a tour has transfers. ([ExportTrait.php:107-110](app/Helper/ExportTrait.php#L107))
- [`destroy()` has no ownership check] Any authenticated user with the `tour.destroy` permission can delete any tour and cascade-delete Quotations / ClientInvoices / InvoicesTours / BusDays / TransferToDrivers / TransferToBuses. ([TourController.php:2281-2340](app/Http/Controllers/TourController.php#L2281))

### Major
- [`index()` paginates in PHP after `->get()` of all tours] ([TourController.php:560](app/Http/Controllers/TourController.php#L560))
- [`create()`/`edit()` load entire supplier catalogs into a dropdown] `Hotel::all()`, `Event::all()`, `Restaurant::all()`, `Guide::all()` for both create and edit forms. ([TourController.php:656-660](app/Http/Controllers/TourController.php#L656), [:1868-1872](app/Http/Controllers/TourController.php#L1868))
- [N+1 in frontsheet] `$tourDay->hotels()`, `$hotel->hotel_offers`, `$hotel->latestHotelOffer->offersWithRoomPrice(...)` per row, no eager loading. ([tour/show.blade.php:424-514](resources/views/tour/show.blade.php#L424))
- [N+1 in DataTables `addColumn('link', ...)`] `TourDay::where('tour', $tour->id)->first()` per row in `tour_achieve_data`, `monthly_chart_data`, `cancelled_chart_data`, `client_data`, `quotation_data`. ([TourController.php:202, 264, 326, 380, 2750](app/Http/Controllers/TourController.php#L202))
- [Duplicate routes for same action] `Route::post('tour/save', ...)`, `Route::resource('tour', ...)`, and `Route::post('tour/{id}/update', ...)` all coexist. ([routes/web.php:282-284](routes/web.php#L282))
- [`generateExternalName` is not unique] Returns `'EETS' . $country_code . (100 + $id)` but `external_name` is declared unique-on-update. Cross-country cloning collides. ([TourController.php:1143](app/Http/Controllers/TourController.php#L1143))
- [`updateStatus`/`update_voucherid`/`update_itnid` lack permission scoping] AJAX endpoints flip status, voucher, itinerary flags on any id passed. ([TourController.php:2812-2841, 2886-2896](app/Http/Controllers/TourController.php#L2812))
- [`pdfData` double-encodes JSON] `response()->json(json_encode($data))`. ([TourController.php:1735](app/Http/Controllers/TourController.php#L1735))
- [TourController doesn't use TourRepository] Injects it; never calls it. ([TourController.php:91-95](app/Http/Controllers/TourController.php#L91))
- [Editable `cityName`/`countryAlias` AJAX path creates `cities` rows unconditionally] Spam vector. ([TourController.php:1937-1945](app/Http/Controllers/TourController.php#L1937))
- [`destroy()` issues `Comment::where('reference_id',$id)->delete()` — hard delete bypassing soft-delete on Comment] ([TourController.php:2304](app/Http/Controllers/TourController.php#L2304))
- [Two parallel `StoreTourRequest` classes] Controller imports root one; `app/Http/Requests/Tour/StoreTourRequest.php` is dead. ([app/Http/Requests/Tour/StoreTourRequest.php](app/Http/Requests/Tour/StoreTourRequest.php))

### Minor
- [Empty try/catch around offices loading silently swallows errors] ([TourController.php:1276-1281](app/Http/Controllers/TourController.php#L1276))
- [`index.blade.php.backup` checked into the repo] ([resources/views/tour/index.blade.php.backup](resources/views/tour/index.blade.php.backup))
- [`docExport` no date guards] `Carbon::createFromFormat('Y-m-d', $tour->departure_date)` throws if dates malformed; `pdfExport` has the guard, `docExport` does not. ([TourController.php:2797-2798](app/Http/Controllers/TourController.php#L2797))
- [`days_dropdown` builds HTML server-side] Concatenates `$tour->name` / `$tourDate->date` into HTML returned to JS — XSS surface if those fields are user-editable. ([TourController.php:2842-2885](app/Http/Controllers/TourController.php#L2842))
- [Five near-identical DataTables endpoints] `tour_achieve_data`, `monthly_chart_data`, `cancelled_chart_data`, `client_data`, `quotation_data` are copy-paste with minor where-clauses. ([TourController.php:150-396, 2716-2759](app/Http/Controllers/TourController.php#L150))

---

## Itineraries

> Not a separate controller — it's a tour concept implemented through PDF / DOC / HTML / Excel exports and a free-text `tour.itinerary_tl` field.

### Files
- **Controllers:** `app/Http/Controllers/TourController.php` (`pdfExport`, `htmlExport`, `docExport`, `pdfData`, `landingPage`, `export`).
- **Helpers:** `app/Helper/ExportTrait.php`.
- **Views:** `resources/views/export/{bootstrap,bootstrap_hotels,doc,doc_hotels,doc_voucher,export,html,landing_page,package,pdf_hotels,pdf_simple,pdf_voucher}.blade.php`.
- **Routes:** `routes/web.php:290, 291, 294, 295, 296, 817`.
- **Field:** `tour.itinerary_tl`, validated in [StoreTourRequest:128](app/Http/Requests/StoreTourRequest.php#L128).

### Tests
_none_

### Dead code / TODOs
- Commented `//return $pdf->download('itinerary_list.pdf');` at [ExportTrait.php:285, 561](app/Helper/ExportTrait.php#L285).
- Unreachable code after `return $response;` referencing undefined `$pdf` at [ExportTrait.php:233-234](app/Helper/ExportTrait.php#L233).
- Large `/* ... */` blocks of legacy filtering at [ExportTrait.php:394-419, 484-512, 521-560](app/Helper/ExportTrait.php#L394).
- `// dd(...)` leftovers at [ExportTrait.php:433, 523](app/Helper/ExportTrait.php#L433).

### Critical
- [`exportPdfVoucher` references undefined `$package` in the transfers loop] Crashes voucher generation when any transfer exists. ([ExportTrait.php:107-110](app/Helper/ExportTrait.php#L107))
- [`landingPage` route bypasses `auth`] Itinerary attachments + full tour record served without login. (CC16; [routes/web.php:817](routes/web.php#L817))
- [`exportVoucherdoc`/`exportHotelsdoc` write to shared `storage_path('app/temp.docx')`] Two concurrent requests race; one user can download another user's voucher. ([ExportTrait.php:219-228, 358-367](app/Helper/ExportTrait.php#L219))
- [`docExport` no date guards] Throws if dates null/malformed. ([TourController.php:2797-2798](app/Http/Controllers/TourController.php#L2797))

### Major
- [HTMLPurifier cache directory created with `@mkdir`] Error suppression hides permission failures; cache silently regenerates per request. ([ExportTrait.php:200-203, 338-341](app/Helper/ExportTrait.php#L200))
- [PDF export silently truncates tours with bad dates] Falls back to `Carbon::now()` and computes nonsensical tour codes. ([TourController.php:1746-1747, 1769-1770](app/Http/Controllers/TourController.php#L1746))
- [`exportPdfHotels` called without `$this->`] Bare function call when `pdf_type === 'hotels'` → `Call to undefined function`. ([TourController.php:1750](app/Http/Controllers/TourController.php#L1750))
- [`landingPage` uses `view()->share(...)`] Leaks variables to other views rendered in the same request. ([TourController.php:1818-1826](app/Http/Controllers/TourController.php#L1818))
- [`pdfData` double-encodes JSON] (CC; [TourController.php:1735](app/Http/Controllers/TourController.php#L1735))

### Minor
- [Assignment-in-return] `return $pdf = $this->exportHtmlShort(...)`. ([TourController.php:1755-1758, 1772](app/Http/Controllers/TourController.php#L1755))
- [`$selectedArray` computed and unused] ([ExportTrait.php:250, 303](app/Helper/ExportTrait.php#L250))
- [Inline `ini_set('upload_max_filesize'...)` mid-method] Has no effect at this lifecycle stage. ([ExportTrait.php:216-217, 355-356](app/Helper/ExportTrait.php#L216))

---

## Frontsheet

> The comparison/quotation grid rendered on the tour show page, plus the dedicated comparison views.

### Files
- **Controllers:** `app/Http/Controllers/ComparisonController.php`; frontsheet tab in `app/Http/Controllers/TourController.php@show`.
- **Models:** `app/Comparison.php`, `app/ComparisonRow.php`, `app/Quotation.php`.
- **Views:** `resources/views/comparison/{show,frontsheet,comments}.blade.php`; frontsheet tab inside `resources/views/tour/show.blade.php:338-635`.
- **Routes:** `routes/web.php:805-806` (only `web` middleware, not `perm`).

### Tests
_none_

### Dead code / TODOs
- Commented auto-set date logic at [ComparisonController.php:139-148](app/Http/Controllers/ComparisonController.php#L139).
- Empty resource stubs at [ComparisonController.php:31-44, 80-89, 163-166](app/Http/Controllers/ComparisonController.php#L31) — should remove resource registration for these verbs.
- Large `{{-- ... --}}` HPP-math comment at [tour/show.blade.php:556-568](resources/views/tour/show.blade.php#L556).
- Empty `<tbody>` left at [tour/show.blade.php:401-418](resources/views/tour/show.blade.php#L401).

### Critical
- [`updateOrInsert` may INSERT row with only `id` + `city_tax`] `TourPackage::updateOrInsert(['id' => $tourDay->firstHotel()->id], ['city_tax' => ...])` — if no row matches, inserts a partial row missing all NOT NULL columns; same for `Hotel::updateOrInsert(...)`. ([ComparisonController.php:122, 126](app/Http/Controllers/ComparisonController.php#L122))
- [`$request->city_tax[$row->id]` indexed without null check after `array_keys(null) ?? []`] `?? []` is on the result of `array_keys`, not its input — TypeError on PHP 8 when `city_tax` not in request. ([ComparisonController.php:114-116](app/Http/Controllers/ComparisonController.php#L114))

### Major
- [Comparison routes only have `web` middleware, no `perm`] Relies on controller-constructor `permissions.required`; combined with CC1/CC2 this means access is uncontrolled. ([routes/web.php:804-807](routes/web.php#L804))
- [`syncComparisonRows` issues per-day SELECT + INSERT on every tour show()] N+1 against `comparison_rows`. ([TourController.php:1269, 2764-2781](app/Http/Controllers/TourController.php#L1269), [ComparisonController.php:168-185](app/Http/Controllers/ComparisonController.php#L168))
- [Comparison `update()` saves N rows in a tight loop with no transaction] Partial saves possible. ([ComparisonController.php:103-149](app/Http/Controllers/ComparisonController.php#L103))
- [`AdminHelper::getComparisonRowCommentsCount(...)` called per-row in view] One COUNT query per tour day. ([tour/show.blade.php:542](resources/views/tour/show.blade.php#L542))
- [`Comparison` & `ComparisonRow` use `$guarded = []`] (CC5)
- [Frontsheet view uses AdminLTE `box-*` + `pull-right` + `data-dismiss` (Bootstrap 4) inside Tabler shell] ([comparison/show.blade.php:9-21](resources/views/comparison/show.blade.php#L9), [comparison/frontsheet.blade.php:17-29, 357](resources/views/comparison/frontsheet.blade.php#L17))
- [Frontsheet show view references `$comparison->comparisonRowByDate(...)->id` without null check in `comparison/frontsheet.blade.php`] (no line guard equivalent to the tour.show wrapper). ([resources/views/comparison/frontsheet.blade.php](resources/views/comparison/frontsheet.blade.php))

### Minor
- [Empty resource controller actions kept] Should be removed and routes scoped to `show`/`update`/`comments` only. ([ComparisonController.php:31-44, 80-89, 163-166](app/Http/Controllers/ComparisonController.php#L31))
- [`syncComparisonRows` duplicated across `ComparisonController` and `TourController`] ([TourController.php:2764-2781](app/Http/Controllers/TourController.php#L2764) vs [ComparisonController.php:168-185](app/Http/Controllers/ComparisonController.php#L168))
- [`App\TourPackage::$roomsPeopleCount` accessed statically from a view] ([comparison/frontsheet.blade.php:52-53](resources/views/comparison/frontsheet.blade.php#L52))
- [Business logic in template] `$ssp = abs($single - $hotelpp)`, `$realBudget = ...` inline in show.blade. ([tour/show.blade.php:523-530](resources/views/tour/show.blade.php#L523))

---

## Services

> Shared catalog of suppliers — Hotel, Event, Guide, Restaurant, Transfer, Bus, Cruise, Flight.

### Files
- **Controllers:** `app/Http/Controllers/ServicesController.php`, `app/Http/Controllers/SupplierSearchController.php`.
- **Models:** `app/Hotel.php`, `app/Event.php`, `app/Guide.php`, `app/Restaurant.php`, `app/Transfer.php`, `app/Bus.php`, `app/Cruises.php`, `app/Flight.php`, `app/ServicesHasCriteria.php`, `app/Criteria.php`, `app/CriteriaType.php`, `app/Rate.php`.
- **Views:** `resources/views/supplier_search/index.blade.php`, `resources/views/component/criterias.blade.php`, `resources/views/component/services_history.blade.php`.
- **Routes:** `routes/web.php:579-582, 594-596`.

### Tests
_none_

### Dead code / TODOs
- `// dd($model);` at [ServicesController.php:86](app/Http/Controllers/ServicesController.php#L86).
- Empty `getServiceForTourPackage($package)` at [ServicesController.php:119-122](app/Http/Controllers/ServicesController.php#L119).
- Commented `// protected $services = [...]` at [SupplierSearchController.php:27](app/Http/Controllers/SupplierSearchController.php#L27).
- Commented `// $namespace = $this->findByCriterias(...)` at [SupplierSearchController.php:105](app/Http/Controllers/SupplierSearchController.php#L105).

### Critical
- [Dynamic class instantiation via `'App\\' . $request->service`] The `else` branch passes the user-controlled class string directly into `::leftJoin` without going through `getValidServices()`. Crafted `?service=User` joins arbitrary tables. ([SupplierSearchController.php:63-99](app/Http/Controllers/SupplierSearchController.php#L63))
- [`generateTableServiceList` uses `service_type_id` as array index] `$this->tour_package->servicesTypes[$request->service_type_id]` — undefined-offset crash on out-of-range; class name then instantiated from the value. ([SupplierSearchController.php:155-160](app/Http/Controllers/SupplierSearchController.php#L155))
- [`ServicesController` has no auth/permission middleware in its constructor] Falls back on the outer `perm` group + CC1 to effectively no-op. ([ServicesController.php:13-15](app/Http/Controllers/ServicesController.php#L13))

### Major
- [`getCollection` loads ALL rows of every service type with no pagination] Then uniques and pushes into one DataTables payload. `ini_set('memory_limit', '800M')` is the workaround. ([SupplierSearchController.php:121-152, 322-408](app/Http/Controllers/SupplierSearchController.php#L121))
- [`findByCriterias` is O(criteria × matches × N+1)] ([SupplierSearchController.php:204-261](app/Http/Controllers/SupplierSearchController.php#L204))
- [Positional-args call to `findByCriterias` silently drops `city/search/country` filters] Positional args misaligned: `$cityCode=null` becomes `$rate=null` at the callsite. ([SupplierSearchController.php:396](app/Http/Controllers/SupplierSearchController.php#L396))
- [`ServicesController::getAllTourPackageWithService` `groupBy` on non-aggregated columns] Invalid under MySQL `ONLY_FULL_GROUP_BY`. ([ServicesController.php:97-100](app/Http/Controllers/ServicesController.php#L97))
- [N+1 in `ServicesController::data`] `$model::find($data->id)` per row in DataTables addColumn. ([ServicesController.php:38-41](app/Http/Controllers/ServicesController.php#L38))
- [`SupplierSearchController` injects entire `TourPackageController` to access `servicesTypes`] Heavy controller as a "service." ([SupplierSearchController.php:21-25](app/Http/Controllers/SupplierSearchController.php#L21))
- [Empty try-catch with `continue`] Hides tour lookup failures silently. ([ServicesController.php:104-110](app/Http/Controllers/ServicesController.php#L104))
- [LIKE wildcard on raw search input] `'%' . $searchName . '%'` — escapes via binding but allows arbitrary substring search; DoS vector on large tables, no rate limit. ([SupplierSearchController.php:80, 95, 229, 248](app/Http/Controllers/SupplierSearchController.php#L80))
- [Inline `<button>` HTML constructed in controller] `$model->name` injected into `data-service_name="..."` attribute without escaping. ([SupplierSearchController.php:187-194, 412-441](app/Http/Controllers/SupplierSearchController.php#L187))

### Minor
- [`serviceColumn` declared with 1 parameter, invoked with 2] ([SupplierSearchController.php:145, 417](app/Http/Controllers/SupplierSearchController.php#L145))
- [Repetitive `leftJoin('countries')->leftJoin('cities')->select(...)` block duplicated 6+ times] Should be a query scope. ([SupplierSearchController.php](app/Http/Controllers/SupplierSearchController.php))
- [`prepareData` has an awkward `hotel_city` special-case for hotels only] ([ServicesController.php:54-79](app/Http/Controllers/ServicesController.php#L54))

---

## Quotations

### Files
- **Controllers:** `app/Http/Controllers/QuotationController.php`, `app/Http/Controllers/ComparisonController.php`.
- **Models:** `app/Quotation.php`, `app/QuotationRow.php`, `app/QuotationValue.php`, `app/Comparison.php`, `app/ComparisonRow.php`.
- **Views:** `resources/views/quotation/{index,create,edit,pdf,excel,column_type}.blade.php`; `resources/views/comparison/{show,frontsheet,comments}.blade.php`; legend partials.
- **Routes:** `routes/web.php:783-793` (quotation), `:804-807` (comparison).
- **Requests:** none — controller uses raw `Request`.
- **Exports:** inline anonymous Excel class in [QuotationController.php:320-336](app/Http/Controllers/QuotationController.php#L320).
- **JS:** `public/js/quotation.js`, `public/js/utils.js`.
- **Seeders:** `database/seeds/PermissionsQuotation.php`, `PermissionsCalculation.php`, `AdditionalPermissions.php`.

### Tests
_none_

### Dead code / TODOs
- `@ToDo: add removing` at [QuotationController.php:196](app/Http/Controllers/QuotationController.php#L196).
- Empty `store()`/`show()`/`destroy()` stubs at [QuotationController.php:165-167, 176-178, 241-243](app/Http/Controllers/QuotationController.php#L165).
- Duplicate assignment `$quotation->calculation = $request->calculation;` at [QuotationController.php:209](app/Http/Controllers/QuotationController.php#L209).
- `// dd(...)` leftovers at [QuotationController.php:287, 295, 312](app/Http/Controllers/QuotationController.php#L287).
- Empty `if ($export == 'csv')` branch at [QuotationController.php:313-316](app/Http/Controllers/QuotationController.php#L313).
- `console.log` leftovers in `public/js/quotation.js:389, 684, 685, 1453`.
- `{{--{{dump(...)}}--}}` leftovers in [quotation/edit.blade.php:187](resources/views/quotation/edit.blade.php#L187), [quotation/create.blade.php:325](resources/views/quotation/create.blade.php#L325).

### Critical
- [PHP object-injection via `unserialize($calculation)`] (CC15; [Quotation.php:97-105](app/Quotation.php#L97))
- [Excel export silently dropped] `excel()` calls `prepareExport(...)` but discards its return and `return back();` — the xlsx never reaches the browser. ([QuotationController.php:316-318](app/Http/Controllers/QuotationController.php#L316))
- [Front-sheet update writes to wrong table via `updateOrInsert`] (Frontsheet section above; [ComparisonController.php:122, 126](app/Http/Controllers/ComparisonController.php#L122))
- [`setAdditionalPersonsAttribute` crashes on malformed payload] `$item['person']` array access. ([Quotation.php:81-90](app/Quotation.php#L81))

### Major
- [No permission seeded for `quotation.save`, `quotation.update`, `quotation.pdf`, `quotation.excel`, `quotation.confirm`, `quotation.confirm_cancel`] (CC1+CC2; [routes/web.php:784-791](routes/web.php#L784))
- [Mass-assignment via `$guarded = []`] (CC5; [Quotation.php:26](app/Quotation.php#L26))
- [No input validation in `save()` / `update()`] ([QuotationController.php:245-269, 203-232](app/Http/Controllers/QuotationController.php#L245))
- [Money math in floats] ([comparison/show.blade.php:226-236](resources/views/comparison/show.blade.php#L226))
- [`rate` / `mark_up` not validated as numeric] ([QuotationController.php:211](app/Http/Controllers/QuotationController.php#L211))
- [N+1 in `index()`] `Tour::find($quotation->tour_id)` inside per-row `map()` after already left-joining `tours`. ([QuotationController.php:65](app/Http/Controllers/QuotationController.php#L65))
- [N+1 in `goAheadTours` and front-sheet] No eager loading; `$tourDay->firstHotel()` and `$comparison->comparisonRowByDate($tourDay->date)` repeated per row.
- [Hard-coded PDF header data] ATTN/FAX/TEL and `SINGLE SUPPLEMENT: 220 €` and `from Arr.: VIE to dept.: VIE` baked into the template; PDFs are wrong for any non-template tour. ([quotation/pdf.blade.php:69-91, 292-295, 381](resources/views/quotation/pdf.blade.php#L69))
- [No pagination in `index()`] ([QuotationController.php:42-53](app/Http/Controllers/QuotationController.php#L42))
- [Toggle JS broken — `function myfunction(){...}` rendered outside `<script>` tag] At [quotation/index.blade.php:133-164](resources/views/quotation/index.blade.php#L133) the `<script>` closes at 133, then JS code follows in body, then a stray `</script>` at 164.
- [Comparison.show creates rows with `id == quotation id`] Relies on shared primary keys; collisions on delete + recreate. ([ComparisonController.php:70-73](app/Http/Controllers/ComparisonController.php#L70))
- [HTML injection via `tour_link`] `{!! $quotation->tour_link !!}` rendered raw with `$tour->name` interpolated. ([QuotationController.php:68](app/Http/Controllers/QuotationController.php#L68), [quotation/index.blade.php:59](resources/views/quotation/index.blade.php#L59))
- [`Quotation` uses `SoftDeletes` but `destroy()` is empty] Soft-deleted rows unreachable from UI. ([Quotation.php:24](app/Quotation.php#L24), [QuotationController.php:241-243](app/Http/Controllers/QuotationController.php#L241))

### Minor
- [Inconsistent route names] `quotation.add` vs `quotation.add_column_message` vs `quotation.updateQuotation`. ([routes/web.php:784-791](routes/web.php#L784))
- [CSV branch is empty no-op] ([QuotationController.php:313-318](app/Http/Controllers/QuotationController.php#L313))
- [`@date(...)` with `@` error suppression in pdf.blade.php] ([quotation/pdf.blade.php:80, 188, 307, 309](resources/views/quotation/pdf.blade.php#L80))
- [Brittle inline-style colors in `quotation/excel.blade.php`] ([resources/views/quotation/excel.blade.php:60, 73](resources/views/quotation/excel.blade.php#L60))
- [Copy-pasted PHPDoc in QuotationRow/QuotationValue/GuestList claiming Quotation methods] ([QuotationRow.php:13-19](app/QuotationRow.php#L13), etc.)

---

## Guests

### Files
- **Controller:** `app/Http/Controllers/GuestListController.php`.
- **Model:** `app/GuestList.php`.
- **Views:** `resources/views/guest_list/{index,create}.blade.php`, `resources/views/legend/guest_list_legend.blade.php`; room-list partial in `resources/views/tour/show.blade.php:1263-1330`.
- **Routes:** `routes/web.php:795-802`.
- **Seeders:** `database/seeds/AdditionalPermissions.php:23` (partial).

### Tests
_none_

### Dead code / TODOs
- `@ToDo: add removing` at [GuestListController.php:245](app/Http/Controllers/GuestListController.php#L245).
- Commented IMAP setup at [GuestListController.php:26-35](app/Http/Controllers/GuestListController.php#L26).
- Commented sent-folder write at [GuestListController.php:162-163](app/Http/Controllers/GuestListController.php#L162).
- Empty `show()`/`edit()`/`update()`/`destroy()` stubs at [GuestListController.php:206-208, 240-241, 253-255, 265-267](app/Http/Controllers/GuestListController.php#L206).
- Dead `$broken_emails_array` branch never populated. ([GuestListController.php:127-128](app/Http/Controllers/GuestListController.php#L127))

### Critical
- [Index page is permanently broken] View tries to AJAX `{{route('quotation.data')}}` — this route does not exist. Always shows "Error loading data." Controller passes `$guestList` but the view ignores it. ([guest_list/index.blade.php:58-81](resources/views/guest_list/index.blade.php#L58))
- [State-changing actions over GET] `guestlist/{id}/send/...` and `guestlist/{id}/delete/...` are `Route::get(...)` — CSRF-free, prefetchable. (CC4; [routes/web.php:799-800](routes/web.php#L799))
- [Null-pointer fatal on send] `\App\Hotel::find($tourPackage->service()->id)` assumes both are non-null. ([GuestListController.php:139-141](app/Http/Controllers/GuestListController.php#L139))
- [Stored XSS into outbound hotel emails] `roomlist_textarea` raw via `request()->roomlist_textarea`, emailed through `Mail::send('email.mail_template', compact('content'), ...)` without escaping; returned verbatim by `showById()`. ([GuestListController.php:80-87, 103-114, 156-165, 210-214](app/Http/Controllers/GuestListController.php#L80))
- [No permissions seeded for any guestlist action] (CC1+CC2)

### Major
- [Hard-coded sender] `gini@eetstravel.com` baked into controller. ([GuestListController.php:159-160](app/Http/Controllers/GuestListController.php#L159))
- [No validation] `store()` and `send()` accept any input without `validate(...)`. ([GuestListController.php:78-95, 98-185](app/Http/Controllers/GuestListController.php#L78))
- [`hotel_ids` stored as CSV string] `implode(',', $request->hotelIds)`, explode later — no relation, breaks if id contains a comma, can't eager-load. ([GuestListController.php:83, 106](app/Http/Controllers/GuestListController.php#L83))
- [N+1 in `getSelectedHotelNames` / `getSelectedHotelNamesEmails`] Loops `TourPackage::find($id)` and `Hotel::find(...)` per id; tour.show calls `getSelectedHotelNames()` twice per row. ([GuestList.php:40-65](app/GuestList.php#L40), [tour/show.blade.php:1304-1306](resources/views/tour/show.blade.php#L1304))
- [`Mail::failures()` does not catch SMTP exceptions] No try/catch around `Mail::send`; auth failure crashes the request. ([GuestListController.php:156-169](app/Http/Controllers/GuestListController.php#L156))
- [Two saves to write `version = id`] Redundant insert+update for one auto-incremented field. ([GuestListController.php:87-90, 111-114](app/Http/Controllers/GuestListController.php#L87))
- [No pagination on `GuestList::all()`] ([GuestListController.php:48](app/Http/Controllers/GuestListController.php#L48))
- [`$guestList->getAuthor()->name ?? '—'`] In PHP 7 the `->name` on null is fatal before `??` short-circuits; use `optional()`. ([tour/show.blade.php:1294](resources/views/tour/show.blade.php#L1294))

### Minor
- [Inconsistent route name casing] `guestList.add` (camel) vs `guestlist.send`/`guestlist.delete` (lower). ([routes/web.php:796, 799-800](routes/web.php#L796))
- [Index view breadcrumb mislabeled "Quotation / Quotation List"] ([guest_list/index.blade.php:5-8](resources/views/guest_list/index.blade.php#L5))
- [Inline `<script>` in create view registers `roomlist_submit` inside `template_selector_guest.change` — only binds after a template is selected] ([guest_list/create.blade.php:458-515](resources/views/guest_list/create.blade.php#L458))

---

## Invoices

> Three flavors: supplier invoices (money agency owes), client invoices (money clients owe), office-fee invoices.

### Files
- **Models:** `app/Invoices.php`, `app/InvoicesTours.php`, `app/ClientInvoices.php`, `app/Transaction.php`.
- **Controllers:** `app/Http/Controllers/InvoicesController.php` (supplier), `app/Http/Controllers/ClientInvoiceController.php` (client), `app/Http/Controllers/OfficeInvoiceController.php` (office-to-office), `app/Http/Controllers/TransactionController.php` (payments).
- **Views:** `resources/views/invoices/{index,create,edit,show,payment_create}.blade.php`, `resources/views/accounting/{index,create,edit,show,payment_create}.blade.php`, `resources/views/accounting/service_transaction/create.blade.php`, `resources/views/office/office_invoices/{create,office_invoice_detail}.blade.php`, `resources/views/export/accounting/{billingPdf,billingExcel}.blade.php`, `resources/views/export/office_invoices/officeInvoicesPdf.blade.php`, `resources/views/account/index.blade.php`.
- **Routes:** `routes/web.php:97-126, 151-163`.
- **Schema:** `supplier_invoices`, `client_invoices`, `invoices_tours`, `invoice_items`, `transactions`, `office_invoices`, `officeinvoice_data`.
- **Permission slugs:** referenced in [PermissionHelper.php](app/Helper/PermissionHelper.php) but **no permission seeders exist** (CC2).

### Tests
_none_

### Dead code / TODOs
- `dd("ok")` at [InvoicesController.php:314, 473](app/Http/Controllers/InvoicesController.php#L314).
- Commented `destroy()` body at [InvoicesController.php:372-376](app/Http/Controllers/InvoicesController.php#L372) — `destroy()` now only deletes the `InvoicesTours` pivot.
- Commented `$invoices = Invoices::find($id);` in edit at [InvoicesController.php:280](app/Http/Controllers/InvoicesController.php#L280).
- Five `dd(...)` calls in [ClientInvoiceController.php:249, 261, 367, 383, 669](app/Http/Controllers/ClientInvoiceController.php#L249).
- Nine commented `// dd` lines in [ClientInvoiceController.php:167, 259, 263, 381, 385, 430, 462, 667, 671](app/Http/Controllers/ClientInvoiceController.php#L167).
- `dd($e->getMessage())` in production try/catch at [OfficeInvoiceController.php:98, 112](app/Http/Controllers/OfficeInvoiceController.php#L98).
- Reference to non-existent methods `updateDeferredRevenueToSalesRevenue()` / `updatePayableToCash()` at [TransactionController.php:39-40](app/Http/Controllers/TransactionController.php#L39) — every `GET /transaction` page raises `BadMethodCallException`.
- `console.log` leftovers across 17+ view files in invoices/accounting/office_invoices.

### Critical
- [Money columns stored as INT or VARCHAR] (CC3)
- [No ownership check on any invoice or transaction action] (`show`/`edit`/`update`/`destroy`/`add_payment`/`payment_store`/`pdfExport`/`excelExport`). Any authenticated user can read/modify/delete any other office's invoices and payments. ([InvoicesController.php:289-333, 360-378, 449-494](app/Http/Controllers/InvoicesController.php#L289), [ClientInvoiceController.php:157-189, 301-401, 410-418, 504-540, 571-621, 647-689](app/Http/Controllers/ClientInvoiceController.php#L157), [OfficeInvoiceController.php:122-178](app/Http/Controllers/OfficeInvoiceController.php#L122))
- [Mass-assignment of money + FK fields] All four invoice models `$guarded = []`. Controllers `Model::create($request->except(["attach"]))`. Attacker can submit `pay_to`, `amount`, `invoice_id`, `office_id`, `client_id`, `tour_id`, `invoice_no`, even `id`. (CC5)
- [Permission middleware fails open + no permission seeders for any finance route] (CC1+CC2)
- [`ClientInvoiceController` and `TransactionController` bypass auth + permissions middleware in constructor] Neither registers `auth` or `permissions.required`; only protection is the `web,perm` route group. (`InvoicesController:49-54` does register them — others don't.) ([ClientInvoiceController.php:42-50](app/Http/Controllers/ClientInvoiceController.php#L42), [TransactionController.php:21](app/Http/Controllers/TransactionController.php#L21))
- [`InvoicesController::update` deletes all payments unconditionally, then conditionally recreates] If any payment row arrives malformed, the `dd("ok")` branch aborts — leaving the invoice with zero payments. Same in `ClientInvoiceController::update:372, 383`. ([InvoicesController.php:303-329](app/Http/Controllers/InvoicesController.php#L303))
- [Duplicate payment creation on retry] No `DB::transaction` around store/update/payment-store loops; partial failure → retry → duplicate rows. `trans_no` is `uniqid` (millisecond), no DB uniqueness. ([InvoicesController.php:236-249, 461-488](app/Http/Controllers/InvoicesController.php#L236), [ClientInvoiceController.php:253-275, 656-685](app/Http/Controllers/ClientInvoiceController.php#L253))
- [`office_id` / `client_id` / `tour_id` never validated against ownership in `ClientInvoiceController::store`] `validateTransaction` requires `currency` and `tour_id` only; `office_id` is gated by `Schema::hasTable('offices')` which is **always false** because the actual table is `office_fees`. Attacker submits any `office_id` and lands the invoice on it. ([ClientInvoiceController.php:280-300](app/Http/Controllers/ClientInvoiceController.php#L280), [app/Offices.php:25](app/Offices.php#L25))
- [`OfficeInvoiceController::store` has zero validation] Raw `DB::table(...)->insert(...)` with `from_office`, `to_office`, `invoice_no`, `date`, and `data[]` rows. Attacker can shift balances between offices arbitrarily. ([OfficeInvoiceController.php:84-118](app/Http/Controllers/OfficeInvoiceController.php#L84))
- [PDF / Excel exports leak any client invoice] `GET /accounting/{id}/export/{pdf_type}` and `GET /accounting/{id}/excel` resolve via `ClientInvoices::find($id)` with no ownership filter. ([ClientInvoiceController.php:504-540, 571-598](app/Http/Controllers/ClientInvoiceController.php#L504))
- [Dead route to non-existent method] `/officeInvoices/{id}/export/{pdf_type}` (`routes/web.php:155`) targets `OfficeInvoiceController@pdfExport` which doesn't exist — 500 error.

### Major
- [Float summation of payment amounts] `$payment_amount += $paidamount` with `==` comparison to invoice total. Combined with INT storage, both rounds and accumulates IEEE-754 error. ([InvoicesController.php:178-188](app/Http/Controllers/InvoicesController.php#L178))
- [No audit log on payment create/delete/edit] Spatie activitylog is in use elsewhere but not on payments — money movements are unrecoverable post-edit.
- [N+1 in all three index methods] `InvoicesTours::all()`, `ClientInvoices::with(['client'])->get()`, `Transaction::all()` then per-row `find` / `where->sum`. ([InvoicesController.php:64-128](app/Http/Controllers/InvoicesController.php#L64), [ClientInvoiceController.php:52-92](app/Http/Controllers/ClientInvoiceController.php#L52), [TransactionController.php:37-79](app/Http/Controllers/TransactionController.php#L37))
- [No pagination on any index] (CC11)
- [Missing validation on update] `InvoicesController::update` does NOT call `validateInvoice`. ([InvoicesController.php:289](app/Http/Controllers/InvoicesController.php#L289))
- [Payment-store has no amount validation] `paid_amount` not validated numeric / non-negative / `<=` invoice total. ([InvoicesController.php:461-494](app/Http/Controllers/InvoicesController.php#L461), [ClientInvoiceController.php:656-689](app/Http/Controllers/ClientInvoiceController.php#L656))
- [Wrong column referenced in TourController billing block] `client_invoices.total_amount` doesn't exist (actual: `amount_receiveable`); `invoices.amount_payable` doesn't exist on `supplier_invoices`. Both queries QueryException → swallowed by catch → tour-show billing panel silently empty. ([TourController.php:1290-1399](app/Http/Controllers/TourController.php#L1290))
- [`ClientInvoiceController::show` sums on non-existent property] `$transaction_cust->total_amount + $total_amount` is always 0. ([ClientInvoiceController.php:173-181](app/Http/Controllers/ClientInvoiceController.php#L173))
- [`Schema::hasTable('offices')` is always false (table is `office_fees`)] So all office-related guards take the empty branch; invoices are silently created with `office = null`. ([ClientInvoiceController.php:127, 144, 170, 287, 304, 519, 586, 650](app/Http/Controllers/ClientInvoiceController.php#L127))
- [No soft-deletes on money tables] `Invoices`, `ClientInvoices`, `InvoicesTours`, `Transaction` lack `use SoftDeletes`. A destroy permanently removes records.
- [`destroy()` only deletes pivot, not parent or children] Leaves orphan `Invoices` and orphan `Transaction` rows, polluting sums forever. ([InvoicesController.php:368-378](app/Http/Controllers/InvoicesController.php#L368))
- [`ClientInvoiceController::update` doesn't re-check existing transaction sums after shrinking items] Leaves invoice in over-paid state. ([ClientInvoiceController.php:323-349](app/Http/Controllers/ClientInvoiceController.php#L323))
- [No `DB::transaction` wrapper around invoice + items + payments multi-row write] Partial failure leaves half-written invoice. ([ClientInvoiceController.php:223-275](app/Http/Controllers/ClientInvoiceController.php#L223))
- [`getInvoicePayments` route param `{id}` but controller signature `($pay_to, Request $request)`] Variable received is route `{id}` value, used in `if ($pay_to == 2)`. Random behaviour by numeric value. ([ClientInvoiceController.php:629-646](app/Http/Controllers/ClientInvoiceController.php#L629))

### Minor
- [Duplicate route name `officeInvoices.create`] `Route::resource` registers one, then `Route::get('officeInvoices/create/{id}', ...)` clobbers it. ([routes/web.php:152-153](routes/web.php#L152))
- [Literal `$invoices/` string in URL::to call] ([InvoicesController.php:340](app/Http/Controllers/InvoicesController.php#L340))
- [Typo: "Payments Cannot be  Greather than total Amount"] ([ClientInvoiceController.php:216, 335](app/Http/Controllers/ClientInvoiceController.php#L216))
- [Method-name typo `getOfficeInvoicesdeatailsdata`] ([OfficeInvoiceController.php:131](app/Http/Controllers/OfficeInvoiceController.php#L131))
- [`paid = 'Yes'|'No'` string overwrite of boolean] ([ClientInvoiceController.php:482](app/Http/Controllers/ClientInvoiceController.php#L482))

---

## Billings

> Client-billing PDF/Excel pipeline + the billing panel on the tour show page.

### Files
- **Controller entry points:** [ClientInvoiceController.php:504-540](app/Http/Controllers/ClientInvoiceController.php#L504) (`pdfExport`), [:571-621](app/Http/Controllers/ClientInvoiceController.php#L571) (`excelExport`, `prepareExport`).
- **Joined billing block on tour-show:** [TourController.php:1283-1399](app/Http/Controllers/TourController.php#L1283).
- **Views:** `resources/views/export/accounting/billingPdf.blade.php`, `billingExcel.blade.php`.
- **Routes:** `routes/web.php:117-118` (`accounting/{id}/export/{pdf_type}`, `accounting/{id}/excel`).

### Tests
_none_

### Dead code / TODOs
- CSV branch fully commented at [ClientInvoiceController.php:592-595](app/Http/Controllers/ClientInvoiceController.php#L592) — silently returns `back()`.
- `$billingData` built but empty in prod due to wrong column references. ([TourController.php:1390-1399](app/Http/Controllers/TourController.php#L1390))
- Second large block after `return view(...)` is unreachable in the happy path — looks like a botched merge. ([TourController.php:1419-1471](app/Http/Controllers/TourController.php#L1419))

### Critical
- [Export endpoints leak any client invoice] No ownership filter on `ClientInvoices::find($id)`. (See Invoices Critical.)
- [`pdf_type` flows into view selection without allowlist] ([ClientInvoiceController.php:467-501, 504-540](app/Http/Controllers/ClientInvoiceController.php#L467))

### Major
- [Tour-show billing panel silently empty in prod] Wrong column names; QueryException caught and logged. ([TourController.php:1290-1399](app/Http/Controllers/TourController.php#L1290))
- [Excel/PDF rebuild entire tour pricing on each export] No caching; 5+s for large tours. ([ClientInvoiceController.php:504-621](app/Http/Controllers/ClientInvoiceController.php#L504))
- [No throttling on export endpoints] CPU-heavy; trivial DoS.
- [Empty-quotation type mismatch] When `$tour->quotations->where("is_confirm", 1)->first()` is empty, `$quotation = []` (array) but views expect Quotation object → runtime type error. ([ClientInvoiceController.php:509-517, 577-585](app/Http/Controllers/ClientInvoiceController.php#L509))
- [Filename built from raw `$tour->name`] `'Invoice' . $excelName . '.' . $export` after only `str_replace(" ","_",...)`. Browser-side risk if name contains path components. ([ClientInvoiceController.php:602, 620](app/Http/Controllers/ClientInvoiceController.php#L602))

### Minor
- [CSV branch dead] ([ClientInvoiceController.php:592-595](app/Http/Controllers/ClientInvoiceController.php#L592))
- [PDF paper size hard-coded `a3`] ([ClientInvoiceController.php:536](app/Http/Controllers/ClientInvoiceController.php#L536))

---

## Vouchers

> Per-tour PDF/DOC vouchers, plus the `vch` flag on `tour_packages`.

### Files
- **Controllers:** `TourController` (`pdfExport`, `docExport`, `update_voucherid`, `prepareTourPackages`), `InvoicesController` (`prepareTourPackages` with voucher branch), `ClientInvoiceController` (`prepareTourPackages`, `pdfExport`), `TMSClient/TourController` (`prepareTourPackages` with voucher branch).
- **Trait:** `app/Helper/ExportTrait.php` (`exportPdfVoucher`, `exportVoucherdoc`, etc.).
- **Views:** `resources/views/export/{pdf_voucher,doc_voucher,pdf_hotels,doc_hotels,pdf_simple,bootstrap,bootstrap_hotels,landing_page}.blade.php`; voucher dropdown at [tour/show.blade.php:148-159](resources/views/tour/show.blade.php#L148); voucher checkbox at [tour/packages.blade.php:885,900](resources/views/tour/packages.blade.php#L885).
- **Routes:** `routes/web.php:295-296` (`tour_doc_export`, `tour_pdf_export`), `:307` (`/update_voucherid`), `:117` (`accounting_pdf_export`), `:155` (`office_invoices_pdf_export`), `:817` (`landing_page` — no auth).

### Tests
_none_

### Dead code / TODOs
- Commented `// return $pdf->download('tour_voucher_list.pdf');` at [ExportTrait.php:141](app/Helper/ExportTrait.php#L141).
- Unreachable post-return code at [ExportTrait.php:233-234](app/Helper/ExportTrait.php#L233).
- Commented `$package->time_from = Carbon::parse(...)` at [ExportTrait.php:325-327, 272-274](app/Helper/ExportTrait.php#L325).
- Large `/* ... */` blocks at [ExportTrait.php:384-419, 478-498](app/Helper/ExportTrait.php#L384).
- `//echo (count($tourDates))."<br>";` at [TourController.php:1684](app/Http/Controllers/TourController.php#L1684).
- Bare function call `exportPdfHotels($tour,$data,$request)` (missing `$this->`) at [TourController.php:1750](app/Http/Controllers/TourController.php#L1750).
- `//dd($tourDates);` / `//echo (count($tourDates))."<br>";` at [InvoicesController.php:426, 434](app/Http/Controllers/InvoicesController.php#L426).
- `dd("ok")` reachable in `payment_store` at [InvoicesController.php:471-473](app/Http/Controllers/InvoicesController.php#L471).

### Critical
- [Public landing-page route exposes voucher data] (CC16; [routes/web.php:817](routes/web.php#L817))
- [`dd("ok")` reachable in production via crafted payment payload] ([InvoicesController.php:472-473](app/Http/Controllers/InvoicesController.php#L472))
- [Race on shared `storage_path('app/temp.docx')`] (CC for itineraries; [ExportTrait.php:219-228, 359-368](app/Helper/ExportTrait.php#L219))
- [Permission gating UI-only] Voucher dropdown has no `Auth::user()->can(...)` wrapper; routes have no seeded permission; with CC1, anyone with a session can download vouchers. ([tour/show.blade.php:148-159](resources/views/tour/show.blade.php#L148))
- [Bare `exportPdfHotels(...)` call (missing `$this->`)] PHP fatal when `pdf_type === 'hotels'`. ([TourController.php:1750](app/Http/Controllers/TourController.php#L1750))

### Major
- [`$package->vch == 0` filter ambiguous for null] Non-hotel package types have nullable `vch`; loose `== 0` matches null and silently drops services from the voucher PDF. ([ExportTrait.php:117-119, 260-263, 313-316](app/Helper/ExportTrait.php#L117))
- [`prepareTourPackages` duplicated across 4 controllers and already drifting] Any voucher fix must be applied in 4 places. (TourController:1681, InvoicesController:431, ClientInvoiceController:467, TMSClient/TourController:266)
- [`PDF::setOptions` mutates global config without resetting] ([ExportTrait.php:139, 282](app/Helper/ExportTrait.php#L139))
- [No CSRF / ownership check on `/update_voucherid`] Writes `vch` for any TourPackage id supplied; no tour-membership check. ([routes/web.php:307](routes/web.php#L307), [TourController.php:2812-2826](app/Http/Controllers/TourController.php#L2812))
- [Voucher view dereferences nullable service blindly] `$package->service()->address_first` without null guard for orphan packages → fatal in PDF generation. ([export/pdf_voucher.blade.php:99-141](resources/views/export/pdf_voucher.blade.php#L99))
- [HTMLPurifier cache dir created with `@mkdir(...)`] Suppressed errors hide perm failures. ([ExportTrait.php:199-203, 338-342](app/Helper/ExportTrait.php#L199))
- [Hard-coded sender/office] `pdf_voucher.blade.php` and `pdf.blade.php` rely on `Offices::where('status',1)->first()`; if no such office exists, view fatals. ([ExportTrait.php:99, 151, 291, 467](app/Helper/ExportTrait.php#L99))
- [`unset($tour->transfers[$id])` with undefined `$package`] ([ExportTrait.php:107-111](app/Helper/ExportTrait.php#L107))

### Minor
- [Same `voucher_<name>.pdf` filename regardless of date] Re-downloads overwrite each other.
- [`@if (!in_array($package->id, $checkedExcludeVch))` always true] `$checkedExcludeVch` is only populated when `exclude_vch` is truthy. ([export/pdf_voucher.blade.php:114](resources/views/export/pdf_voucher.blade.php#L114))
- [Voucher table widths hard-coded 700px] Doesn't fit A4. ([resources/views/export/pdf_voucher.blade.php:22-37](resources/views/export/pdf_voucher.blade.php#L22))
- [Inconsistent param name: `pdf_type` vs `doc_type`] ([ExportTrait.php:168](app/Helper/ExportTrait.php#L168))

---

## Tasks

### Files
- **Controllers:** `app/Http/Controllers/TaskController.php`, `app/Http/Controllers/Api/TaskApiController.php`, `app/Http/Controllers/Api/UserApiController.php` (tasks method).
- **Model:** `app/Task.php`.
- **Repository:** `app/Repository/Contracts/TaskRepository.php`, `app/Repository/TaskRepository/EloquentTaskRepository.php`.
- **Trait:** `app/Http/Controllers/Traits/ManagesTaskQueries.php`.
- **Scheduled command:** `app/Console/Commands/TaskDeadlineNotification.php`.
- **Views:** `resources/views/task/{index,index_monday,create,edit,show}.blade.php`.
- **Routes:** `routes/web.php:316-352`, `routes/api.php:66, 70-79`.
- **Migrations:** `database/migrations/2017_06_22_113318_create_tasks_table.php`, `2025_10_23_211354_add_monday_fields_to_tasks_table.php`.

### Tests
_none_

### Dead code / TODOs
- `//use Trackable;` commented trait at [Task.php:12](app/Task.php#L12).
- `isOverdueOriginal()` unused duplicate of `isOverdue()` at [Task.php:137-148](app/Task.php#L137).
- Commented `where('dead_line', '<', ...)` and dead `$statusPending` block at [EloquentTaskRepository.php:22, 37, 64-72, 80-82, 115, 126](app/Repository/TaskRepository/EloquentTaskRepository.php#L22).
- Unused `use function GuzzleHttp\Psr7\uri_for;` at [TaskDeadlineNotification.php:13](app/Console/Commands/TaskDeadlineNotification.php#L13).
- Commented `Route::resource('task', 'TasksController');` at [routes/api.php:90](routes/api.php#L90).
- "REPLACED TASK ROUTES" comment banners at [routes/web.php:323-327, 348-350](routes/web.php#L323).
- `✅`-prefixed annotation comments throughout TaskController.

### Critical
- [API IDOR: `/api/users/{user}/tasks` returns any user's tasks] No scoping check — authenticated user can list any other user's tasks. ([UserApiController.php:100](app/Http/Controllers/Api/UserApiController.php#L100))
- [`TaskApiController` writes to non-existent columns] Code uses `deadline`, `status_id`, `tour_id`, `assigned_to`; schema columns are `dead_line`, `status`, `tour`, `assign`. Writes via `Task::create($validated)` and `$task->update($validated)` either fail or persist orphan columns; `overdue()` filters on a column that doesn't exist. Effectively the entire `/api/tasks*` surface is broken. ([TaskApiController.php:41, 50-58, 60, 93-101, 103, 157, 170-174](app/Http/Controllers/Api/TaskApiController.php#L41))
- [XSS via `{!! $task->content !!}` and `{!! $task->task_type !!}`] User-supplied content rendered raw on the task show view. ([task/show.blade.php:33, 45, 60](resources/views/task/show.blade.php#L33))
- [`updateField` allows arbitrary status change without authorization or validation] Any user can mark any other user's task complete via POST `/task/{id}/update-field` with `field=status` and any `value` — no `exists:statuses,id`, no ownership check. ([TaskController.php:373-402](app/Http/Controllers/TaskController.php#L373))

### Major
- [No ownership / office scope on task edit/update/delete/calendar-update] All `findOrFail($id)` and mutate without ownership check. ([TaskController.php:254-318, 332-345, 373-402](app/Http/Controllers/TaskController.php#L254))
- [No validation that `assigned_user` is in same office] ([TaskController.php:128-168, 254-291](app/Http/Controllers/TaskController.php#L128))
- [Edit form checkboxes use `name="assigned_user"` not `name="assigned_user[]"`] Only last-checked user submits — multi-assignment broken from edit form. ([task/edit.blade.php:225](resources/views/task/edit.blade.php#L225))
- [`TaskDeadlineNotification` not idempotent] Runs daily; recreates `Notification` rows on every run with no dedupe key. Users get the same notification each day. ([TaskDeadlineNotification.php:77-108](app/Console/Commands/TaskDeadlineNotification.php#L77))
- [`TaskDeadlineNotification` returns `false` on success] Misleading return value plus O(users × tasks × assigned_users) nested loop. ([TaskDeadlineNotification.php:74, 79-105, 111](app/Console/Commands/TaskDeadlineNotification.php#L74))
- [`TaskApiController::index` caches `paginate()` keyed on `$request->all()`] Stale data; unbounded `per_page` allows DoS by huge page size. ([TaskApiController.php:21-44](app/Http/Controllers/Api/TaskApiController.php#L21))
- [API catch blocks return `$e->getMessage()` to client] Leaks internals. ([TaskApiController.php:71-77, 114-120, 134-140, 185-191](app/Http/Controllers/Api/TaskApiController.php#L71))
- [Hardcoded status numeric `2` for "Pending"] Across 7+ controllers — couples logic to a magic ID. (EloquentTaskRepository, BookingRequestController, TourPackageController, OfferController, ScaffoldInterface\AppController)
- [`isOverdue()` ignores completion status] Returns true for completed tasks past deadline; the correct `isOverdueOriginal()` is never called. ([Task.php:61-67](app/Task.php#L61))
- [Timezone-naive overdue logic] Server default tz used everywhere. ([Task.php:66, 117](app/Task.php#L66), [TaskDeadlineNotification.php:59-60, 92-94](app/Console/Commands/TaskDeadlineNotification.php#L59))
- [`TaskController::index` lists ALL tasks regardless of user/office] Multi-tenant leak. ([TaskController.php:62-109](app/Http/Controllers/TaskController.php#L62))
- [`updateField` priority/sort_order not validated as integer / bounded] ([TaskController.php:379-385](app/Http/Controllers/TaskController.php#L379))
- [N+1 in `EloquentTaskRepository::allForAssignedToTour`] No eager load before iterating `$task->assigned_users`. ([EloquentTaskRepository.php:78-101](app/Repository/TaskRepository/EloquentTaskRepository.php#L78))
- [`task/{id}/delete` is GET] (CC4; [routes/web.php:341, 343](routes/web.php#L341))
- [Search is client-side only] DOM filter on a paginated page — searching past page 1 silently broken. ([task/index_monday.blade.php:1329-1341](resources/views/task/index_monday.blade.php#L1329))
- [Inline edit has no loading / error feedback] `console.log` on success, `alert()` on error. ([task/index_monday.blade.php:1150-1172](resources/views/task/index_monday.blade.php#L1150))

### Minor
- [Duplicate `tour()` and `tourModel()` relationships] ([Task.php:69-77](app/Task.php#L69))
- [`getStatusName()` / `getStatusColor()` do `Status::find($this->status)` per call] N+1 in index loop. ([Task.php:49-59](app/Task.php#L49))
- [Mixed `GET delete` and `DELETE` for task destroy] ([routes/web.php:341-345](routes/web.php#L341))
- [`✅` emoji comments throughout TaskController] ([TaskController.php:56, 66, 154-156, 182, 287-289, 340-344, 370-372](app/Http/Controllers/TaskController.php#L56))
- [`findAssignedPending` builds a query but returns void] Missing `return`. ([EloquentTaskRepository.php:119-127](app/Repository/TaskRepository/EloquentTaskRepository.php#L119))

---

## Exports

> Cross-cutting PDF/Word/Excel export pipeline used by most controllers.

### Files
- **Controllers:** `app/Http/Controllers/ExportController.php`, plus `pdfExport`/`docExport`/`htmlExport`/`landingPage` methods in `TourController`, `ClientInvoiceController` (pdfExport/excelExport/prepareExport), `OfficeInvoiceController` (pdfExport), `QuotationController` (prepareExport).
- **Trait:** `app/Helper/ExportTrait.php`.
- **Used by:** `EmployesSalaryController`, `BalanceAmountController`, `ClientInvoiceController`, `InvoicesController`, `OfficeController`, `OfficeEarningController`, `OfficeInvoiceController`, `TourExpenseController`, `UtilityExpenseController`, `TourController`.
- **Views:** `resources/views/export/{bootstrap,bootstrap_hotels,doc,doc_hotels,doc_voucher,export,html,landing_page,package,pdf_hotels,pdf_simple,pdf_voucher}.blade.php`, `export/accounting/{billingExcel,billingPdf}.blade.php`, `export/office_invoices/officeInvoicesPdf.blade.php`, `component/pdf_export_services.blade.php`.
- **Routes:** `routes/web.php:117, 155, 290, 294-296, 592-593`.

### Tests
_none_

### Dead code / TODOs
- See Itineraries + Vouchers dead-code lists (they overlap with Exports).
- `$data = []; foreach (...) ...` commented block at [ExportController.php:255-259](app/Http/Controllers/ExportController.php#L255).
- Duplicate `$htmlContent2` rendered then never used in `exportDocShort` at [ExportTrait.php:623-640](app/Helper/ExportTrait.php#L623).

### Critical
- [Arbitrary class instantiation via `service_name`] `with(new $service)->getTable();` with no allow-list. Combined with raw `$table` interpolation into `DB::table($table)->leftJoin("$table.city", ...)`, this is class-name → SQL-identifier injection. ([ExportController.php:32-43](app/Http/Controllers/ExportController.php#L32), [HelperTrait.php:128-131](app/Helper/HelperTrait.php#L128))
- [Raw SQL identifier interpolation in `getData`/`getAnotherData`/`getDataHotel`] `$table` is concatenated into `DB::table($table)->leftJoin('cities', "{$table}.city", ...)->orderBy("${table}.id")`. ([ExportController.php:218-228, 242-260, 262-284](app/Http/Controllers/ExportController.php#L218))
- [Tour exports have no ownership check] `pdfExport`, `docExport`, `htmlExport`, `landingPage`, `export` all `Tour::findOrFail` and dump the whole tour PDF with linked invoice data — any staff user can download any tour's voucher. ([TourController.php:1737-1772, 1775-1827, 1835-1851, 2793-2810](app/Http/Controllers/TourController.php#L1737))
- [Shared tempfile race for docx exports] `storage_path('app/temp.docx')` used by 3 methods and `unlink()`'d after send. Under load, user A can receive user B's docx. ([ExportTrait.php:219, 358, 665](app/Helper/ExportTrait.php#L219))
- [Stored XSS in export templates via raw `{!! ... !!}`] User-controlled fields rendered unescaped: `{!! $tour->phone !!}` ([bootstrap.blade.php:1068](resources/views/export/bootstrap.blade.php#L1068)), `{!! $package->name !!}` (:1227), `{!! $package->description !!}` (:1235), `{!! $srv->address_first !!}` (:1262), `{!! $srv->code !!}` (:1264), `{!! $menus !!}` (:1320), `{!! $packageDescription !!}` ([landing_page.blade.php:333](resources/views/export/landing_page.blade.php#L333)). HTML export serves live HTML (stored XSS); PDF path doesn't purify; only DOC path uses HTMLPurifier.
- [`exportSeasons` GET endpoint is destructive] Creates `Seasons` + `SeasonsPricesRoomTypeHotels` rows for hotels without seasons on every call. ([ExportController.php:57-189](app/Http/Controllers/ExportController.php#L57))
- [`exportSeasons` uses Maatwebsite v2 API removed in installed v3] `\PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp` and `Excel::create("seasons", function ...)` don't exist; endpoint throws fatal. ([ExportController.php:77-79, 81](app/Http/Controllers/ExportController.php#L77))
- [`exportPdfHotels` called without `$this->`] Bare function call → `Call to undefined function`. ([TourController.php:1750](app/Http/Controllers/TourController.php#L1750))
- [`exportPdfVoucher` uses undefined `$package`] ([ExportTrait.php:107-111](app/Helper/ExportTrait.php#L107))

### Major
- [No request validation on any export endpoint] `pdfExport`, `docExport`, `htmlExport`, `export`, `landingPage`, `ExportController::export` read `pdf_type`, `doc_type`, `exclude_vch`, `exclude`, `service_name`, `column`, `search`, `type` without `validate(...)`.
- [Filename from raw `$tour->name` reaches `Content-Disposition`] CRLF / header injection / path traversal. Only `str_replace(' ', '_', ...)` is applied. ([ExportTrait.php:32, 65, 75, 84, 94, 144-145, 233-234, 286-287, 573-574](app/Helper/ExportTrait.php#L32))
- [`docExport` crashes on tours without dates] ([TourController.php:2797-2798](app/Http/Controllers/TourController.php#L2797))
- [Memory/time-limit overrides everywhere as workaround for fragile pipeline] `ini_set('memory_limit', '800M')`, `set_time_limit(0)`, `ini_set('upload_max_filesize','62M')` scattered through every export method. PDF/DOC fully buffered, never streamed.
- [Soft 300-item cap in PDF export, no UI feedback, doesn't apply to Excel/CSV] ([ExportController.php:49-50](app/Http/Controllers/ExportController.php#L49))
- [N+1 queries in every export path] `TourDay::where('tour', $tour->id)->get()->sortBy('date')` with no `with('packages.status')` — repeated in 7 export methods. ([ExportTrait.php:101, 113-122, 249, 255-263, 302, 308-317, 384, 478, 514-530, 591, 603-618](app/Helper/ExportTrait.php#L101))
- [`view()->share(...)` pollutes global view scope] ([ExportTrait.php:129-138, 280, 443-450, 533-541](app/Helper/ExportTrait.php#L129))
- [HTMLPurifier config recreated per request via `@mkdir`] Silenced perm errors. ([ExportTrait.php:197-203, 336-342, 643-649](app/Helper/ExportTrait.php#L197))
- [`exportSeasons` cross-year season ranges ignore tour timezone] ([ExportController.php:117-132](app/Http/Controllers/ExportController.php#L117))
- [`getAnotherData`/`getData`/`getDataHotel` orWhere without parenthesized scoping] ([ExportController.php:244-260, 218-228, 262-278](app/Http/Controllers/ExportController.php#L244))
- [`getData` filters `$d->deleted_at` in PHP after `get()`] Defeats DB-side filtering. ([ExportController.php:229-233, 279-283](app/Http/Controllers/ExportController.php#L229))
- [`pdf_export_services` view receives raw DB rows for any service-model] Combined with `{!! !!}` patterns is a stored-XSS sink. ([ExportController.php:51-54](app/Http/Controllers/ExportController.php#L51))

### Minor
- [Tab/space whitespace inconsistency throughout ExportTrait]
- [Typo: `"Tour Itenary.docx"` (Itinerary)] ([ExportTrait.php:671](app/Helper/ExportTrait.php#L671))
- [`prepareExport` defined in QuotationController and ClientInvoiceController with different signatures from the trait's `prepareExport`] Same-name collision. ([QuotationController.php:320](app/Http/Controllers/QuotationController.php#L320), [ClientInvoiceController.php:599](app/Http/Controllers/ClientInvoiceController.php#L599))
- [`Excel::create("seasons", ...)->download($exp)` uses user-supplied `$exp` as file extension] No allowlist. ([ExportController.php:75, 81, 188](app/Http/Controllers/ExportController.php#L75))

---

## Offices

### Files
- **Models:** `app/Offices.php`, `app/Office_Balance.php`, `app/Office_Earnings.php`, `app/Office_Employes_Salary.php`, `app/Office_Tours.php`, `app/Office_Utility_Expenses.php`.
- **Controllers:** `OfficeController`, `OfficeEarningController`, `EmployesSalaryController`, `TourExpenseController`, `UtilityExpenseController`, `BalanceAmountController`.
- **Views:** `resources/views/office/{index,create,edit,show}.blade.php`, `office/{office_balance,office_earning,employe_salary,tour_expenses,utility_expenses}/{create,edit}.blade.php`.
- **Routes:** `routes/web.php:141-212`.
- **Schema:** `office_fees`, `office_balance`, `office_earnings`, `office_employes_salary`, `office_tours`, `office_utility_expenses`.

### Tests
_none_

### Dead code / TODOs
- `index()` stubs in [EmployesSalaryController.php:52-55](app/Http/Controllers/EmployesSalaryController.php#L52), [UtilityExpenseController.php:52-55](app/Http/Controllers/UtilityExpenseController.php#L52) that just re-render the office index.
- `getButton` builds HTML with stray closing `</div>` (no opening) across six office sub-controllers.
- Commented `office_invoices` SQL at [OfficeInvoiceController.php:124-125](app/Http/Controllers/OfficeInvoiceController.php#L124).
- Dead `except => 'landingPage'` clauses (CC14).

### Critical
- [Mass-assignment of `office_id` across all sub-resources] `$guarded = []` on `Office_*` models; controllers call `Model::create($request->except(["attach"]))` and `$model->update(...)`. Any authenticated user can post `office_id=<other office>` and attribute expenses / steal earnings. (CC5; controllers at [BalanceAmountController.php:80-93](app/Http/Controllers/BalanceAmountController.php#L80), [OfficeEarningController.php:71-92](app/Http/Controllers/OfficeEarningController.php#L71), etc.)
- [No ownership check on edit/update/destroy] All six office sub-controllers do `Model::find($id)` then mutate. ([BalanceAmountController.php:85-110](app/Http/Controllers/BalanceAmountController.php#L85), [OfficeEarningController.php:84-108](app/Http/Controllers/OfficeEarningController.php#L84), [EmployesSalaryController.php:89-115](app/Http/Controllers/EmployesSalaryController.php#L89), [TourExpenseController.php:95-141](app/Http/Controllers/TourExpenseController.php#L95), [UtilityExpenseController.php:98-150](app/Http/Controllers/UtilityExpenseController.php#L98))
- [`OfficeController::store` mass-assignment] `offices::create($request->except(["attach"]))` accepts `status`, `bank_name`, `account_no`, `swift_code`, with no validation. ([OfficeController.php:101-104](app/Http/Controllers/OfficeController.php#L101))
- [Money columns are INT or VARCHAR] (CC3)
- [No Spatie classes registered for office sub-models] [PermissionHelper.php](app/Helper/PermissionHelper.php) only lists `Offices::class`; combined with CC1 every sub-controller route is unguarded.
- [`/{id}/delete` routes are GET] (CC4; [routes/web.php:100, 110, 145, 161, 178, 186, 194, 202, 210](routes/web.php#L100))
- [Bug: `officeInvoices/{id}/delete` deletes an unrelated `Office_Earnings` row] `OfficeInvoiceController@destroy($id)` does `Office_Earnings::find($id)`. ([OfficeInvoiceController.php:171-179](app/Http/Controllers/OfficeInvoiceController.php#L171))

### Major
- [No validation on Office store/update] Empty `office_name`, malformed `account_no`/`swift_code` accepted. ([OfficeController.php:101-115](app/Http/Controllers/OfficeController.php#L101))
- [`Model::find($id); $model->find($id)->delete();` anti-pattern repeated in all six sub-controllers] Crashes on null. ([OfficeController.php:208-228](app/Http/Controllers/OfficeController.php#L208), etc.)
- [`validateData` requires fields but doesn't enforce numeric/positive] User can submit `"abc"` as salary / expense / balance. ([EmployesSalaryController.php:80-88](app/Http/Controllers/EmployesSalaryController.php#L80), [TourExpenseController.php:86-94](app/Http/Controllers/TourExpenseController.php#L86), [UtilityExpenseController.php:90-97](app/Http/Controllers/UtilityExpenseController.php#L90), [BalanceAmountController.php:71-79](app/Http/Controllers/BalanceAmountController.php#L71), [OfficeEarningController.php:76-83](app/Http/Controllers/OfficeEarningController.php#L76))
- [N+1 in `OfficeController::show`] Six `Office_*::where("office_id",...)->get()` plus per-row buttons. ([OfficeController.php:147-185](app/Http/Controllers/OfficeController.php#L147))
- [No soft-deletes on Office sub-models] Permanent deletion; no journal.
- [`Office_Earnings::profit` is `varchar(200)` but summed numerically] `$total_office_earning + $office_earning->profit` truncates non-digit chars. ([OfficeController.php:171-177](app/Http/Controllers/OfficeController.php#L171))
- [`OfficeController::destroy` gate compares `count($office_invoice) != '0'`] PHP-8-strict-comparison hazard. ([OfficeController.php:214-225](app/Http/Controllers/OfficeController.php#L214))

### Minor
- [`redirect()->back()` after store on direct-URL forms can loop to `/`] ([OfficeEarningController.php:74](app/Http/Controllers/OfficeEarningController.php#L74), etc.)
- [`UtilityExpenseController::store` returns `view('office.create')` instead of redirect] Leaks unrelated form. ([UtilityExpenseController.php:88](app/Http/Controllers/UtilityExpenseController.php#L88))
- [Stray tab in flash message] `"Utility Expense $utility_expenses->subject_of_expense\t deleted"`. ([UtilityExpenseController.php:148](app/Http/Controllers/UtilityExpenseController.php#L148))
- [Naming drift: blade folder `office/`, model `Offices`, table `office_fees`] Drove the `Schema::hasTable('offices')` bug noted in Invoices.

---

## Landing page

### Files
- **Controller:** [TourController.php:1775-1827](app/Http/Controllers/TourController.php#L1775) (`landingPage`); constructor at [:91-98](app/Http/Controllers/TourController.php#L91).
- **Route:** [routes/web.php:817](routes/web.php#L817).
- **View:** `resources/views/export/landing_page.blade.php`.
- **Whitelist:** [PermissionsRequiredMiddleware.php:13](app/Http/Middleware/PermissionsRequiredMiddleware.php#L13).
- **Callers:** [tour/show.blade.php:1584, 2117](resources/views/tour/show.blade.php#L1584).
- **Dead `except => 'landingPage'` clauses (CC14):** InvoicesController, TourExpenseController, OfficeEarningController, BalanceAmountController, UtilityExpenseController, EmployesSalaryController, OfficeController, OfficeInvoiceController, TMSClient/TourController.

### Tests
_none_

### Dead code / TODOs
- Commented `{{-- Rooms --}}` block at [landing_page.blade.php:182-186](resources/views/export/landing_page.blade.php#L182).
- Large commented attachment-fallback block at [:250-300, 367-383](resources/views/export/landing_page.blade.php#L250).
- Commented Pickup/Dropoff block at [:327-328](resources/views/export/landing_page.blade.php#L327).
- Commented `$data = $this->prepareTourPackages(...)` at [TourController.php:1788](app/Http/Controllers/TourController.php#L1788).
- `$usersResponsible = User::find($tour->responsible);` shared with view but unused. ([TourController.php:1814](app/Http/Controllers/TourController.php#L1814))

### Critical
- [IDOR — sequential auto-increment ids, no token, no auth] Anyone with a base URL can scrape every tour by iterating `/tour/1/landingpage`, `/tour/2/landingpage`, etc. (CC16; [TourController.php:1778](app/Http/Controllers/TourController.php#L1778), [create_tours_table.php:17](database/migrations/2017_06_22_105518_create_tours_table.php#L17))
- [Constructor `except => 'landingPage'` for `auth`, `preventBackHistory`, `permissions.required`] All three guards explicitly skipped; whitelist in `PermissionsRequiredMiddleware::$ignoredRoutes` confirms intent. ([TourController.php:96-97](app/Http/Controllers/TourController.php#L96))
- [Supplier `work_phone` / `work_fax` rendered for every package] Internal supplier contacts exposed via public URL. ([landing_page.blade.php:323-325](resources/views/export/landing_page.blade.php#L323))
- [`{!! $packageDescription !!}` raw HTML rendered on a public page] Stored XSS to any client who opens an attacker-crafted tour. ([landing_page.blade.php:333](resources/views/export/landing_page.blade.php#L333))
- [Deterministic / enumerable attachment paths] `asset('system/App/File/attaches/000/000/' . str_pad($img['id'], 3, '0', STR_PAD_LEFT) . '/original/' . $img['file_name'])` lets anyone walk attachment IDs without auth. ([landing_page.blade.php:245, 362](resources/views/export/landing_page.blade.php#L245))

### Major
- [No rate limit] No `throttle:*` on the public route. ([routes/web.php:817](routes/web.php#L817))
- [No-cache headers missing] `preventBackHistory` is excepted, so the no-cache headers don't apply; browser back / proxy cache can leak previously viewed tour. ([TourController.php:96](app/Http/Controllers/TourController.php#L96))
- [Bootstrap 4 from third-party CDN with no SRI] Supply-chain risk + breaks visual consistency with the BS5 staff app. ([landing_page.blade.php:11-14](resources/views/export/landing_page.blade.php#L11))
- [External CDNs without integrity attributes] jQuery 3.3.1, popper 1.14.0, Bootstrap 4.1.0. ([landing_page.blade.php:11-14](resources/views/export/landing_page.blade.php#L11))
- [Internal workflow status name exposed] `$package->getStatusName()` rendered ("Pending", "Cancelled" etc.) for end-client view. ([landing_page.blade.php:318](resources/views/export/landing_page.blade.php#L318))
- [No graceful 404 — uses staff app's default error view] ([TourController.php:1778](app/Http/Controllers/TourController.php#L1778))
- [No print stylesheet / save-as-PDF / share-link] Client-facing artifact missing basic affordances. ([landing_page.blade.php:1-394](resources/views/export/landing_page.blade.php#L1))
- [Hard-coded English strings] Mixed with `trans('main.*')` elsewhere; "Tour name", "Dep Date - Ret Date", "Image for landing page" English-only. ([landing_page.blade.php:173, 177, 189](resources/views/export/landing_page.blade.php#L173))
- [Mobile logo broken on narrow viewports] Absolute positioning over container with no top padding. ([landing_page.blade.php:24-37, 163-167](resources/views/export/landing_page.blade.php#L24))
- [Internal `$package->description` rendered raw when `description_package` is set] ([landing_page.blade.php:218, 333](resources/views/export/landing_page.blade.php#L218))

### Minor
- [PHP 8.1+ deprecated `formatLocalized('%B %d, %Y (%A)')`] Locale-dependent strftime-style formatter. ([landing_page.blade.php:211](resources/views/export/landing_page.blade.php#L211))
- [Mixed tabs/spaces, doubled spaces in class attributes] ([landing_page.blade.php:166](resources/views/export/landing_page.blade.php#L166))
- [`view()->share()` instead of `view(..., $data)`] Mixed style. ([TourController.php:1818-1826](app/Http/Controllers/TourController.php#L1818))
- [Unbounded `exclude[]` query param accepted on a public GET] Defended only by Blade's `in_array` checks. ([TourController.php:1805-1812](app/Http/Controllers/TourController.php#L1805))
- [Trailing whitespace / blank lines in controller body] ([TourController.php:1777, 1779, 1813](app/Http/Controllers/TourController.php#L1777))

---

## Help

### Files
_no dedicated files for this module._ Exhaustive search across routes, controllers, views, sidebar partials — no `HelpController`, no `help.index` route, no `resources/views/help/` directory, no "Help"/"Documentation" sidebar entry. Unrelated matches: `app/Helper/helpers.php`, `app/Helper/HelperTrait.php`, `resources/views/vendor/laravel-form-builder/help_block.php`, `public/css/font-awesome-4.7.0/HELP-US-OUT.txt`, `public/assets/plugin/richtexteditor/runtime/help.htm`.

### Tests
_none_

### Dead code / TODOs
_none_ (module does not exist; nothing to flag)

### Critical
- [Help module absent] No Help / Documentation surface anywhere in the application for staff, client, or supplier portals. Either explicitly drop Help from project scope OR scaffold a minimal `HelpController` + `resources/views/help/index.blade.php` route gated by `perm` middleware with a `PermissionsHelpSeeder` and a sidebar entry. **This is an open product question — see "Open questions" below.**

### Major
_none_

### Minor
_none_

---

## Recommended fix order

Your spec proposes Tours → Itineraries → Services → Quotations → Guests → Invoices → Billings → Vouchers → Frontsheet → Tasks → Exports → Offices → Landing → Help. Given the cross-cutting findings, I recommend re-ordering to **fix the systemic bugs first**, because:

- Every per-module "missing permission check" finding is moot until CC1 + CC2 are fixed.
- Every per-module "money math wrong" finding is moot until CC3 is fixed.
- Every per-module "GET delete" finding is moot until CC4 is fixed.
- Every per-module "mass-assignment" finding is moot until CC5 is fixed.

Suggested order:

1. **Phase 0 — systemic** (one PR per item, all touching multiple modules):
   1. CC1 fix the permission middleware fail-open.
   2. CC2 add missing permission seeders + assign to roles.
   3. CC4 convert GET-delete routes to DELETE with CSRF; ship the Ajaxis delete-confirmation modal update.
   4. CC5 add explicit `$fillable` to every model that touches money, auth, or user-controlled data.
   5. CC3 migrate money columns from INT/VARCHAR → DECIMAL(15,2); add a one-time backfill migration.
   6. CC6 add FK constraints + indexes on FK columns for `transactions`, `supplier_invoices`, `client_invoices`, `invoice_items`, `tasks`, `comparison_rows`, `guest_list`, `office_*` tables.
   7. CC12 remove `dd(...)` reachable from production paths.
2. **Phase 1 — modules** in your proposed order, now mostly small (each module reduces to its module-specific Critical + Major).

If you'd rather follow the original order strictly, I can do that — just expect more risky changes per PR because each module's fix will be larger.

---

## Open questions for you (please resolve before Phase 2)

These need product / engineering direction; I shouldn't guess:

1. **Help module — scaffold or drop?** No code exists. Do you want a minimal in-app help center, or is external docs sufficient and we drop it from scope?
2. **Landing page — is `/tour/{id}/landingpage` intended to be world-readable, or should it gain a per-tour share token?** Current behavior is "anyone can iterate IDs." If you want it public, we still need a non-sequential identifier.
3. **Multi-office isolation — is data per-office (a client invoice in Office A is never readable by Office B's users), or is the agency single-tenant?** Many "no ownership check" findings collapse if it's the latter.
4. **Money precision — what currency precision do you need?** I'm assuming DECIMAL(15,2) is sufficient. If you handle cryptocurrencies or fractional-cent rates, we need DECIMAL(20,8).
5. **`TourControllerImproved.php` and `app/Services/TourService.php` — delete, or finish?** The "improved" controller is orphaned; the modern `TourService` references columns/relations that don't exist. If you want them, I need direction.
6. **Two `StoreTourRequest` classes — which one is canonical?** ([app/Http/Requests/StoreTourRequest.php](app/Http/Requests/StoreTourRequest.php) vs [app/Http/Requests/Tour/StoreTourRequest.php](app/Http/Requests/Tour/StoreTourRequest.php))
7. **API contract** — modules with both `/foo` (web) and `/api/foo` (Sanctum) — should the Sanctum API be a first-class consumer (mobile? external integrations?), or is it experimental? It affects how thoroughly I rebuild `TaskApiController` (currently using wrong column names — almost certainly never actually used).
8. **Test infrastructure** — `RefreshDatabase` requires a clean test DB. Is `dev_tms` the test DB too, or should I add a `.env.testing` and CI config? Current `phpunit.xml` only sets `APP_ENV=testing`.
9. **`exportSeasons` GET endpoint** — destructive (creates DB rows), uses removed PHPExcel API, behind a normal `perm` route. Should this be deleted, converted to a queued artisan command, or rebuilt for v3 + idempotent?
10. **Frontend rewrite scope** — you mentioned Tailwind in an earlier conversation. Do we continue patching Bootstrap 5 + Tabler, or is a Tailwind migration in scope for Phase 2?
