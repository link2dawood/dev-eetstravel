<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AllowCentsInHotelOfferAmounts extends Migration
{
    /**
     * Offer prices and charges were integer columns, so 89.50 was stored as 90.
     * Existing whole-number values are kept as they are (e.g. 99 becomes 99.00).
     */
    private $columns = [
        ['hotel_offers', 'city_tax', 'INT(11)', 'NULL'],
        ['hotel_offers', 'halfboard', 'INT(100)', 'NULL'],
        ['hotel_offers', 'portrage_perperson', 'INT(11)', 'NULL'],
        ['hotel_offers', 'children_cost', 'INT(100)', 'NULL'],
        ['offer_room_prices', 'price', 'INT(11)', 'NOT NULL'],
        ['offer_cancellation_policies', 'cancellation_percentage', 'INT(100)', 'NOT NULL'],
    ];

    public function up()
    {
        foreach ($this->columns as [$table, $column, , $null]) {
            DB::statement("ALTER TABLE {$table} MODIFY {$column} DECIMAL(12,2) {$null}");
        }
    }

    public function down()
    {
        foreach ($this->columns as [$table, $column, $type, $null]) {
            DB::statement("ALTER TABLE {$table} MODIFY {$column} {$type} {$null}");
        }
    }
}
