<?php

namespace Botble\Theme\Supports;

use Botble\Base\Facades\BaseHelper;
use Botble\Sitemap\Sitemap;
use Botble\Slug\Facades\SlugHelper;
use Carbon\Carbon;
use Illuminate\Http\Response;

class SiteMapManager
{
    protected array $keys = ['sitemap', 'pages'];

    protected array $removedKeys = [];

    protected string $extension = 'xml';

    protected string $defaultDate = '2024-11-01 00:00';

    public function __construct(protected Sitemap $siteMap)
    {
    }

    public function init(?string $prefix = null, string $extension = 'xml'): self
    {
        // create new site map object
        $this->siteMap = app('sitemap');
        // set cache (key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean))
        // by default cache is disabled
        $this->siteMap->setCache('cache_site_map_key' . $prefix . $extension, setting('cache_time_site_map', 60), setting('enable_cache_site_map', true));

        if ($prefix == 'pages' && ! BaseHelper::getHomepageId()) {
            $this->add(BaseHelper::getHomepageUrl(), Carbon::now()->toDateTimeString());
        }

        $this->extension = $extension;

        if (! $prefix) {
            $this->addSitemap($this->route('pages'));
        }

        return $this;
    }

    public function addSitemap(string $loc, ?string $lastModified = null): self
    {
        $removedLoc = array_map(fn ($item) => $this->route($item), $this->removedKeys);

        if (! $this->isCached() && ! in_array($loc, $removedLoc)) {
            $this->siteMap->addSitemap($loc, $lastModified ?: $this->defaultDate);
        }

        return $this;
    }

    public function route(?string $key = null): string
    {
        return route('public.sitemap.index', [$key, $this->extension]);
    }

    public function add(string $url, ?string $date = null, string $priority = '1.0', string $sequence = 'daily'): self
    {
        if (! $this->isCached()) {
            $this->siteMap->add($url, $date ?: $this->defaultDate, $priority, $sequence);
        }

        return $this;
    }

    public function isCached(): bool
    {
        return $this->siteMap->isCached();
    }

    public function getSiteMap(): Sitemap
    {
        return $this->siteMap;
    }

    public function render(string $type = 'xml'): Response
    {
        // show your site map (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        return $this->siteMap->render($type);
    }

    public function getKeys(): array
    {
        return array_filter($this->keys, fn ($item) => ! in_array($item, $this->removedKeys));
    }

    public function registerKey(string|array $key, ?string $value = null): self
    {
        if (is_array($key)) {
            $this->keys = array_merge($this->keys, $key);
        } else {
            $this->keys[$key] = $value ?: $key;
        }

        return $this;
    }

    public function removeKey(array|string $key): self
    {
        $this->removedKeys = [
            ...$this->removedKeys,
            ...(array) $key,
        ];

        return $this;
    }

    public function allowedExtensions(): array
    {
        $extensions = ['xml', 'xml-mobile', 'html', 'txt', 'ror-rss', 'ror-rdf', 'google-news'];

        $slugPostfix = SlugHelper::getPublicSingleEndingURL();

        if (! $slugPostfix) {
            return $extensions;
        }

        $slugPostfix = trim($slugPostfix, '.');

        return array_filter($extensions, fn ($item) => $item != $slugPostfix);
    }
}
