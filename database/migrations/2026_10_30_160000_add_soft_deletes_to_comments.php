<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToComments extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('comments', 'deleted_at')) Schema::table('comments', function ($t) { $t->softDeletes(); });
    }
    public function down()
    {
        // Preserve archived discussions and their deletion timestamps.
    }
}
