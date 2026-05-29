<?php

use Illuminate\Database\Seeder;

/**
 * Seeds the Spatie permission slugs gating the Help module's routes.
 *
 * Both slugs are derived from the route names registered in
 * routes/web.php (help.index, help.contact) and consumed by
 * App\Http\Middleware\PermissionsRequiredMiddleware.
 *
 * AUDIT.md → "Help" Critical was: "module absent — scaffold or drop".
 * This seeder is the permission-registration half of the scaffold.
 *
 * Re-running: insertOrIgnore so the seeder is safe to re-run on a
 * partially-seeded database (the existing PermissionsSeeder pattern
 * uses raw insert and breaks on re-run; we opt for the safer variant).
 */
class PermissionsHelpSeeder extends Seeder
{
    public static $permissions = [
        'help.index'   => 'Help — Index',
        'help.contact' => 'Help — Contact form submission',
    ];

    public function run()
    {
        $now = date('Y-m-d H:i:s');

        foreach (self::$permissions as $name => $alias) {
            DB::table('permissions')->insertOrIgnore([
                'name'       => $name,
                'alias'      => $alias,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
