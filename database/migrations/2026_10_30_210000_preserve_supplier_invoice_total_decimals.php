<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class PreserveSupplierInvoiceTotalDecimals extends Migration
{
    public function up()
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement('ALTER TABLE supplier_invoices MODIFY total_amount DECIMAL(15,2) NOT NULL');
    }

    public function down()
    {
        // Keep cent precision so a rollback cannot silently truncate invoice values.
    }
}
