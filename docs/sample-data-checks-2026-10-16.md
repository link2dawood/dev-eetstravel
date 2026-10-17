# Sample data and checks — October 16, 2026

Samples were added and checked in this order: Bus Company, Base Input, Communications, Clients, Accounting.

| Section | Added | Checks |
| --- | --- | --- |
| Bus Company | 3 companies, 6 buses, 6 drivers, 8 trips, 21 calendar days, 2 notes | Company/bus/driver lists, company detail, calendar data, browser sample visibility |
| Base Input | 5 statuses, 3 room types, 3 rates, 4 currencies, 5 criteria | Six listing pages and browser search; existing exchange rates preserved |
| Communications | 6 email templates and a note/reply on a sample tour | Comments search and browser rendering; template controller rendering |
| Clients | 4 clients, 6 contacts, 1 note | Search, create form, detail and edit forms; browser sample visibility |
| Accounting | 3 client invoices and 2 payments | Unpaid/partial/paid balances, 20% VAT, lists/forms/detail pages, browser search, PDF and Excel downloads |

Sample names begin with `QA Sample`. Invoice references begin with `QA-SAMPLE-CLIENT-`. Accounting invoices each total EUR 240; payment amounts are EUR 0, 100 and 240 respectively. The shared sample tour is `QA Sample Section Test Tour`.

The existing account (user ID 1) receives HTTP 403 on `/templates` because it lacks `templates.index`. Permissions were left unchanged. Templates were checked separately at the controller/rendering level; ordinary account access remains restricted. No emails were sent and no live mailbox was tested.

Browser checks found no JavaScript errors on the five section pages or the calendar. Accounting search displayed exactly three sample invoices. PDF and XLSX downloads were checked for valid file signatures.

An Excel-export defect discovered during testing was fixed: worksheet titles now use the internal invoice ID, so long invoice references and Excel-invalid title characters no longer break export. The full invoice reference remains in document content. A regression test exports and opens a workbook containing a long reference with `/` and `:` characters.

35 scoped automated tests passed across BusValidationTest, BusSafetyTest, AccountingSafetyTest, AccountingPermissionsTest and DatabaseErrorResponseTest. These use in-memory SQLite, with a guard preventing application-database use.

Before adding data, a database backup and row fingerprints were saved under `storage/app/sample-data/section-checks/`. After all checks, every pre-existing row across all 113 tables still matched its original fingerprint. Existing tours, permissions and currency exchange rates were not modified. Stale sample tracking files were archived only after confirming that none of their IDs existed in the restored database.

Per-section page statuses and browser results are saved in the same storage directory. The temporary authenticated browser session was revoked after testing.

`scripts/qa-sample-sections.php` provides local-only setup/check phases (`bus`, `base`, `communication`, `clients`, `accounting`, `preservation`). These retain existing samples. Its `backup` phase refuses to overwrite an existing baseline. The browser script accepts `QA_STORAGE_STATE` pointing to a valid authenticated Playwright state file, plus optional `PLAYWRIGHT_MODULE` and `PLAYWRIGHT_EXECUTABLE_PATH` overrides.
