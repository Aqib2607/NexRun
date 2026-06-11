<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    public static function getCacheKey(string $suffix = ''): string
    {
        $table = (new static)->getTable();
        return $suffix ? "{$table}:{$suffix}" : $table;
    }

    public static function cached(string $key, int $ttl, \Closure $callback)
    {
        return Cache::remember(static::getCacheKey($key), $ttl, $callback);
    }

    public static function clearCache(string $key = ''): void
    {
        if ($key) {
            Cache::forget(static::getCacheKey($key));
        } else {
            Cache::tags([static::getCacheKey()])->flush();
        }
    }

    protected static function bootCacheable(): void
    {
        static::saved(function () {
            try {
                Cache::tags([(new static)->getTable()])->flush();
            } catch (\Throwable $e) {
                // Tag-based flushing may not be supported by all drivers
            }
        });

        static::deleted(function () {
            try {
                Cache::tags([(new static)->getTable()])->flush();
            } catch (\Throwable $e) {
                // Tag-based flushing may not be supported by all drivers
            }
        });
    }
}
