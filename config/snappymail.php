<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SnappyMail URL
    |--------------------------------------------------------------------------
    |
    | Public URL path where SnappyMail is mounted (Apache alias in
    | snappymail-apache.conf points /mail to public/snappymail).
    |
    */
    'url' => env('SNAPPYMAIL_URL', '/mail'),

    /*
    |--------------------------------------------------------------------------
    | SnappyMail Admin URL
    |--------------------------------------------------------------------------
    */
    'admin_url' => env('SNAPPYMAIL_ADMIN_URL', '/mail/?admin'),

    /*
    |--------------------------------------------------------------------------
    | SnappyMail Bootstrap Path
    |--------------------------------------------------------------------------
    |
    | Absolute path to SnappyMail's index.php. We include this in API mode
    | (SNAPPYMAIL_INCLUDE_AS_API) from SnappyMailController to call
    | \RainLoop\Api::CreateUserSsoHash() for single sign-on.
    |
    */
    'path' => env('SNAPPYMAIL_PATH', public_path('snappymail/index.php')),

    /*
    |--------------------------------------------------------------------------
    | IMAP / SMTP defaults shown on the user configuration screen
    |--------------------------------------------------------------------------
    |
    | These are only displayed as hints to the user. SnappyMail itself resolves
    | the actual server per email domain via public/snappymail/data/_data_/
    | _default_/domains/<domain>.json — add a JSON file there for each domain
    | your users sign in with.
    |
    */
    'imap' => [
        'host'          => env('IMAP_HOST', 'localhost'),
        'port'          => env('IMAP_PORT', 993),
        'encryption'    => env('IMAP_ENCRYPTION', 'ssl'),
        'validate_cert' => env('IMAP_VALIDATE_CERT', true),
    ],

    'smtp' => [
        'host'       => env('MAIL_HOST', 'localhost'),
        'port'       => env('MAIL_PORT', 587),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    */
    'features' => [
        'sso_enabled'  => env('SNAPPYMAIL_SSO_ENABLED', true),
        'direct_login' => env('SNAPPYMAIL_DIRECT_LOGIN', true),
        'admin_access' => env('SNAPPYMAIL_ADMIN_ACCESS', true),
        'user_config'  => env('SNAPPYMAIL_USER_CONFIG', true),
    ],

    'security' => [
        'require_https'       => env('SNAPPYMAIL_REQUIRE_HTTPS', false),
        'session_timeout'     => env('SNAPPYMAIL_SESSION_TIMEOUT', 3600),
        'max_attachment_size' => env('SNAPPYMAIL_MAX_ATTACHMENT_SIZE', 25),
    ],

    'logging' => [
        'enabled' => env('SNAPPYMAIL_LOGGING', true),
        'channel' => env('SNAPPYMAIL_LOG_CHANNEL', 'stack'),
        'level'   => env('SNAPPYMAIL_LOG_LEVEL', 'info'),
    ],

];
