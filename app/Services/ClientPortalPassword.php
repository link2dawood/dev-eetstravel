<?php
namespace App\Services;

class ClientPortalPassword
{
    public static function rules(): array
    {
        return ['bail', 'nullable', 'string', 'min:6', 'max:128'];
    }

    public static function hash(string $password): string
    {
        return \Illuminate\Support\Facades\Hash::driver('argon2id')->make($password, ['memory' => 65536, 'time' => 3, 'threads' => 1]);
    }

    public static function legacyValue(string $stored): string
    {
        try { return \Illuminate\Support\Facades\Crypt::decryptString($stored); }
        catch (\Throwable $e) {
            $payload = json_decode((string)base64_decode($stored, true), true);
            if (is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac'])) throw new \RuntimeException('A client portal password cannot be decrypted; reset it before migration.');
            return $stored;
        }
    }
}
