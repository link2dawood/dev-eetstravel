<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * restaurants.id was the only primary key without AUTO_INCREMENT: a new restaurant got id 0
 * and the next insert failed with a duplicate key. Existing ids are kept; MySQL continues
 * numbering after the current maximum.
 */
class AddAutoIncrementToRestaurantsId extends Migration
{
    public function up()
    {
        if (DB::getDriverName() !== 'mysql') return;
        if ($this->hasAutoIncrement()) {
            return;
        }

        // An id-0 row would be renumbered by the ALTER; never let that happen silently
        if (DB::table('restaurants')->where('id', 0)->exists()) {
            throw new RuntimeException('restaurants has a row with id 0; give it a real id before running this migration.');
        }

        DB::statement('ALTER TABLE restaurants MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down()
    {
        if (DB::getDriverName() !== 'mysql') return;
        if ($this->hasAutoIncrement()) {
            DB::statement('ALTER TABLE restaurants MODIFY id INT UNSIGNED NOT NULL');
        }
    }

    private function hasAutoIncrement()
    {
        $column = DB::selectOne("SHOW COLUMNS FROM restaurants WHERE Field = 'id'");

        return $column && stripos($column->Extra, 'auto_increment') !== false;
    }
}
