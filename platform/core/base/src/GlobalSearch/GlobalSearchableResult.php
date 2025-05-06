<?php

namespace Botble\Base\GlobalSearch;

use Botble\Base\Contracts\GlobalSearchableResult as GlobalSearchableResultContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class GlobalSearchableResult implements GlobalSearchableResultContract, JsonSerializable, Arrayable, Jsonable
{
    public function __construct(
        protected string $title,
        protected string $description = '',
        protected array $parents = [],
        protected string $url = '',
        protected bool $shouldOpenNewTab = false,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function shouldOpenNewTab(): bool
    {
        return $this->shouldOpenNewTab;
    }

    public function getParents(): array
    {
        return $this->parents;
    }

    public function getUniqueId(): string
    {
        return "{$this->getTitle()}-{$this->getUrl()}";
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'parents' => $this->parents,
            'url' => $this->url,
            'shouldOpenNewTab' => $this->shouldOpenNewTab,
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
