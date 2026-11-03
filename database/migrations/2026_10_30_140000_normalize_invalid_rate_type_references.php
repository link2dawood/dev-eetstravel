<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NormalizeInvalidRateTypeReferences extends Migration
{
    public function up()
    {
        $validIds = DB::table('rate_types')->pluck('id')->map(fn($id) => (string)$id)->all();
        DB::table('rates')->whereNotNull('rate_type')->whereNotIn('rate_type', $validIds)->update(['rate_type' => null]);
    }

    public function down()
    {
        // Invalid references cannot be inferred from an unclassified rate.
    }
}
