<?php

require dirname(__DIR__).'/vendor/autoload.php';

set_error_handler(function ($severity, $message, $file, $line) {
    if (strpos($message, 'Callables of the form') !== false) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    return false;
});

$smtp = new Swift_SmtpTransport('smtp.example.test', 587, 'tls');
if ($smtp->getHost() !== 'smtp.example.test' || $smtp->getPort() !== 587 || $smtp->getEncryption() !== 'tls') {
    throw new RuntimeException('SMTP configuration changed.');
}

$message = new Swift_Message('Compatibility check', 'Test body', 'text/plain');
$message->setFrom('sender@example.test')->setTo('recipient@example.test');
$message->attach(new Swift_Attachment('attachment data', 'test.txt', 'text/plain'));
$message->attach(new Swift_EmbeddedFile('image data', 'test.png', 'image/png'));
$message->addPart('HTML body', 'text/html');
new Swift_SendmailTransport();
new Swift_FailoverTransport([$smtp]);
new Swift_LoadBalancedTransport([$smtp]);
new Swift_SpoolTransport(new Swift_MemorySpool());
$mailer = new Swift_Mailer(new Swift_NullTransport());
if ($mailer->send($message) !== 1 || strpos($message->toString(), base64_encode('attachment data')) === false) {
    throw new RuntimeException('Message construction or null transport failed.');
}

restore_error_handler();
echo "Swift Mailer constructors and message serialization passed (no email sent).\n";
