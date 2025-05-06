<?php

namespace Botble\Base\Supports;

class BreadcrumbItem
{
    protected string $title;

    protected string $url;

    public static function make(): static
    {
        return app(static::class);
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title ?? '';
    }

    public function setRoute(string $name, array $parameters = [], bool $absolute = true): static
    {
        return $this->setUrl(route($name, $parameters, $absolute));
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url ?? '';
    }
}
