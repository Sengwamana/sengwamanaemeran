<?php

namespace Botble\Sitemap;

use Botble\Base\Facades\BaseHelper;
use Carbon\Carbon;
use Datetime;

class Model
{
    protected array $items = [];

    protected array $sitemaps = [];

    protected ?string $title = null;

    protected ?string $link = null;

    protected mixed $useStyles = true;

    protected string $sloc = '/vendor/core/packages/sitemap/styles/';

    protected bool $useCache = false;

    protected string $cacheKey = 'cms-sitemap.';

    protected int $cacheDuration = 60;

    protected bool $escaping = true;

    protected bool $useLimitSize = false;

    protected bool|int|null $maxSize = null;

    protected bool $useGzip = false;

    /**
     * Populating model variables from configuration file.
     */
    public function __construct(array $config)
    {
        $this->useCache = $config['use_cache'] ?? $this->useCache;
        $this->cacheKey = $config['cache_key'] ?? $this->cacheKey;
        $this->cacheDuration = $config['cache_duration'] ?? $this->cacheDuration;
        $this->escaping = $config['escaping'] ?? $this->escaping;
        $this->useLimitSize = $config['use_limit_size'] ?? $this->useLimitSize;
        $this->useStyles = $config['use_styles'] ?? $this->useStyles;
        $this->sloc = $config['styles_location'] ?? $this->sloc;
        $this->maxSize = $config['max_size'] ?? $this->maxSize;
        $this->useGzip = $config['use_gzip'] ?? $this->useGzip;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getSitemaps(): array
    {
        return $this->sitemaps;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function isUseStyles(): bool
    {
        return $this->useStyles;
    }

    public function getSloc(): string
    {
        return $this->sloc;
    }

    public function isUseCache(): bool
    {
        return $this->useCache;
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey . BaseHelper::getHomepageUrl();
    }

    public function getCacheDuration(): int|string
    {
        return $this->cacheDuration;
    }

    public function isEscaping(): bool
    {
        return $this->escaping;
    }

    public function isUseLimitSize(): bool
    {
        return $this->useLimitSize;
    }

    public function getMaxSize(): bool|int|null
    {
        return $this->maxSize;
    }

    public function getUseGzip(): bool
    {
        return $this->useGzip;
    }

    public function setEscaping(bool $escaping): static
    {
        $this->escaping = $escaping;

        return $this;
    }

    public function setItems(array $items): static
    {
        $this->items[] = $items;

        return $this;
    }

    public function setSitemaps(array $sitemap): static
    {
        $this->sitemaps[] = $sitemap;

        return $this;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function setUseStyles(bool $useStyles): static
    {
        $this->useStyles = $useStyles;

        return $this;
    }

    public function setSloc(string $sloc): static
    {
        $this->sloc = $sloc;

        return $this;
    }

    public function setUseLimitSize(bool $useLimitSize): static
    {
        $this->useLimitSize = $useLimitSize;

        return $this;
    }

    public function setMaxSize(int $maxSize): static
    {
        $this->maxSize = $maxSize;

        return $this;
    }

    public function setUseGzip(bool $useGzip = true): static
    {
        $this->useGzip = $useGzip;

        return $this;
    }

    /**
     * Limit size of $items array to 50000 elements (1000 for google-news).
     */
    public function limitSize(int $max = 50000): static
    {
        $this->items = array_slice($this->items, 0, $max);

        return $this;
    }

    public function resetItems(array $items = []): static
    {
        $this->items = $items;

        return $this;
    }

    public function resetSitemaps(array $sitemaps = []): static
    {
        $this->sitemaps = $sitemaps;

        return $this;
    }

    public function setUseCache(bool $useCache = true): static
    {
        $this->useCache = $useCache;

        return $this;
    }

    public function setCacheKey(string $cacheKey): static
    {
        $this->cacheKey = $cacheKey;

        return $this;
    }

    public function setCacheDuration(Carbon|Datetime|int $cacheDuration): static
    {
        $this->cacheDuration = $cacheDuration;

        return $this;
    }
}
