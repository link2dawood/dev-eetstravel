<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Non-destructive replacement for "php artisan migrate" on deploy.
 *
 * The production database predates parts of the migration history: tables such as
 * password_resets exist, but there is no matching row in the migrations table, so a
 * plain migrate aborts with "Base table or view already exists" and the whole deploy
 * fails. This command reconciles (baselines) that history instead:
 *
 *   - a pending migration whose schema is already present is only recorded as run,
 *   - a genuinely new migration is executed through the normal migrate command,
 *   - a migration that would drop a table, column or index is never run automatically;
 *     it is reported for manual review unless --allow-destructive is given.
 *
 * Nothing is ever rolled back, dropped or truncated by this command itself: it only
 * ever runs up(), and it only inserts rows into the migrations table.
 */
class MigrateSafe extends Command
{
    use ConfirmableTrait;

    protected $signature = 'migrate:safe
        {--force : Run without confirmation in production}
        {--dry-run : Report what would happen without touching the database}
        {--allow-destructive : Also run pending migrations that drop tables, columns or indexes}
        {--path=* : Migration directories (defaults to database/migrations)}';

    protected $description = 'Run pending migrations without ever dropping existing tables, columns or rows';

    /**
     * Schema operations that can destroy existing data.
     */
    private const DESTRUCTIVE_PATTERNS = [
        'dropColumn',
        'dropIfExists',
        'dropForeign',
        'dropIndex',
        'dropUnique',
        'dropPrimary',
        'dropSoftDeletes',
        'dropMorphs',
        'dropTimestamps',
        'Schema::drop',
        '->truncate(',
        'DELETE FROM',
        'TRUNCATE',
    ];

    /**
     * MySQL error codes that mean "this schema change is already in place".
     */
    private const ALREADY_APPLIED_CODES = [
        1050, // table already exists
        1060, // duplicate column name
        1061, // duplicate key name
        1091, // can't drop: column/key does not exist
        1022, // duplicate key
        1826, // duplicate foreign key constraint name
        1068, // multiple primary keys defined
    ];

    private const ALREADY_APPLIED_MESSAGES = [
        'already exists',
        'duplicate column name',
        'duplicate key name',
        'check that column/key exists',
        'duplicate foreign key',
        'multiple primary key',
    ];

    /**
     * MySQL error codes that mean "this migration cannot be applied to the current
     * schema" - typically a rename or a change of something that is no longer there
     * because the database was built from a dump newer than the migration.
     */
    private const NOT_APPLICABLE_CODES = [
        1051, // unknown table
        1054, // unknown column
        1072, // key column does not exist in table
        1146, // table does not exist
        1176, // key does not exist in table
        1005, // can't create table (usually errno 150: foreign key mismatch)
        1215, // cannot add foreign key constraint
        1364, // field has no default value (data the migration does not provide)
        1452, // cannot add or update a child row: foreign key constraint fails
        1553, // cannot drop index needed by a foreign key constraint
        1824, // failed to open the referenced table
        1825, // failed to add the foreign key constraint
        3730, // cannot drop table referenced by a foreign key constraint
        // Values that would be mangled by the column type this migration wants.
        1264, // out of range value for column
        1265, // data truncated for column
        1292, // incorrect value for column
        1366, // incorrect integer/string value for column
        1406, // data too long for column
    ];

    private const NOT_APPLICABLE_MESSAGES = [
        'there is no column with name',
        'unknown column',
        'incorrect integer value',
        'incorrect decimal value',
        'incorrect double value',
        'data truncated',
        'data too long',
        'out of range value',
        'foreign key constraint fails',
        'cannot add foreign key',
    ];

    /**
     * The session sql_mode to restore after a migration ran with strict checking.
     *
     * @var string|null
     */
    private $previousSqlMode = null;

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        if (! $dryRun && ! $this->confirmToProceed()) {
            return 1;
        }

        $this->line($dryRun
            ? 'DRY RUN: inspecting pending migrations, nothing will be written.'
            : 'Reconciling pending migrations (non-destructive).');

        if (! Schema::hasTable('migrations')) {
            if ($dryRun) {
                $this->warn('The migrations table does not exist yet; it would be created.');
            } else {
                $this->call('migrate:install');
            }
        }

        $ran = Schema::hasTable('migrations')
            ? DB::table('migrations')->pluck('migration')->all()
            : [];
        if (empty($ran) && Schema::hasTable('users')) {
            $this->warn('The migrations table has no rows but the database already has tables.');
            $this->warn('Existing tables will be recorded as migrated, never recreated, and the stored');
            $this->warn('schema dump in database/schema is never loaded by this command.');
        }

        $ran = array_flip($ran);

        $pending = [];
        foreach ($this->migrationFiles() as $name => $path) {
            if (! isset($ran[$name])) {
                $pending[$name] = $path;
            }
        }

        if (empty($pending)) {
            $this->info('Nothing to migrate; database schema history is up to date.');

            return 0;
        }

        $this->line(sprintf('%d pending migration(s) found.', count($pending)));

        $recorded = [];
        $executed = [];
        $needsReview = [];

        foreach ($pending as $name => $path) {
            $plan = $this->planFor($path);

            if ($plan['destructive'] && ! $this->option('allow-destructive')) {
                if ($this->looksAlreadyApplied($plan)) {
                    $this->baseline($name, $dryRun, 'schema already matches this migration');
                    $recorded[] = $name;

                    continue;
                }

                $note = 'uses '.implode(', ', $plan['destructiveOps']);

                $missingTables = array_values(array_filter($plan['creates'], function ($table) {
                    return ! Schema::hasTable($table);
                }));

                if (! empty($missingTables)) {
                    $note .= '; it also creates missing table(s): '.implode(', ', $missingTables);
                }

                $this->warn(sprintf('  ! %s  SKIPPED: %s', $name, $note));
                $needsReview[$name] = $note;

                continue;
            }

            if ($this->looksAlreadyApplied($plan)) {
                $this->baseline($name, $dryRun, 'schema already present');
                $recorded[] = $name;

                continue;
            }

            if ($dryRun) {
                $this->line(sprintf('  > %s  would run', $name));
                $executed[] = $name;

                continue;
            }

            try {
                // A migration that changes an existing column runs with strict value
                // checking, so values it cannot convert abort the statement instead of
                // being silently overwritten with 0 or ''.
                $this->strictValues($plan['changesColumns']);

                $exitCode = $this->call('migrate', $this->migrateOptions($path));

                if ($exitCode !== 0) {
                    $this->error(sprintf('Migration %s exited with code %d; stopping.', $name, $exitCode));

                    return 1;
                }

                $executed[] = $name;
            } catch (Throwable $e) {
                switch ($this->classifyFailure($e)) {
                    case 'applied':
                        $this->baseline($name, false, 'database reported the change is already in place');
                        $recorded[] = $name;
                        break;

                    case 'not-applicable':
                        // The schema this migration expects is not there, so it can never
                        // succeed: leave it pending and report it instead of failing the
                        // deploy or guessing what to change.
                        $note = 'cannot be applied to the current schema or data: '.$this->firstLine($e->getMessage());
                        $this->warn(sprintf('  ! %s  SKIPPED: %s', $name, $note));
                        $needsReview[$name] = $note;
                        break;

                    default:
                        $this->error(sprintf('Migration %s failed: %s', $name, $e->getMessage()));
                        $this->error('Deployment stopped. No data was removed by migrate:safe.');

                        return 1;
                }
            } finally {
                $this->strictValues(false);
            }
        }

        $this->summarise($recorded, $executed, $needsReview, $dryRun);

        return 0;
    }

    /**
     * Turn MySQL strict value checking on or off for the migration about to run.
     *
     * config/database.php sets 'strict' => false, so a migration that narrows a column
     * (for example guides.city from varchar to unsignedInteger) would quietly turn
     * every value it cannot convert into 0 instead of failing. STRICT_ALL_TABLES makes
     * the statement fail instead, so the migration is reported and the data is kept.
     *
     * It is only switched on for migrations that change existing columns, because the
     * same mode would also reject inserts that rely on implicit column defaults.
     */
    private function strictValues(bool $strict): void
    {
        if (! $strict && $this->previousSqlMode === null) {
            return;
        }

        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        try {
            if ($strict) {
                $mode = (string) DB::selectOne('SELECT @@SESSION.sql_mode AS sql_mode')->sql_mode;

                if (stripos($mode, 'STRICT_ALL_TABLES') !== false) {
                    return;
                }

                $this->previousSqlMode = $mode;
                DB::statement('SET SESSION sql_mode = ?', [trim($mode.',STRICT_ALL_TABLES', ',')]);

                return;
            }

            $restore = $this->previousSqlMode;
            $this->previousSqlMode = null;
            DB::statement('SET SESSION sql_mode = ?', [$restore]);
        } catch (Throwable $e) {
            $this->warn('Could not change strict value checking: '.$this->firstLine($e->getMessage()));
        }
    }

    /**
     * Options for running one migration file through the normal migrate command.
     *
     * --schema-path deliberately points at a file that does not exist. Without it,
     * migrate loads database/schema/{connection}-schema.dump whenever the migrations
     * table has no rows, and that dump starts with DROP TABLE IF EXISTS for every
     * table: on a live database it would destroy all existing data.
     *
     * @return array<string, mixed>
     */
    private function migrateOptions(string $path): array
    {
        $options = [
            '--force' => true,
            '--path' => [$path],
            '--realpath' => true,
        ];

        if ($this->supportsOption('migrate', 'schema-path')) {
            $options['--schema-path'] = storage_path('framework/migrate-safe-no-schema-dump');
        }

        return $options;
    }

    private function supportsOption(string $command, string $option): bool
    {
        $application = $this->getApplication();

        if ($application === null || ! $application->has($command)) {
            return false;
        }

        return $application->find($command)->getDefinition()->hasOption($option);
    }

    /**
     * Pending migration files keyed by migration name, ordered the way migrate orders them.
     */
    private function migrationFiles(): array
    {
        $paths = $this->option('path') ?: [database_path('migrations')];

        $files = [];
        foreach ($paths as $path) {
            foreach (glob(rtrim($path, '/').'/*_*.php') ?: [] as $file) {
                $files[basename($file, '.php')] = $file;
            }
        }

        ksort($files);

        return $files;
    }

    /**
     * Describe what the up() method of a migration does, without executing it.
     *
     * @return array{creates: array<int, string>, dropsTables: array<int, string>, dropsColumns: array<string, array<int, string>>, destructive: bool, destructiveOps: array<int, string>, onlySchemaCreates: bool, changesColumns: bool}
     */
    private function planFor(string $path): array
    {
        $body = $this->upBody((string) file_get_contents($path));

        $destructiveOps = [];
        foreach (self::DESTRUCTIVE_PATTERNS as $pattern) {
            if (stripos($body, $pattern) !== false) {
                $destructiveOps[] = $pattern;
            }
        }

        $creates = [];
        $dropsTables = [];
        $dropsColumns = [];
        $otherOps = false;

        foreach ($this->schemaCalls($body) as $call) {
            switch ($call['method']) {
                case 'create':
                    $creates[] = $call['table'];
                    break;
                case 'drop':
                case 'dropIfExists':
                    $dropsTables[] = $call['table'];
                    break;
                case 'table':
                    $columns = $this->droppedColumns($call['segment']);
                    if (! empty($columns)) {
                        $dropsColumns[$call['table']] = array_merge(
                            $dropsColumns[$call['table']] ?? [],
                            $columns
                        );
                    }
                    $otherOps = true;
                    break;
                default:
                    $otherOps = true;
            }
        }

        // Anything the parser does not recognise (raw SQL, data back-fills, renames)
        // counts as "other", so the migration is attempted rather than baselined.
        if (preg_match('/DB::(statement|unprepared|table|select|update|insert)/i', $body)) {
            $otherOps = true;
        }

        return [
            'creates' => array_values(array_unique($creates)),
            'dropsTables' => array_values(array_unique($dropsTables)),
            'dropsColumns' => $dropsColumns,
            'destructive' => ! empty($destructiveOps),
            'destructiveOps' => $destructiveOps,
            'onlySchemaCreates' => ! $otherOps,
            'changesColumns' => strpos($body, '->change(') !== false,
        ];
    }

    /**
     * Extract the body of up(), stopping before down() so rollback code is ignored.
     */
    private function upBody(string $source): string
    {
        if (! preg_match('/function\s+up\s*\(/i', $source, $match, PREG_OFFSET_CAPTURE)) {
            return $source;
        }

        $body = substr($source, $match[0][1] + strlen($match[0][0]));

        if (preg_match('/function\s+down\s*\(/i', $body, $downMatch, PREG_OFFSET_CAPTURE)) {
            $body = substr($body, 0, $downMatch[0][1]);
        }

        return $body;
    }

    /**
     * Schema:: calls in source order, each with the slice of code that follows it.
     *
     * @return array<int, array{method: string, table: string, segment: string}>
     */
    private function schemaCalls(string $body): array
    {
        $pattern = '/Schema::(?:connection\([^)]*\)->)?(create|table|drop|dropIfExists|rename)\(\s*[\'"]([^\'"]+)[\'"]/';

        if (! preg_match_all($pattern, $body, $matches, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $calls = [];
        $count = count($matches[0]);

        for ($i = 0; $i < $count; $i++) {
            $start = $matches[0][$i][1];
            $end = $i + 1 < $count ? $matches[0][$i + 1][1] : strlen($body);

            $calls[] = [
                'method' => $matches[1][$i][0],
                'table' => $matches[2][$i][0],
                'segment' => substr($body, $start, $end - $start),
            ];
        }

        return $calls;
    }

    /**
     * Column names passed to dropColumn() inside one Schema::table() block.
     *
     * @return array<int, string>
     */
    private function droppedColumns(string $segment): array
    {
        if (! preg_match_all('/dropColumn\(\s*(\[[^\]]*\]|[^)]*)\)/', $segment, $matches)) {
            return [];
        }

        $columns = [];
        foreach ($matches[1] as $arguments) {
            if (preg_match_all('/[\'"]([^\'"]+)[\'"]/', $arguments, $names)) {
                $columns = array_merge($columns, $names[1]);
            }
        }

        return array_values(array_unique($columns));
    }

    /**
     * True when the live schema already looks the way this migration would leave it.
     *
     * @param  array{creates: array<int, string>, dropsTables: array<int, string>, dropsColumns: array<string, array<int, string>>, destructive: bool, destructiveOps: array<int, string>, onlySchemaCreates: bool, changesColumns: bool}  $plan
     */
    private function looksAlreadyApplied(array $plan): bool
    {
        $assertions = 0;

        foreach ($plan['creates'] as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
            $assertions++;
        }

        foreach ($plan['dropsTables'] as $table) {
            if (Schema::hasTable($table)) {
                return false;
            }
            $assertions++;
        }

        foreach ($plan['dropsColumns'] as $table => $columns) {
            foreach ($columns as $column) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                    return false;
                }
                $assertions++;
            }
        }

        if ($assertions === 0) {
            return false;
        }

        // A migration that also adds columns, back-fills data or runs raw SQL is run
        // normally even when its tables exist; those statements are the point of it.
        return $plan['onlySchemaCreates'] || $plan['destructive'];
    }

    /**
     * Record a migration as run without executing it.
     */
    private function baseline(string $name, bool $dryRun, string $reason): void
    {
        if ($dryRun) {
            $this->line(sprintf('  = %s  would be recorded as run (%s)', $name, $reason));

            return;
        }

        DB::table('migrations')->insert([
            'migration' => $name,
            'batch' => $this->nextBatch(),
        ]);

        $this->line(sprintf('  = %s  recorded as run (%s)', $name, $reason));
    }

    private function nextBatch(): int
    {
        return ((int) DB::table('migrations')->max('batch')) + 1;
    }

    /**
     * Why did a migration fail?
     *
     * applied        - the change is already in the database, so record it and move on.
     * not-applicable - the migration needs something that is not there (a column it
     *                  renames, a table it alters) and can never succeed as written.
     * fatal          - anything else; the deploy stops so a human can look at it.
     */
    private function classifyFailure(Throwable $e): string
    {
        $codes = [];
        $message = '';

        for ($error = $e; $error !== null; $error = $error->getPrevious()) {
            $codes[] = $error->getCode();
            $message .= ' '.strtolower($error->getMessage());

            // PDO reports SQLSTATE through getCode(); the MySQL error number this code
            // matches on lives in errorInfo[1].
            if (isset($error->errorInfo[1])) {
                $codes[] = $error->errorInfo[1];
            }
        }

        foreach ($codes as $code) {
            if (is_numeric($code) && in_array((int) $code, self::ALREADY_APPLIED_CODES, true)) {
                return 'applied';
            }
        }

        foreach (self::ALREADY_APPLIED_MESSAGES as $needle) {
            if (strpos($message, $needle) !== false) {
                return 'applied';
            }
        }

        foreach ($codes as $code) {
            if (is_numeric($code) && in_array((int) $code, self::NOT_APPLICABLE_CODES, true)) {
                return 'not-applicable';
            }
        }

        foreach (self::NOT_APPLICABLE_MESSAGES as $needle) {
            if (strpos($message, $needle) !== false) {
                return 'not-applicable';
            }
        }

        return 'fatal';
    }

    private function firstLine(string $message): string
    {
        $message = trim(strtok($message, "\n"));

        return strlen($message) > 160 ? substr($message, 0, 157).'...' : $message;
    }

    /**
     * @param  array<int, string>  $recorded
     * @param  array<int, string>  $executed
     * @param  array<string, string>  $needsReview
     */
    private function summarise(array $recorded, array $executed, array $needsReview, bool $dryRun): void
    {
        $this->line('');
        $this->info(sprintf(
            '%s %d migration(s) run, %d recorded as already applied, %d skipped for review.',
            $dryRun ? 'Would have:' : 'Done:',
            count($executed),
            count($recorded),
            count($needsReview)
        ));

        if (! empty($needsReview)) {
            $this->warn('Left pending for review (nothing was dropped or deleted):');
            foreach ($needsReview as $name => $note) {
                $this->warn(sprintf('  - %s', $name));
                $this->warn(sprintf('      %s', $note));
            }
            $this->warn('Review them against the live schema, back up the database, then run');
            $this->warn('one of these by hand once you know it is safe:');
            $this->warn('  php artisan migrate --force --path=database/migrations/<file>.php');
            $this->warn('  php artisan migrate:safe --force --allow-destructive');
        }
    }
}
