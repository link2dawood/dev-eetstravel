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
        DB::statement('ALTER TABLE hotel_offers MODIFY ref VARCHAR(100) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE hotel_offers MODIFY ref INT(100) NULL');
    }
}
