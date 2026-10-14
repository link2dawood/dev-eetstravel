<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PreserveClientInvoiceExtraCostDecimals extends Migration
{
    public function up()
    {
        Schema::table('client_invoices', function (Blueprint $table) {
            $table->decimal('extra_cost', 15, 2)->nullable()->change();
        });
    }

    public function down()
    {
        // Keep the widened column: reverting to integer would discard invoice cents.
    }
}
