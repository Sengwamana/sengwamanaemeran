<?php

namespace Botble\Support\Services\Cache;

use Botble\Base\Facades\BaseHelper;
use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class Cache implements CacheInterface
{
    final public function __construct(
        protected CacheManager $cache,
        protected ?string $cacheGroup,
        protected array $config = []
    ) {
        $this->config = ! empty($config) ? $config : [
            'cache_time' => 10 * 60,
            'stored_keys' => storage_path('cache_keys.json'),
        ];
    }

    public static function make(string $group): static
    {
        return new static(app(CacheManager::class), $group);
    }

    public function get(string $key): mixed
    {
        if (! file_exists($this->config['stored_keys'])) {
            return null;
        }

        return $this->cache->get($this->generateCacheKey($key));
    }

    public function generateCacheKey(string $key): string
    {
        return md5($this->cacheGroup) . '@' . $key;
    }

    public function put(string $key, $value, Closure|DateTimeInterface|DateInterval|int|null $ttl = null): bool
    {
        if (! $ttl) {
            $ttl = $this->config['cache_time'];
        }

        if ($ttl === -1) {
            $ttl = null;
        }

        $key = $this->generateCacheKey($key);

        $this->storeCacheKey($key);

        $this->cache->put($key, $value, $ttl);

        return true;
    }

    public function forever(string $key, mixed $value): bool
    {
        return $this->put($key, $value, -1);
    }

    public function remember(string $key, Closure|DateTimeInterface|DateInterval|int|null $ttl, Closure $callback): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = value($callback);

        $this->put($key, $value, $ttl);

        return $value;
    }

    public function rememberForever(string $key, Closure $callback): mixed
    {
        return $this->remember($key, -1, $callback);
    }

    public function storeCacheKey(string $key): bool
    {
        if (File::exists($this->config['stored_keys'])) {
            $cacheKeys = BaseHelper::getFileData($this->config['stored_keys']);
            if (! empty($cacheKeys) && ! in_array($key, Arr::get($cacheKeys, $this->cacheGroup, []))) {
                $cacheKeys[$this->cacheGroup][] = $key;
            }
        } else {
            $cacheKeys = [];
            $cacheKeys[$this->cacheGroup][] = $key;
        }

        BaseHelper::saveFileData($this->config['stored_keys'], $cacheKeys);

        return true;
    }

    public function has(string $key): bool
    {
        if (! File::exists($this->config['stored_keys'])) {
            return false;
        }

        $key = $this->generateCacheKey($key);

        return $this->cache->has($key);
    }

    public function forget(string $key): bool
    {
        return $this->cache->forget($key);
    }

    public function flush(): bool
    {
        $cacheKeys = [];
        if (File::exists($this->config['stored_keys'])) {
            $cacheKeys = BaseHelper::getFileData($this->config['stored_keys']);
        }

        if (! empty($cacheKeys) && $caches = Arr::get($cacheKeys, $this->cacheGroup)) {
            foreach ($caches as $cache) {
                $this->cache->forget($cache);
            }

            unset($cacheKeys[$this->cacheGroup]);
        }

        if (! empty($cacheKeys)) {
            BaseHelper::saveFileData($this->config['stored_keys'], $cacheKeys);
        } elseif (File::exists($this->config['stored_keys'])) {
            File::delete($this->config['stored_keys']);
        }

        return true;
    }

    public function generateCacheKeyFromInput(): string
    {
        return serialize(request()->input()) . serialize(BaseHelper::getHomepageUrl());
    }
}
