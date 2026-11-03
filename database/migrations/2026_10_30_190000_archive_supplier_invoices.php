<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class ArchiveSupplierInvoices extends Migration
{
    public function up()
    {
        if (Schema::hasTable('supplier_invoices')) {
            if (!Schema::hasColumn('supplier_invoices', 'deleted_at')) Schema::table('supplier_invoices', function ($table) { $table->softDeletes(); });
            if (!Schema::hasColumn('supplier_invoices', 'deleted_by')) Schema::table('supplier_invoices', function ($table) { $table->unsignedInteger('deleted_by')->nullable(); });
        }

        if (Schema::hasTable('invoices_tours') && !Schema::hasColumn('invoices_tours', 'deleted_at')) {
            Schema::table('invoices_tours', function ($table) { $table->softDeletes(); });
        }
    }

    public function down()
    {
        // Keep archived financial records and audit metadata.
    }
}
