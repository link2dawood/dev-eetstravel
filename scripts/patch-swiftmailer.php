<?php

// Swift Mailer 6 uses partially supported constructor callables deprecated in
// PHP 8.2. Keep this compatibility patch until the Laravel mailer is upgraded.
// Run after Composer installs/updates; safe to run repeatedly.
$directory = dirname(__DIR__).'/vendor/swiftmailer/swiftmailer/lib/classes/Swift';
if (!is_dir($directory)) {
    exit(0);
}

foreach (glob($directory.'/*.php') as $path) {
    $source = file_get_contents($path);
    $patched = preg_replace(
        '~\\\\call_user_func_array\(\s*\[\$this, \'Swift_\w+::__construct\'\],\s*(.*?)\s*\);~s',
        'parent::__construct(...$1);',
        $source,
        -1,
        $count
    );
    if ($patched === null) {
        throw new RuntimeException('Cannot patch Swift Mailer: '.$path);
    }
    if ($count && file_put_contents($path, $patched) === false) {
        throw new RuntimeException('Cannot write Swift Mailer patch: '.$path);
    }
}
