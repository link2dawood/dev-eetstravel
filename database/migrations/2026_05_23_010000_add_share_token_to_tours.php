<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a per-tour share token used by the public landing page route
 * (GET /tour/{id}/landingpage). The token is generated lazily on first
 * staff access (see Tour::ensureShareToken()) and required as ?t=<token>
 * for anonymous requests to the landing page, blocking ID enumeration.
 *
 * 40 chars = Sha1Rand-style; CHAR not VARCHAR for fixed-width index lookups.
 * Nullable + unique so legacy tours can be backfilled lazily without
 * needing a one-shot data migration.
 */
class AddShareTokenToTours extends Migration
{
    public function up()
    {
        Schema::table('tours', function (Blueprint $table) {
            if (!Schema::hasColumn('tours', 'share_token')) {
                $table->char('share_token', 40)->nullable()->unique()->after('external_name');
            }
        });
    }

    public function down()
    {
        Schema::table('tours', function (Blueprint $table) {
            if (Schema::hasColumn('tours', 'share_token')) {
                $table->dropUnique(['share_token']);
                $table->dropColumn('share_token');
            }
        });
    }
}
