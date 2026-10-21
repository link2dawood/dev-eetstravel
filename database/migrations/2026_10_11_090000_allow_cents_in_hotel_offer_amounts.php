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
            if (!\Illuminate\Support\Facades\Schema::hasTable($table) || !\Illuminate\Support\Facades\Schema::hasColumn($table, $column)) continue;
            \Illuminate\Support\Facades\Schema::table($table, function ($blueprint) use ($column, $null) { $blueprint->decimal($column, 12, 2)->nullable($null === 'NULL')->change(); });
        }
    }

    public function down()
    {
        // Preserve cents on rollback.
    }
}
