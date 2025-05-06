<?php

namespace Botble\Base\PanelSections;

use Botble\ACL\Contracts\HasPermissions;
use Botble\Base\Contracts\PanelSections\PanelSectionItem as PanelSectionItemContract;
use Botble\Base\Events\PanelSectionItemRendered;
use Botble\Base\Events\PanelSectionItemRendering;

class PanelSectionItem implements PanelSectionItemContract
{
    protected string $id;

    protected string $sectionId;

    protected string $title;

    protected string $description = '';

    protected string $icon = 'ti ti-box';

    protected int $priority = 0;

    protected ?array $permissions = null;

    protected string $view = 'core/base::sections.item';

    protected string $url = '';

    protected bool $urlShouldOpenNewTab = false;

    public static function make(string $id): static
    {
        return app(static::class)->setId($id);
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setSectionId(string $sectionId): static
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    public function getSectionId(): string
    {
        return $this->sectionId;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function withDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function withIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function withPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function withView(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function withUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function urlShouldOpenNewTab(bool $openNewTab = true): static
    {
        $this->urlShouldOpenNewTab = $openNewTab;

        return $this;
    }

    public function shouldOpenNewTab(): bool
    {
        return $this->urlShouldOpenNewTab;
    }

    public function withRoute(string $route, array $parameters = [], bool $absolute = true): static
    {
        return $this
            ->withUrl(route($route, $parameters, $absolute))
            ->withPermission($route);
    }

    public function withPermission(string $permission): static
    {
        $this->permissions[] = $permission;

        return $this;
    }

    public function withPermissions(array $permissions): static
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function getPermissions(): array
    {
        return $this->permissions ?? [];
    }

    public function hasPermissions(): bool
    {
        return $this->permissions !== null;
    }

    public function withoutPermission(): static
    {
        $this->permissions = null;

        return $this;
    }

    public function checkPermissions(): bool
    {
        if (! $this->hasPermissions()) {
            return true;
        }

        $user = auth()->guard()->user();

        if (! $user || ($user instanceof HasPermissions && ! $user->hasAnyPermission($this->permissions))) {
            return false;
        }

        return true;
    }

    public function render(): string
    {
        do_action('panel_section_item_rendering', $this);

        PanelSectionItemRendering::dispatch($this);

        $content = view(
            $this->view,
            $this->getDataForView()
        )->render();

        $content = apply_filters('panel_section_item_content', $content, $this);

        PanelSectionItemRendered::dispatch($this, $content);

        do_action('panel_sections_rendered', $this, $content);

        return $content;
    }

    public function toHtml(): string
    {
        return $this->render();
    }

    protected function getDataForView(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'icon' => $this->getIcon(),
            'url' => $this->getUrl(),
            'urlShouldOpenNewTab' => $this->shouldOpenNewTab(),
            'priority' => $this->getPriority(),
            'permissions' => $this->getPermissions(),
            'sectionId' => $this->getSectionId(),
        ];
    }
}
