<?php

namespace App\Services;

use App\User;
use Spatie\Activitylog\Models\Activity;

class ActivityAccess
{
    public static function query($user)
    {
        abort_unless($user, 403);
        $query = Activity::query();
        if (!$user->hasRole('admin')) {
            $query->where('activity_log.causer_id', $user->id)
                ->where('activity_log.causer_type', User::class);
        }
        return $query;
    }

    public static function link($value): ?string
    {
        if (!is_string($value) || $value === '' || preg_match('/[\x00-\x20\x7f\\\\]/', $value)) return null;
        $parts = parse_url($value);
        if ($parts === false || isset($parts['user']) || isset($parts['pass'])) return null;
        if (isset($parts['host'])) {
            $allowed = array_filter([parse_url(config('app.url'), PHP_URL_HOST), 'dev.eetstravel.com']);
            if (!in_array(strtolower($parts['host']), array_map('strtolower', $allowed), true)
                || !in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) return null;
        } elseif (isset($parts['scheme']) || str_starts_with($value, '//')) {
            return null;
        }
        $path = $parts['path'] ?? '';
        $decoded = rawurldecode($path);
        if (!str_starts_with($path, '/') || str_starts_with($decoded, '//') || preg_match('/[\x00-\x20\x7f\\\\]/', $decoded)) return null;
        return $path . (isset($parts['query']) ? '?' . $parts['query'] : '') . (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
    }
}
