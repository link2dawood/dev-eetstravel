<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        if (!Schema::hasColumn('tours','client_id')) Schema::table('tours',function(Blueprint $table) { $table->unsignedInteger('client_id')->nullable()->index(); });
        if (!Schema::hasTable('tour_responsible_user')) Schema::create('tour_responsible_user',function(Blueprint $table) {
            $table->unsignedInteger('tour_id'); $table->unsignedInteger('user_id'); $table->unique(['tour_id','user_id']);
        });
        // Preserve existing assignments in the API representation.
        \Illuminate\Support\Facades\DB::table('tours')->where('responsible','>',0)->orderBy('id')->chunkById(200,function($tours) {
            foreach($tours as $tour) \Illuminate\Support\Facades\DB::table('tour_responsible_user')->updateOrInsert(['tour_id'=>$tour->id,'user_id'=>$tour->responsible]);
        });
    }
    public function down() {
        // Preserve client links and responsibility assignments on rollback.
    }
};
