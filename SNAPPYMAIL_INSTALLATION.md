# SnappyMail Manual Installation Guide

## Download SnappyMail Manually

Since automated download failed due to network restrictions, please follow these steps:

### Option 1: Download on Local Machine and Upload

1. **Download SnappyMail** on your local computer:
   - Visit: https://github.com/the-djmaze/snappymail/releases/latest
   - Download `snappymail-latest.zip`

2. **Upload to Server:**
   ```bash
   scp snappymail-latest.zip user@your-server:/var/www/html/public/snappymail/
   ```

3. **Extract on Server:**
   ```bash
   cd /var/www/html/public/snappymail
   unzip snappymail-latest.zip
   rm snappymail-latest.zip
   ```

4. **Set Permissions:**
   ```bash
   chown -R www-data:www-data /var/www/html/public/snappymail
   chmod -R 755 /var/www/html/public/snappymail
   ```

### Option 2: Direct Download on Server (if internet available)

```bash
cd /var/www/html/public/snappymail
wget https://github.com/the-djmaze/snappymail/releases/download/v2.35.4/snappymail-2.35.4.zip
unzip snappymail-2.35.4.zip
rm snappymail-2.35.4.zip
chown -R www-data:www-data /var/www/html/public/snappymail
chmod -R 755 /var/www/html/public/snappymail
```

### Option 3: Use Composer Package

```bash
cd /var/www/html
composer require snappymail/snappymail-webmail
```

## Initial Configuration

1. **Access Admin Panel:**
   - URL: `http://your-domain.com/mail/?admin`
   - Default password: `12345` (CHANGE IMMEDIATELY!)

2. **Configure IMAP/SMTP:**
   - Use settings from `/var/www/html/config/imap.php`
   - Host: Check `IMAP_HOST` in `.env`
   - Port: Check `IMAP_PORT` in `.env`
   - Encryption: Check `IMAP_ENCRYPTION` in `.env`

3. **Enable SSO:**
   - Go to Security settings
   - Enable "Allow SSO"
   - Copy the SSO key to `.env` as `SNAPPYMAIL_SSO_KEY`

## Integration Status

✅ SSO Controller created
✅ Routes configured
✅ Config files created
⏳ Manual SnappyMail installation required
⏳ Admin configuration needed

## Next Steps After Installation

1. Complete manual installation above
2. Configure admin panel
3. Test SSO: Visit `/email/webmail`
4. Verify email sending/receiving
