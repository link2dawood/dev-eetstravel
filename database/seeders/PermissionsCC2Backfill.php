<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * AUDIT.md CC2 — backfill permission rows for modules that routes/web.php
 * exposes via Route::resource but that had no permission seeder until
 * now. Without these rows the PermissionsRequiredMiddleware would throw
 * `PermissionDoesNotExist`; CC1's hardening kept the system usable for
 * admin / Super User roles via $adminRolesBypass, but ordinary roles
 * were locked out of these modules entirely.
 *
 * Run via `php artisan db:seed --class=Database\\Seeders\\PermissionsCC2Backfill`.
 * Each row is upserted via DB::table('permissions')->updateOrInsert so
 * the seeder is safe to re-run and won't duplicate.
 */
class PermissionsCC2Backfill extends Seeder
{
    public static array $permissions = [
        // ---- Accounting / client invoices ------------------------------
        'accounting.index'   => 'Accounting List',
        'accounting.create'  => 'Accounting Create',
        'accounting.edit'    => 'Accounting Edit',
        'accounting.show'    => 'Accounting Show',
        'accounting.destroy' => 'Accounting Delete',
        'accounts.update'    => 'Accounts Update',   // legacy alias name
        'accounts.destroy'   => 'Accounts Delete',   // legacy alias name

        // ---- Supplier invoices ----------------------------------------
        'invoices.index'   => 'Invoices List',
        'invoices.create'  => 'Invoices Create',
        'invoices.edit'    => 'Invoices Edit',
        'invoices.show'    => 'Invoices Show',
        'invoices.destroy' => 'Invoices Delete',
        'invoice.update'   => 'Invoice Update',

        // ---- Transactions (payments) ----------------------------------
        'transaction.index'   => 'Transactions List',
        'transaction.create'  => 'Transactions Create',
        'transaction.edit'    => 'Transactions Edit',
        'transaction.show'    => 'Transactions Show',
        'transaction.destroy' => 'Transactions Delete',

        // ---- Reporting -------------------------------------------------
        'reporting.index'   => 'Reporting List',
        'reporting.create'  => 'Reporting Create',
        'reporting.edit'    => 'Reporting Edit',
        'reporting.show'    => 'Reporting Show',
        'reporting.destroy' => 'Reporting Delete',
        'reporting.update'  => 'Reporting Update',

        // ---- Office + office sub-resources ----------------------------
        'office.index'   => 'Office List',
        'office.create'  => 'Office Create',
        'office.edit'    => 'Office Edit',
        'office.show'    => 'Office Show',
        'office.update'  => 'Office Update',
        'office.destroy' => 'Office Delete',

        'tour_expenses.index'   => 'Tour Expenses List',
        'tour_expenses.create'  => 'Tour Expenses Create',
        'tour_expenses.edit'    => 'Tour Expenses Edit',
        'tour_expenses.show'    => 'Tour Expenses Show',
        'tour_expenses.update'  => 'Tour Expenses Update',
        'tour_expenses.destroy' => 'Tour Expenses Delete',

        'utility_expenses.index'   => 'Utility Expenses List',
        'utility_expenses.create'  => 'Utility Expenses Create',
        'utility_expenses.edit'    => 'Utility Expenses Edit',
        'utility_expenses.show'    => 'Utility Expenses Show',
        'utility_expenses.update'  => 'Utility Expenses Update',
        'utility_expenses.destroy' => 'Utility Expenses Delete',

        'employes-salary.index'   => 'Employees Salary List',
        'employes-salary.create'  => 'Employees Salary Create',
        'employes-salary.edit'    => 'Employees Salary Edit',
        'employes-salary.show'    => 'Employees Salary Show',
        'employes-salary.update'  => 'Employees Salary Update',
        'employes-salary.destroy' => 'Employees Salary Delete',

        'office_earning.index'   => 'Office Earning List',
        'office_earning.create'  => 'Office Earning Create',
        'office_earning.edit'    => 'Office Earning Edit',
        'office_earning.show'    => 'Office Earning Show',
        'office_earning.update'  => 'Office Earning Update',
        'office_earning.destroy' => 'Office Earning Delete',

        'office_balance.index'   => 'Office Balance List',
        'office_balance.create'  => 'Office Balance Create',
        'office_balance.edit'    => 'Office Balance Edit',
        'office_balance.show'    => 'Office Balance Show',
        'office_balance.update'  => 'Office Balance Update',
        'office_balance.destroy' => 'Office Balance Delete',

        // ---- Taxes / settings / templates / menu / comparison ---------
        'taxes.index'   => 'Taxes List',
        'taxes.create'  => 'Taxes Create',
        'taxes.edit'    => 'Taxes Edit',
        'taxes.show'    => 'Taxes Show',
        'taxes.destroy' => 'Taxes Delete',
        'taxes_update'  => 'Taxes Update',

        'settings.index'   => 'Settings List',
        'settings.create'  => 'Settings Create',
        'settings.edit'    => 'Settings Edit',
        'settings.show'    => 'Settings Show',
        'settings.destroy' => 'Settings Delete',

        'templates.index'   => 'Templates List',
        'templates.create'  => 'Templates Create',
        'templates.edit'    => 'Templates Edit',
        'templates.show'    => 'Templates Show',
        'templates.destroy' => 'Templates Delete',

        'menu.index'   => 'Menu List',
        'menu.create'  => 'Menu Create',
        'menu.edit'    => 'Menu Edit',
        'menu.show'    => 'Menu Show',
        'menu.destroy' => 'Menu Delete',

        'comparison.index'  => 'Comparison List',
        'comparison.create' => 'Comparison Create',
        'comparison.edit'   => 'Comparison Edit',
        'comparison.show'   => 'Comparison Show',

        // ---- Users (Spatie management UI) -----------------------------
        'users.index'   => 'Users List',
        'users.create'  => 'Users Create',
        'users.edit'    => 'Users Edit',
        'users.show'    => 'Users Show',
        'users.destroy' => 'Users Delete',

        // ---- Guest list (the actual view file, not the misnamed
        //      guest_list/index.blade.php that's really the Quotation list)
        'guestlist.index'   => 'Guest List List',
        'guestlist.create'  => 'Guest List Create',
        'guestlist.edit'    => 'Guest List Edit',
        'guestlist.show'    => 'Guest List Show',
        'guestlist.destroy' => 'Guest List Delete',

        // ---- Client-side tour package endpoints -----------------------
        'client_tour_package.index'   => 'Client Tour Package List',
        'client_tour_package.create'  => 'Client Tour Package Create',
        'client_tour_package.edit'    => 'Client Tour Package Edit',
        'client_tour_package.show'    => 'Client Tour Package Show',
        'client_tour_package.destroy' => 'Client Tour Package Delete',
    ];

    public function run(): void
    {
        $now = now();
        foreach (self::$permissions as $name => $alias) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'alias'      => $alias,
                    'updated_at' => $now,
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                ]
            );
        }
    }
}
