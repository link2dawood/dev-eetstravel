<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * currency_rates.rate was double(8,2): exchange rates such as 0.8634 were saved
 * as 0.86. Widening to decimal(18,6) keeps every existing value.
 */
class WidenCurrencyRatesRatePrecision extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE `currency_rates` MODIFY `rate` DECIMAL(18,6) NOT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `currency_rates` MODIFY `rate` DOUBLE(8,2) NOT NULL');
    }
}
