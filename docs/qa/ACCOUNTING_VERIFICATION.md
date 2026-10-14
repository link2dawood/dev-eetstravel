# Accounting verification

Scope: Billing/client invoices, supplier invoices and payments, transaction ledger, offices, tour expenses, utilities, salaries, earnings, balances, interoffice invoices, taxes, supplier reports, and accounting data shown on tours.

## Changes

- Tax deletion now removes the tax rather than calling the client-deletion repository.
- Client invoice creation and editing use the same server-side VAT calculation. Posted line totals are recalculated; multiple payments retain the correct invoice ID. Extra cost contributes to the invoice total and preserves cents.
- Payments validate dates, amounts and matching row fields. Client/supplier payments cannot exceed the remaining balance. Invoice changes and payment saves use database transactions; editing without a loaded payment form preserves existing payments.
- Paid invoices and offices with linked financial records are protected against deletion.
- Office finance forms validate both creation and editing, save only validated fields, and no longer try to store CSRF tokens. Broken edit routes, missing child index/detail actions and incorrect redirects were corrected.
- Interoffice invoices save header/items atomically, display their stored lines, and support editing and PDF downloads. Their actions operate on office invoices rather than office earnings.
- Billing PDF and Excel downloads use the stored invoice lines, taxes, extra cost and payment balance instead of adding unrelated quotation/tour totals.
- Reporting uses database pagination and filters, respects deleted suppliers, and links to supplier reports. Supplier invoices resolve by service type/reference, and invoice links use the invoice-tour link ID expected by the controller.
- Tour accounting queries use actual database columns and correct supplier invoice link IDs.
- Obsolete DataTables scripts were removed from migrated accounting screens. Payment lists render on the server; tabs, mobile tables, labels, missing values, search, sorting and pagination were corrected.
- Offices and Taxes navigation uses its corresponding permissions. The existing transaction ledger is linked from Accounting. Route aliases map to existing permission names; no permission definitions were added.

## Validation

Completed: **30 regression tests / 55 assertions** and **67 browser/workflow checks**, all passing. [Recorded results](accounting-results.json) include the checked routes and scenarios. Temporary accounts, browser-created invoices and saved sign-in files were removed after verification; the sample dataset below remains.

- `AccountingSafetyTest` and `AccountingPermissionsTest` cover invoice calculations, extra-cost cents, multiple payments, preserving payments, overpayments, malformed payment rows, paid-invoice deletion, tax/client deletion isolation, reporting pagination/deleted suppliers, supplier link IDs, and limited-role write rejection. These tests use an isolated SQLite database.
- `BusSafetyTest` and `BusValidationTest` provide regression coverage for earlier shared-code changes.
- `tests/Browser/accounting-audit.cjs` checks accounting list/create/detail/edit/payment pages, the five office child forms, VAT invoice submission, invalid payments/tax and 390px mobile layouts.
- `tests/Browser/accounting-workflows.cjs` exercises invoice edits, extra-cost cents, payment preservation, ledger payment creation/editing/deletion, all five child updates and validation, office/tax/interoffice CRUD, PDF/Excel file signatures, each supplier report/filter, no-match state, guest rejection and table search/sort/pagination. Pagination is exercised with a five-row page size in the browser state so it remains testable with a small QA dataset.
- Blade compilation, PHP syntax and JavaScript syntax checks accompany the tests. Browser checks block external providers; no real bank transactions or emails are sent.

Generated manual cases in `test-cases.csv` remain a separate suite. These focused automated checks do not mark all generated manual cases as executed.

## Sample data

Run the repeatable seeder in the local test installation:

```powershell
php artisan db:seed --class=AccountingSampleSeeder
```

Find records by `QA SAMPLE` / `QA-SAMPLE`:

| Record | Purpose |
| --- | --- |
| QA SAMPLE Accounting Office / Partner Office | Office finances and interoffice invoices |
| QA SAMPLE Accounting Client / Tour | Isolated invoice relationships |
| QA-SAMPLE-CLIENT-UNPAID | EUR 240.00, paid 0.00, remaining 240.00 |
| QA-SAMPLE-CLIENT-PARTIAL | EUR 240.00, paid 50.00, remaining 190.00 |
| QA-SAMPLE-CLIENT-PAID | EUR 240.00, paid 240.00, remaining 0.00 |
| QA-SAMPLE-SUPPLIER-PARTIAL | Total 300.00, paid 75.00, remaining 225.00 |
| QA-SAMPLE-OFFICE-001 | Interoffice line total 125.50 |
| QA SAMPLE Zero VAT / VAT 20% | Tax selection and calculations |
| QA SAMPLE office child records | Tour expense, utility, salary, earnings and balance examples |

The seeder refreshes its sample office expense/earning/balance values when rerun. It does not replace existing sample invoice payments. Samples are test entries in the local application's tables and appear in local lists/totals.

## Running browser checks

Set `PLAYWRIGHT_MODULE` to an installed Playwright module if it is not resolvable normally. Set `ACCOUNTING_STORAGE_STATE` to a locally saved authenticated admin Playwright state and `ACCOUNTING_MANIFEST` to a JSON file containing the sample IDs: `office`, `partner`, `tour`, `client`, `client_invoice` (unpaid), `supplier_invoice`, `supplier_link`, `tax`, `transaction`, `tour_expenses`, `utility_expenses`, `employes-salary`, `office_earning`, and `office_balance`. [The local installation's sample IDs](accounting-sample-ids.json) can be used directly; regenerate IDs for a different database.

```powershell
node tests/Browser/accounting-audit.cjs
node tests/Browser/accounting-workflows.cjs
php vendor/bin/phpunit --filter 'AccountingPermissionsTest|AccountingSafetyTest|BusSafetyTest|BusValidationTest' tests/Unit
```

Browser scripts mutate labeled sample records and write local results/screenshots under `storage/app`. The workflow script creates and deletes disposable offices, taxes and interoffice invoices; a failed run may leave its clearly labeled disposable records for inspection. The audit creates a disposable browser invoice. Keep session-state files and credentials out of version control.

## Database change

`2026_10_14_000001_preserve_client_invoice_extra_cost_decimals.php` widens `client_invoices.extra_cost` from integer to nullable decimal so fractional costs are not truncated. It was applied to the local installation. Its rollback intentionally retains the decimal column to avoid discarding cents.
