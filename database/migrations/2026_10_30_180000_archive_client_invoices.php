<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class ArchiveClientInvoices extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('client_invoices', 'deleted_at')) Schema::table('client_invoices', function ($t) { $t->softDeletes(); });
        if (!Schema::hasColumn('client_invoices', 'deleted_by')) Schema::table('client_invoices', function ($t) { $t->unsignedInteger('deleted_by')->nullable(); });
        if (!Schema::hasColumn('invoice_items', 'deleted_at')) Schema::table('invoice_items', function ($t) { $t->softDeletes(); });
    }
    public function down() { /* Preserve archived financial records and their metadata. */ }
}
