<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Covers the deploy-time migrate:safe command: an existing table must never be
 * recreated or dropped, existing rows must survive, and a migration that would drop
 * a column must stay pending unless it is explicitly allowed.
 */
class MigrateSafeTest extends TestCase
{
    /** @var string */
    private $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->path = sys_get_temp_dir().'/migrate-safe-'.uniqid();
        File::makeDirectory($this->path, 0755, true);

        File::put($this->path.'/2030_01_01_000000_create_safe_widgets_table.php', <<<'PHP'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up()
    {
        Schema::create('safe_widgets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
    }
    public function down()
    {
        Schema::dropIfExists('safe_widgets');
    }
};
PHP);

        File::put($this->path.'/2030_01_02_000000_add_label_to_safe_widgets_table.php', <<<'PHP'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up()
    {
        Schema::table('safe_widgets', function (Blueprint $table) {
            $table->string('label')->nullable();
        });
    }
    public function down()
    {
    }
};
PHP);

        File::put($this->path.'/2030_01_03_000000_drop_label_from_safe_widgets_table.php', <<<'PHP'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up()
    {
        Schema::table('safe_widgets', function (Blueprint $table) {
            $table->dropColumn('label');
        });
    }
    public function down()
    {
    }
};
PHP);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->path);

        parent::tearDown();
    }

    private function createLegacyTable(bool $withLabel = false): void
    {
        Schema::create('safe_widgets', function ($table) use ($withLabel) {
            $table->increments('id');
            $table->string('name');
            if ($withLabel) {
                $table->string('label')->nullable();
            }
        });

        DB::table('safe_widgets')->insert(['name' => 'existing row']);
    }

    private function runSafeMigrate(array $options = []): int
    {
        return $this->artisan('migrate:safe', array_merge([
            '--force' => true,
            '--path' => [$this->path],
        ], $options))->run();
    }

    /** @test */
    public function it_records_an_existing_table_instead_of_recreating_it()
    {
        $this->createLegacyTable();

        $this->assertSame(0, $this->runSafeMigrate());

        // The create migration was baselined, not executed: the row is untouched.
        $this->assertSame(1, DB::table('safe_widgets')->count());
        $this->assertSame('existing row', DB::table('safe_widgets')->first()->name);
        $this->assertTrue($this->hasRun('2030_01_01_000000_create_safe_widgets_table'));

        // The genuinely new column migration still ran.
        $this->assertTrue(Schema::hasColumn('safe_widgets', 'label'));
        $this->assertTrue($this->hasRun('2030_01_02_000000_add_label_to_safe_widgets_table'));
    }

    /** @test */
    public function it_leaves_a_column_dropping_migration_pending_by_default()
    {
        $this->createLegacyTable();

        $this->assertSame(0, $this->runSafeMigrate());

        $this->assertTrue(Schema::hasColumn('safe_widgets', 'label'));
        $this->assertFalse($this->hasRun('2030_01_03_000000_drop_label_from_safe_widgets_table'));
    }

    /** @test */
    public function it_runs_a_column_dropping_migration_only_when_explicitly_allowed()
    {
        $this->createLegacyTable();

        $this->assertSame(0, $this->runSafeMigrate(['--allow-destructive' => true]));

        $this->assertFalse(Schema::hasColumn('safe_widgets', 'label'));
        $this->assertTrue($this->hasRun('2030_01_03_000000_drop_label_from_safe_widgets_table'));
        $this->assertSame(1, DB::table('safe_widgets')->count());
    }

    /** @test */
    public function it_treats_an_existing_column_as_already_migrated()
    {
        $this->createLegacyTable(true);

        $this->assertSame(0, $this->runSafeMigrate());

        $this->assertTrue(Schema::hasColumn('safe_widgets', 'label'));
        $this->assertTrue($this->hasRun('2030_01_02_000000_add_label_to_safe_widgets_table'));
        $this->assertSame(1, DB::table('safe_widgets')->count());
    }

    /** @test */
    public function dry_run_changes_nothing()
    {
        $this->createLegacyTable();

        $this->assertSame(0, $this->runSafeMigrate(['--dry-run' => true]));

        $this->assertFalse(Schema::hasColumn('safe_widgets', 'label'));
        $this->assertFalse(
            Schema::hasTable('migrations')
            && DB::table('migrations')->where('migration', 'like', '2030_%')->exists()
        );
    }

    /** @test */
    public function it_creates_missing_tables_from_scratch()
    {
        $this->assertSame(0, $this->runSafeMigrate());

        $this->assertTrue(Schema::hasTable('safe_widgets'));
        $this->assertTrue(Schema::hasColumn('safe_widgets', 'name'));
        $this->assertTrue($this->hasRun('2030_01_01_000000_create_safe_widgets_table'));
    }

    /** @test */
    public function it_accepts_a_single_migration_file_path()
    {
        $exitCode = $this->artisan('migrate:safe', [
            '--force' => true,
            '--path' => [$this->path.'/2030_01_01_000000_create_safe_widgets_table.php'],
        ])->run();

        $this->assertSame(0, $exitCode);
        $this->assertTrue(Schema::hasTable('safe_widgets'));
        $this->assertTrue($this->hasRun('2030_01_01_000000_create_safe_widgets_table'));

        // Only the named file was considered.
        $this->assertFalse(Schema::hasColumn('safe_widgets', 'label'));
        $this->assertFalse($this->hasRun('2030_01_02_000000_add_label_to_safe_widgets_table'));
    }

    private function hasRun(string $migration): bool
    {
        return Schema::hasTable('migrations')
            && DB::table('migrations')->where('migration', $migration)->exists();
    }
}
