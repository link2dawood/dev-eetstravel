<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeRefToStringOnHotelOffersTable extends Migration
{
    /**
     * Booking references from hotels are alphanumeric (e.g. "HB-12345"),
     * but the column was an integer, so every reference was stored as 0.
     */
    public function up()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('hotel_offers')) return;
        \Illuminate\Support\Facades\Schema::table('hotel_offers', function ($table) {
            $column = $table->string('ref', 100)->nullable();
            if (\Illuminate\Support\Facades\Schema::hasColumn('hotel_offers', 'ref')) $column->change();
        });
    }

    public function down()
    {
        // Retain alphanumeric booking references on rollback.
    }
}
