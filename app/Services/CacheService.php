<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Default cache TTL in seconds (1 hour)
     */
    const DEFAULT_TTL = 3600;

    /**
     * Cache tags for organized cache management
     */
    const CACHE_TAGS = [
        'tours' => 'tours',
        'users' => 'users',
        'clients' => 'clients',
        'statistics' => 'statistics',
        'dashboard' => 'dashboard',
        'reports' => 'reports',
    ];

    /**
     * Get cached data or execute callback and cache result
     *
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @param array $tags
     * @return mixed
     */
    public function remember(string $key, callable $callback, int $ttl = self::DEFAULT_TTL, array $tags = [])
    {
        try {
            if (!empty($tags)) {
                return Cache::tags($tags)->remember($key, $ttl, $callback);
            }

            return Cache::remember($key, $ttl, $callback);

        } catch (\Exception $e) {
            Log::warning('Cache operation failed, executing callback directly', [
                'key' => $key,
                'error' => $e->getMessage(),
                'tags' => $tags,
            ]);

            // If cache fails, execute callback directly
            return $callback();
        }
    }

    /**
     * Store data in cache
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @param array $tags
     * @return bool
     */
    public function put(string $key, $value, int $ttl = self::DEFAULT_TTL, array $tags = []): bool
    {
        try {
            if (!empty($tags)) {
                return Cache::tags($tags)->put($key, $value, $ttl);
            }

            return Cache::put($key, $value, $ttl);

        } catch (\Exception $e) {
            Log::warning('Cache put operation failed', [
                'key' => $key,
                'error' => $e->getMessage(),
                'tags' => $tags,
            ]);

            return false;
        }
    }

    /**
     * Get data from cache
     *
     * @param string $key
     * @param mixed $default
     * @param array $tags
     * @return mixed
     */
    public function get(string $key, $default = null, array $tags = [])
    {
        try {
            if (!empty($tags)) {
                return Cache::tags($tags)->get($key, $default);
            }

            return Cache::get($key, $default);

        } catch (\Exception $e) {
            Log::warning('Cache get operation failed', [
                'key' => $key,
                'error' => $e->getMessage(),
                'tags' => $tags,
            ]);

            return $default;
        }
    }

    /**
     * Remove data from cache
     *
     * @param string $key
     * @param array $tags
     * @return bool
     */
    public function forget(string $key, array $tags = []): bool
    {
        try {
            if (!empty($tags)) {
                return Cache::tags($tags)->forget($key);
            }

            return Cache::forget($key);

        } catch (\Exception $e) {
            Log::warning('Cache forget operation failed', [
                'key' => $key,
                'error' => $e->getMessage(),
                'tags' => $tags,
            ]);

            return false;
        }
    }

    /**
     * Flush cache by tags
     *
     * @param array $tags
     * @return bool
     */
    public function flushTags(array $tags): bool
    {
        try {
            if (empty($tags)) {
                return false;
            }

            Cache::tags($tags)->flush();

            Log::info('Cache flushed successfully', ['tags' => $tags]);

            return true;

        } catch (\Exception $e) {
            Log::warning('Cache flush operation failed', [
                'tags' => $tags,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Generate cache key with prefix
     *
     * @param string $prefix
     * @param mixed ...$parts
     * @return string
     */
    public function generateKey(string $prefix, ...$parts): string
    {
        $keyParts = array_merge([$prefix], $parts);
        $keyParts = array_map(function ($part) {
            if (is_array($part) || is_object($part)) {
                return md5(serialize($part));
            }
            return (string) $part;
        }, $keyParts);

        return implode(':', $keyParts);
    }

    /**
     * Cache tour-related data
     *
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public function cacheTours(string $key, callable $callback, int $ttl = self::DEFAULT_TTL)
    {
        return $this->remember($key, $callback, $ttl, [self::CACHE_TAGS['tours']]);
    }

    /**
     * Cache user-related data
     *
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public function cacheUsers(string $key, callable $callback, int $ttl = self::DEFAULT_TTL)
    {
        return $this->remember($key, $callback, $ttl, [self::CACHE_TAGS['users']]);
    }

    /**
     * Cache statistics data
     *
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public function cacheStatistics(string $key, callable $callback, int $ttl = 300) // 5 minutes for stats
    {
        return $this->remember($key, $callback, $ttl, [self::CACHE_TAGS['statistics']]);
    }

    /**
     * Cache dashboard data
     *
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public function cacheDashboard(string $key, callable $callback, int $ttl = 600) // 10 minutes for dashboard
    {
        return $this->remember($key, $callback, $ttl, [self::CACHE_TAGS['dashboard']]);
    }

    /**
     * Clear all tour-related caches
     *
     * @return bool
     */
    public function clearTourCaches(): bool
    {
        return $this->flushTags([
            self::CACHE_TAGS['tours'],
            self::CACHE_TAGS['statistics'],
            self::CACHE_TAGS['dashboard'],
        ]);
    }

    /**
     * Clear all user-related caches
     *
     * @return bool
     */
    public function clearUserCaches(): bool
    {
        return $this->flushTags([
            self::CACHE_TAGS['users'],
            self::CACHE_TAGS['dashboard'],
        ]);
    }

    /**
     * Clear all caches
     *
     * @return bool
     */
    public function clearAll(): bool
    {
        try {
            Cache::flush();
            Log::info('All caches cleared successfully');
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to clear all caches', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get cache size and statistics
     *
     * @return array
     */
    public function getStats(): array
    {
        try {
            // This is driver-specific. For Redis, we can get more detailed stats
            $driver = config('cache.default');

            $stats = [
                'driver' => $driver,
                'enabled' => true,
                'timestamp' => now()->toISOString(),
            ];

            // Add driver-specific stats if available
            if ($driver === 'redis') {
                $stats = array_merge($stats, $this->getRedisStats());
            }

            return $stats;

        } catch (\Exception $e) {
            Log::warning('Failed to get cache stats', [
                'error' => $e->getMessage(),
            ]);

            return [
                'driver' => config('cache.default'),
                'enabled' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    /**
     * Get Redis-specific cache statistics
     *
     * @return array
     */
    protected function getRedisStats(): array
    {
        try {
            $redis = Cache::getRedis();
            $info = $redis->info();

            return [
                'memory_used' => $info['used_memory_human'] ?? 'N/A',
                'keys_count' => $redis->dbsize(),
                'connected_clients' => $info['connected_clients'] ?? 'N/A',
                'hits' => $info['keyspace_hits'] ?? 0,
                'misses' => $info['keyspace_misses'] ?? 0,
                'hit_rate' => $this->calculateHitRate(
                    $info['keyspace_hits'] ?? 0,
                    $info['keyspace_misses'] ?? 0
                ),
            ];

        } catch (\Exception $e) {
            Log::warning('Failed to get Redis cache stats', [
                'error' => $e->getMessage(),
            ]);

            return ['redis_stats_error' => $e->getMessage()];
        }
    }

    /**
     * Calculate cache hit rate
     *
     * @param int $hits
     * @param int $misses
     * @return string
     */
    protected function calculateHitRate(int $hits, int $misses): string
    {
        $total = $hits + $misses;

        if ($total === 0) {
            return '0%';
        }

        $rate = ($hits / $total) * 100;
        return round($rate, 2) . '%';
    }

    /**
     * Warm up common caches
     *
     * @return array Results of warming operations
     */
    public function warmUp(): array
    {
        $results = [];

        try {
            // Warm up tour statistics
            $results['tour_statistics'] = $this->cacheStatistics(
                'tour_statistics_all',
                function () {
                    return \App\Tour::selectRaw('
                        COUNT(*) as total_tours,
                        SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as active_tours,
                        SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as completed_tours,
                        SUM(pax) as total_passengers
                    ')->first();
                }
            );

            // Warm up active users count
            $results['active_users'] = $this->cacheUsers(
                'active_users_count',
                function () {
                    return \App\User::where('is_active', true)->count();
                }
            );

            // Warm up upcoming tours
            $results['upcoming_tours'] = $this->cacheTours(
                'upcoming_tours_count',
                function () {
                    return \App\Tour::where('departure_date', '>', now())->count();
                }
            );

            Log::info('Cache warm-up completed successfully', $results);

        } catch (\Exception $e) {
            Log::error('Cache warm-up failed', [
                'error' => $e->getMessage(),
                'results' => $results,
            ]);

            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Check if caching is enabled and working
     *
     * @return bool
     */
    public function isWorking(): bool
    {
        try {
            $testKey = 'cache_test_' . time();
            $testValue = 'test_value_' . rand(1000, 9999);

            // Test put
            $putResult = $this->put($testKey, $testValue, 60);
            if (!$putResult) {
                return false;
            }

            // Test get
            $getValue = $this->get($testKey);
            if ($getValue !== $testValue) {
                return false;
            }

            // Test forget
            $forgetResult = $this->forget($testKey);

            // Verify it's gone
            $getAfterForget = $this->get($testKey, 'not_found');

            return $forgetResult && $getAfterForget === 'not_found';

        } catch (\Exception $e) {
            Log::warning('Cache working check failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}