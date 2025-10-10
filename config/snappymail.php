<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SnappyMail URL
    |--------------------------------------------------------------------------
    |
    | The URL path where SnappyMail is accessible. This should match your
    | Apache/Nginx configuration.
    |
    */
    'url' => env('SNAPPYMAIL_URL', '/mail'),

    /*
    |--------------------------------------------------------------------------
    | SnappyMail Admin URL
    |--------------------------------------------------------------------------
    |
    | The URL path for accessing SnappyMail admin panel
    |
    */
    'admin_url' => env('SNAPPYMAIL_ADMIN_URL', '/mail/?admin'),

    /*
    |--------------------------------------------------------------------------
    | SSO Key
    |--------------------------------------------------------------------------
    |
    | The secret key used for Single Sign-On authentication with SnappyMail.
    | This MUST match the SSO key configured in SnappyMail admin panel.
    |
    | Generate a secure key using: php artisan key:generate
    | Or use: openssl rand -base64 32
    |
    */
    'sso_key' => env('SNAPPYMAIL_SSO_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Admin Password
    |--------------------------------------------------------------------------
    |
    | SnappyMail default admin password (CHANGE AFTER INSTALLATION!)
    |
    */
    'admin_password' => env('SNAPPYMAIL_ADMIN_PASSWORD', '12345'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Settings
    |--------------------------------------------------------------------------
    |
    | Configure whether email passwords should be encrypted when stored
    |
    */
    'encrypt_passwords' => env('SNAPPYMAIL_ENCRYPT_PASSWORDS', true),

    /*
    |--------------------------------------------------------------------------
    | IMAP Server Configuration
    |--------------------------------------------------------------------------
    |
    | Default IMAP server settings from your existing configuration
    |
    */
    'imap' => [
        'host' => env('IMAP_HOST', 'localhost'),
        'port' => env('IMAP_PORT', 993),
        'encryption' => env('IMAP_ENCRYPTION', 'ssl'), // ssl, tls, or none
        'validate_cert' => env('IMAP_VALIDATE_CERT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMTP Server Configuration
    |--------------------------------------------------------------------------
    |
    | Default SMTP server settings for sending emails
    |
    */
    'smtp' => [
        'host' => env('MAIL_HOST', 'localhost'),
        'port' => env('MAIL_PORT', 587),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'), // ssl, tls, or none
        'username' => env('MAIL_USERNAME', ''),
        'password' => env('MAIL_PASSWORD', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific SnappyMail features
    |
    */
    'features' => [
        'sso_enabled' => env('SNAPPYMAIL_SSO_ENABLED', true),
        'direct_login' => env('SNAPPYMAIL_DIRECT_LOGIN', true),
        'admin_access' => env('SNAPPYMAIL_ADMIN_ACCESS', true),
        'user_config' => env('SNAPPYMAIL_USER_CONFIG', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security configuration for SnappyMail integration
    |
    */
    'security' => [
        'require_https' => env('SNAPPYMAIL_REQUIRE_HTTPS', false),
        'session_timeout' => env('SNAPPYMAIL_SESSION_TIMEOUT', 3600), // seconds
        'max_attachment_size' => env('SNAPPYMAIL_MAX_ATTACHMENT_SIZE', 25), // MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable logging for SnappyMail SSO and authentication
    |
    */
    'logging' => [
        'enabled' => env('SNAPPYMAIL_LOGGING', true),
        'channel' => env('SNAPPYMAIL_LOG_CHANNEL', 'stack'),
        'level' => env('SNAPPYMAIL_LOG_LEVEL', 'info'),
    ],

];
