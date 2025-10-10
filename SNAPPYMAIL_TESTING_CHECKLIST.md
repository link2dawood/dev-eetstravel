# SnappyMail Integration Testing Checklist

## Pre-Deployment Checklist

### 1. Environment Configuration ✓
- [ ] Copy `.env.snappymail.example` settings to `.env`
- [ ] Generate secure `SNAPPYMAIL_SSO_KEY`
  ```bash
  php artisan key:generate
  # or
  openssl rand -base64 32
  ```
- [ ] Configure IMAP/SMTP settings in `.env`
- [ ] Set secure admin password

### 2. SnappyMail Installation ✓
- [ ] Download SnappyMail manually (see `SNAPPYMAIL_INSTALLATION.md`)
- [ ] Extract to `/var/www/html/public/snappymail/`
- [ ] Set correct permissions:
  ```bash
  chown -R www-data:www-data /var/www/html/public/snappymail
  chmod -R 755 /var/www/html/public/snappymail
  ```
- [ ] Verify `index.php` exists

### 3. Apache/Web Server Configuration ✓
- [ ] Include `snappymail-apache.conf` in VirtualHost
  ```bash
  sudo nano /etc/apache2/sites-available/your-site.conf
  # Add: Include /var/www/html/snappymail-apache.conf
  ```
- [ ] Test Apache configuration:
  ```bash
  sudo apache2ctl configtest
  ```
- [ ] Reload Apache:
  ```bash
  sudo systemctl reload apache2
  ```
- [ ] Verify `.htaccess` is working

### 4. Laravel Configuration ✓
- [ ] Clear all caches:
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  ```
- [ ] Verify routes are registered:
  ```bash
  php artisan route:list | grep snappymail
  ```
- [ ] Check config is loaded:
  ```bash
  php artisan config:cache
  php artisan config:show snappymail
  ```

## Functional Testing

### 5. SnappyMail Admin Panel
- [ ] Access admin panel: `/mail/?admin`
- [ ] Login with admin password (default: `12345`)
- [ ] **CHANGE ADMIN PASSWORD IMMEDIATELY**
- [ ] Configure IMAP domain:
  - Domain: Your email domain
  - IMAP Server: From `.env`
  - IMAP Port: From `.env`
  - Encryption: SSL/TLS
- [ ] Configure SMTP settings
- [ ] Enable SSO in Security settings
- [ ] Copy SSO key to `.env` as `SNAPPYMAIL_SSO_KEY`
- [ ] Save all settings

### 6. SSO Authentication
- [ ] Login to Laravel application
- [ ] Navigate to `/email/webmail`
- [ ] Should redirect to SnappyMail with auto-login
- [ ] Verify no password prompt appears
- [ ] Check session is maintained

### 7. Email Operations
- [ ] Send test email
- [ ] Receive and read emails
- [ ] Reply to email
- [ ] Forward email
- [ ] Create folders
- [ ] Move emails between folders
- [ ] Delete emails
- [ ] Search functionality
- [ ] Attachment upload
- [ ] Attachment download

### 8. Tour Email Templates (Laravel)
- [ ] Access `/templates`
- [ ] Create tour confirmation email
- [ ] Test sending via Laravel Mail
- [ ] Verify SMTP delivery
- [ ] Check email formatting
- [ ] Test with attachments

### 9. User Configuration
- [ ] Access `/email/configure`
- [ ] Save email credentials
- [ ] Verify encryption works
- [ ] Test login with saved credentials

### 10. Navigation & UI
- [ ] Verify "Webmail (SnappyMail)" link in sidebar
- [ ] Click link opens SnappyMail in new tab
- [ ] Email widget visible on dashboard (if added)
- [ ] Old email inbox still accessible

## Security Testing

### 11. Authentication & Authorization
- [ ] SSO only works for authenticated users
- [ ] Logout from Laravel logs out of SnappyMail
- [ ] Direct `/mail` access requires login
- [ ] Admin panel restricted to admin role
- [ ] Email passwords are encrypted in database

### 12. Security Headers
- [ ] X-Content-Type-Options: nosniff
- [ ] X-Frame-Options: SAMEORIGIN
- [ ] X-XSS-Protection: enabled
- [ ] CSP headers configured (if applicable)

### 13. HTTPS & SSL/TLS
- [ ] Force HTTPS (if enabled in config)
- [ ] IMAP connection uses SSL/TLS
- [ ] SMTP connection uses TLS
- [ ] Certificate validation working

### 14. Data Protection
- [ ] Email passwords encrypted in database
- [ ] Session timeout configured
- [ ] No passwords in logs
- [ ] No sensitive data in error messages
- [ ] File upload size limits enforced

## Performance Testing

### 15. Load Testing
- [ ] Test with large mailbox (>1000 emails)
- [ ] Multiple concurrent users
- [ ] Large attachment handling (up to 25MB)
- [ ] Search performance
- [ ] Folder sync speed

### 16. Resource Usage
- [ ] Check PHP memory limit (256MB recommended)
- [ ] Monitor Apache/PHP-FPM processes
- [ ] Database query performance
- [ ] Browser console for JS errors

## Migration Testing

### 17. Data Migration
- [ ] Run migration command (dry-run first):
  ```bash
  php artisan snappymail:migrate --dry-run
  ```
- [ ] Review migration output
- [ ] Execute actual migration:
  ```bash
  php artisan snappymail:migrate
  ```
- [ ] Verify user credentials migrated
- [ ] Check email data archived (if opted)

### 18. Backward Compatibility
- [ ] Old email routes still work (deprecated)
- [ ] Email templates still functional
- [ ] Historical emails accessible
- [ ] No broken links in application

## Post-Deployment

### 19. Monitoring
- [ ] Set up logging:
  ```bash
  tail -f storage/logs/laravel.log
  ```
- [ ] Monitor Apache error logs:
  ```bash
  tail -f /var/log/apache2/error.log
  ```
- [ ] Track failed SSO attempts
- [ ] Monitor email delivery

### 20. Documentation
- [ ] User training materials created
- [ ] Admin documentation updated
- [ ] Rollback procedure documented
- [ ] Support contacts listed

### 21. Backup
- [ ] Database backup created
- [ ] Old email system archived
- [ ] Config files backed up
- [ ] Rollback plan tested

## Known Issues & Solutions

### Issue: SSO Not Working
**Solution:**
1. Verify `SNAPPYMAIL_SSO_KEY` matches SnappyMail admin setting
2. Check user has `email_login` configured
3. Review logs: `storage/logs/laravel.log`

### Issue: Emails Not Sending
**Solution:**
1. Check SMTP settings in `.env`
2. Test SMTP connection: `php artisan tinker` → `Mail::raw('test', fn($m) => $m->to('test@example.com')->subject('Test'))`
3. Check SMTP port is not blocked by firewall

### Issue: Permission Denied
**Solution:**
```bash
chown -R www-data:www-data /var/www/html/public/snappymail
chmod -R 755 /var/www/html/public/snappymail
```

### Issue: 404 on /mail
**Solution:**
1. Verify Apache alias configured
2. Check .htaccess exists
3. Enable mod_rewrite: `sudo a2enmod rewrite`
4. Restart Apache: `sudo systemctl restart apache2`

## Sign-Off

### Testing Completed By:
- **Name:** _________________
- **Date:** _________________
- **Role:** _________________

### Approved for Production:
- **Name:** _________________
- **Date:** _________________
- **Role:** _________________

## Next Steps After Testing

1. ✅ All tests passed → Deploy to production
2. ❌ Issues found → Document and fix before deployment
3. 📝 Create user training schedule
4. 📧 Announce new webmail system to users
5. 🔄 Schedule old system deprecation (30-60 days)
6. 🗑️ Plan for final cleanup after migration period
