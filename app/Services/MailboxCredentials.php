<?php
namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Crypt;

class MailboxCredentials
{
    public function password(User $user): string
    {
        $value = (string)$user->email_password;
        if ($value === '') throw new \RuntimeException('Mailbox password is not configured.');
        try { return Crypt::decryptString($value); }
        catch (\Throwable $e) {
            $payload = json_decode((string)base64_decode($value, true), true);
            if ($user->email_password_encrypted || (is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']))) {
                throw new \RuntimeException('Mailbox password must be configured again.');
            }
            return $value;
        }
    }
}
