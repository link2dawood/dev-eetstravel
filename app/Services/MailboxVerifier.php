<?php
namespace App\Services;

class MailboxVerifier
{
    public function verify(string $login, string $password): bool
    {
        if (!function_exists('imap_open') || !config('snappymail.imap.host')) return false;
        $host = (string)config('snappymail.imap.host');
        $port = (int)config('snappymail.imap.port', 993);
        $encryption = (string)config('snappymail.imap.encryption', 'ssl');
        if (!in_array($encryption, ['ssl', 'tls', 'none'], true) || preg_match('/[{}\/\r\n]/', $host)) return false;
        $flags = '/imap' . ($encryption === 'none' ? '' : '/' . $encryption) . '/validate-cert';
        imap_timeout(IMAP_OPENTIMEOUT, 10);
        $connection = @imap_open('{' . $host . ':' . $port . $flags . '}INBOX', $login, $password, OP_HALFOPEN, 1);
        // Clear extension errors without logging server responses or credentials.
        imap_errors(); imap_alerts();
        if (!$connection) return false;
        imap_close($connection);
        return true;
    }
}
