<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Email;
use App\EmailFolder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateToSnappyMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snappymail:migrate
                            {--dry-run : Run without making changes}
                            {--encrypt : Encrypt email passwords}
                            {--users= : Migrate specific user IDs (comma-separated)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate email credentials and data to SnappyMail';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('===========================================');
        $this->info('   SnappyMail Migration Tool');
        $this->info('===========================================');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $encrypt = $this->option('encrypt');
        $userIds = $this->option('users') ? explode(',', $this->option('users')) : null;

        if ($dryRun) {
            $this->warn('DRY RUN MODE: No changes will be made');
            $this->newLine();
        }

        // Step 1: Check SnappyMail installation
        if (!$this->checkSnappyMailInstallation()) {
            $this->error('SnappyMail is not installed. Please install it first.');
            $this->info('See: /var/www/html/SNAPPYMAIL_INSTALLATION.md');
            return 1;
        }

        // Step 2: Migrate users with email credentials
        $this->info('Step 1: Migrating user email credentials...');
        $this->migrateUserCredentials($dryRun, $encrypt, $userIds);

        // Step 3: Archive old emails (optional)
        $this->newLine();
        if ($this->confirm('Do you want to archive old email data?', true)) {
            $this->info('Step 2: Archiving old email data...');
            $this->archiveOldEmails($dryRun);
        }

        // Step 4: Archive old email folders
        $this->newLine();
        if ($this->confirm('Do you want to archive email folders?', true)) {
            $this->info('Step 3: Archiving email folders...');
            $this->archiveEmailFolders($dryRun);
        }

        $this->newLine();
        $this->info('===========================================');
        $this->info('   Migration Complete!');
        $this->info('===========================================');
        $this->newLine();

        $this->info('Next Steps:');
        $this->line('1. Access SnappyMail admin: ' . config('snappymail.admin_url'));
        $this->line('2. Configure SSO key in admin panel');
        $this->line('3. Update .env with SNAPPYMAIL_SSO_KEY');
        $this->line('4. Test SSO: Visit /email/webmail');

        return 0;
    }

    /**
     * Check if SnappyMail is installed
     *
     * @return bool
     */
    private function checkSnappyMailInstallation()
    {
        $indexPath = public_path('snappymail/index.php');
        return file_exists($indexPath);
    }

    /**
     * Migrate user email credentials
     *
     * @param bool $dryRun
     * @param bool $encrypt
     * @param array|null $userIds
     * @return void
     */
    private function migrateUserCredentials($dryRun = false, $encrypt = false, $userIds = null)
    {
        $query = User::query();

        if ($userIds) {
            $query->whereIn('id', $userIds);
        }

        $users = $query->get();
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($users as $user) {
            try {
                // Check if user has email credentials
                if (empty($user->email_login)) {
                    // Try to use system IMAP credentials
                    if (env('IMAP_USERNAME')) {
                        $user->email_login = $user->email ?? env('IMAP_USERNAME');
                        $user->email_password = env('IMAP_PASSWORD');
                    } else {
                        $skipped++;
                        $bar->advance();
                        continue;
                    }
                }

                if (!$dryRun) {
                    // Encrypt password if requested
                    if ($encrypt && !empty($user->email_password)) {
                        if (!($user->email_password_encrypted ?? false)) {
                            $user->email_password = Crypt::encryptString($user->email_password);
                            $user->email_password_encrypted = true;
                        }
                    }

                    $user->save();
                }

                $migrated++;
            } catch (\Exception $e) {
                $this->error("\nError migrating user {$user->id}: " . $e->getMessage());
                Log::error('SnappyMail migration error', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Status', 'Count'],
            [
                ['Migrated', $migrated],
                ['Skipped', $skipped],
                ['Errors', $errors],
            ]
        );
    }

    /**
     * Archive old email data
     *
     * @param bool $dryRun
     * @return void
     */
    private function archiveOldEmails($dryRun = false)
    {
        $emailCount = Email::count();

        if ($emailCount == 0) {
            $this->info('No emails to archive.');
            return;
        }

        $this->info("Found {$emailCount} emails to archive.");

        if (!$dryRun) {
            // Create archive table if it doesn't exist
            if (!DB::getSchemaBuilder()->hasTable('emails_archived')) {
                DB::statement('CREATE TABLE emails_archived LIKE emails');
                $this->info('Created emails_archived table.');
            }

            // Copy data to archive
            DB::statement('INSERT INTO emails_archived SELECT * FROM emails');
            $this->info('Emails copied to archive.');

            // Optionally delete old emails
            if ($this->confirm('Delete old emails from emails table?', false)) {
                Email::truncate();
                $this->info('Old emails deleted.');
            }
        }
    }

    /**
     * Archive email folders
     *
     * @param bool $dryRun
     * @return void
     */
    private function archiveEmailFolders($dryRun = false)
    {
        $folderCount = EmailFolder::count();

        if ($folderCount == 0) {
            $this->info('No email folders to archive.');
            return;
        }

        $this->info("Found {$folderCount} email folders to archive.");

        if (!$dryRun) {
            // Create archive table if it doesn't exist
            if (!DB::getSchemaBuilder()->hasTable('email_folders_archived')) {
                DB::statement('CREATE TABLE email_folders_archived LIKE email_folders');
                $this->info('Created email_folders_archived table.');
            }

            // Copy data to archive
            DB::statement('INSERT INTO email_folders_archived SELECT * FROM email_folders');
            $this->info('Email folders copied to archive.');

            // Optionally delete old folders
            if ($this->confirm('Delete old folders from email_folders table?', false)) {
                EmailFolder::truncate();
                $this->info('Old email folders deleted.');
            }
        }
    }
}
