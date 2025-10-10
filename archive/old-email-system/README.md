# Old Email System Archive

This directory contains the archived old email system that has been replaced by SnappyMail.

## Date Archived
**Date:** {{ date('Y-m-d H:i:s') }}

## What Was Replaced
The old IMAP-based email system has been replaced with **SnappyMail** webmail integration.

## Files to Archive (Manual Step)

### Controllers
```bash
# Move old email controller (BACKUP FIRST!)
cp app/Http/Controllers/EmailController.php archive/old-email-system/controllers/
# Note: Keep for now for backward compatibility
```

### Views
```bash
# Archive old email views
cp -r resources/views/email archive/old-email-system/views/
```

### IMAP Libraries
```bash
# Archive custom IMAP implementation
cp -r app/Http/Imap archive/old-email-system/imap/
```

### Commands
```bash
# Archive old email commands
cp app/Console/Commands/ParseEmails.php archive/old-email-system/commands/
```

### Models (Keep these for data access)
- `app/Email.php` - KEEP (for historical data)
- `app/EmailFolder.php` - KEEP (for historical data)

### Database Tables
**DO NOT DELETE** - Keep for historical reference:
- `emails` - Historical email data
- `email_folders` - Folder structure
- Consider creating archive tables:
  - `emails_archived`
  - `email_folders_archived`

## New System

### SnappyMail Location
- Installation: `/var/www/html/public/snappymail/`
- Access URL: `/mail`
- SSO URL: `/email/webmail`

### New Files Created
- **Controller:** `app/Http/Controllers/SnappyMailController.php`
- **Config:** `config/snappymail.php`
- **Migration:** `app/Console/Commands/MigrateToSnappyMail.php`
- **Mail Class:** `app/Mail/TourEmail.php`
- **Views:** `resources/views/snappymail/`, `resources/views/emails/tour/`

## Migration Steps

### 1. Backup Database
```bash
php artisan backup:database
# or
mysqldump -u root -p database_name > backup_before_snappymail.sql
```

### 2. Run Migration Command
```bash
php artisan snappymail:migrate --dry-run  # Test first
php artisan snappymail:migrate            # Actual migration
```

### 3. Archive Old Files (After Testing)
Only after SnappyMail is fully working and tested:
```bash
# Move old email system files to archive
mv app/Http/Imap archive/old-email-system/imap/
mv app/Console/Commands/ParseEmails.php archive/old-email-system/commands/
# Keep EmailController for now (deprecated but functional)
```

### 4. Clean Up Routes
Old email routes are marked as DEPRECATED in `routes/web.php` but still functional.
Remove them completely only after all users have migrated to SnappyMail.

## Rollback Plan

If you need to rollback to the old system:

1. **Restore Files:**
   ```bash
   cp -r archive/old-email-system/imap app/Http/
   cp archive/old-email-system/commands/ParseEmails.php app/Console/Commands/
   ```

2. **Remove SnappyMail Routes:**
   Comment out SnappyMail routes in `routes/web.php`

3. **Restore Database (if needed):**
   ```bash
   mysql -u root -p database_name < backup_before_snappymail.sql
   ```

4. **Clear Cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   ```

## Features Comparison

### Old System (IMAP)
- ✗ Direct IMAP connection per user
- ✗ Custom PHP IMAP implementation
- ✗ Limited webmail features
- ✗ No modern UI
- ✗ Performance issues with large mailboxes

### New System (SnappyMail)
- ✓ Modern webmail interface
- ✓ SSO integration with Laravel
- ✓ Better performance
- ✓ Mobile responsive
- ✓ Multiple account support
- ✓ Attachment handling
- ✓ Search functionality
- ✓ Folder management

## Support

For issues or questions:
1. Check `/var/www/html/SNAPPYMAIL_INSTALLATION.md`
2. View SnappyMail docs: https://snappymail.eu/
3. Check Laravel logs: `storage/logs/laravel.log`

## Important Notes

⚠️ **DO NOT DELETE:**
- Database tables (`emails`, `email_folders`)
- Models (`app/Email.php`, `app/EmailFolder.php`)
- Old routes (keep for backward compatibility initially)

✅ **Safe to Archive (after testing):**
- `app/Http/Imap/` directory
- `app/Console/Commands/ParseEmails.php`
- `resources/views/email/` (old views)

🔄 **Keep for Now:**
- `app/Http/Controllers/EmailController.php` (deprecated but functional)
- Old email routes (marked as deprecated)
