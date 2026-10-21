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
        if (!\Illuminate\Support\Facades\Schema::hasTable('currency_rates')) return;
        \Illuminate\Support\Facades\Schema::table('currency_rates', function ($table) { $table->decimal('rate', 18, 6)->change(); });
    }

    public function down()
    {
        // Preserve exchange-rate precision on rollback.
    }
}
