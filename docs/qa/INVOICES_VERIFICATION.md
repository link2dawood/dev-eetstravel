# Supplier invoice follow-up — 2026-10-30

This note records fixes to `/invoices` findings IN1–IN9 in [the main QA report](../../QA-home-dashboard-2026-10-27.md). The observations in Part 32 remain as historical evidence.

## Changes

- Supplier payment validation and displayed balances include the base amount plus extra cost. `supplier_invoices.total_amount` and `extra_amount` now store `DECIMAL(15,2)`; the migration comparisons found no changes to existing values.
- Invoice numbers are unique within an office for application writes. Creation and changed-number edits recheck the constraint while holding the office row lock. Existing duplicate numbers were left in place; an invoice retaining its old number can still be edited.
- Invoice package IDs must be active and belong to one of the submitted tours. The supplier-package dropdown requires `invoices.create`, escapes output and returns 404 for unknown tours.
- Received and due dates are editable and validated. Supplier invoice list/show/edit/archive/payment URLs consistently use the supplier invoice ID. The list is paginated and aggregates all linked tours and services.
- Unpaid supplier invoices and their tour links are archived with staff attribution. Invoices with payments remain undeletable. Updating invoice metadata preserves payment rows.

## Verification

- Focused Unit suite: **238 tests, 1,267 assertions passed**.
- `php scripts/qa-mysql-invoices.php`: **14 checks passed** against local MySQL. It covered cent-precision payment limits, paying the complete extra-cost balance, duplicate and cross-tour validation, bounded list queries, invoice-ID show and missing-record handling, dropdown permission and escaping, dates, payment preservation, and soft archive attribution. Test writes ran in a transaction and rolled back; invoice, link and payment row snapshots matched afterward.
- `php scripts/qa-invoice-extra-cost-schema.php`: existing total and extra-cost values matched after the decimal migrations.
- PHP syntax, Blade compilation and diff whitespace checks passed. Auto-increment counters may advance despite rollback. No real payment was sent or pre-existing invoice archived. Historical duplicate invoice numbers and prior hard deletions were not rewritten or reconstructed. No full authenticated browser walkthrough was performed.
