<?php

namespace Botble\Support\Services\Cache;

use Closure;
use DateInterval;
use DateTimeInterface;

interface CacheInterface
{
    public function get(string $key): mixed;

    public function put(string $key, mixed $value, Closure|DateTimeInterface|DateInterval|int|null $ttl = null): bool;

    public function forever(string $key, mixed $value): bool;

    public function remember(string $key, Closure|DateTimeInterface|DateInterval|int|null $ttl, Closure $callback): mixed;

    public function rememberForever(string $key, Closure $callback): mixed;

    public function has(string $key): bool;

    public function forget(string $key): bool;

    public function flush(): bool;
}
