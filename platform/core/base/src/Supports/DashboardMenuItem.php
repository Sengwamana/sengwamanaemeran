<?php

namespace Botble\Base\Supports;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;

class DashboardMenuItem implements Arrayable
{
    use Conditionable;
    use Tappable;

    protected string $id;

    protected int $priority = 99;

    protected ?string $parentId = null;

    protected string $name = '';

    protected ?string $icon = null;

    protected string|Closure $url = '';

    protected string $route = '';

    protected array|bool $permissions = [];

    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function priority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function parentId(?string $parentId): static
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url instanceof Closure ? call_user_func($this->url) : $this->url;
    }

    public function url(string|Closure $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function route(string $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function getPermissions(): array|bool
    {
        return $this->permissions;
    }

    public function permission(string $permission): static
    {
        $this->permissions[] = $permission;

        return $this;
    }

    public function permissions(string|array|bool $permissions): static
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $this->permissions = $permissions;

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'priority' => $this->getPriority(),
            'parent_id' => $this->getParentId(),
            'name' => $this->getName(),
            'icon' => $this->getIcon(),
            'url' => $this->getUrl(),
            'route' => $this->getRoute(),
            'permissions' => $this->getPermissions(),
        ];
    }
}
