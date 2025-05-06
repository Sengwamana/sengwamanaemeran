<?php

namespace Botble\Base\Contracts\PanelSections;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

interface PanelSectionItem extends Htmlable, Renderable
{
    public static function make(string $id): static;

    public function setId(string $id): static;

    public function getId(): string;

    public function setSectionId(string $sectionId): static;

    public function getSectionId(): string;

    public function setTitle(string $title): static;

    public function getTitle(): string;

    public function withDescription(string $description): static;

    public function getDescription(): string;

    public function withIcon(string $icon): static;

    public function getIcon(): string;

    public function withPriority(int $priority): static;

    public function getPriority(): int;

    public function withView(string $view);

    public function getView(): string;

    public function withUrl(string $url): static;

    public function getUrl(): string;

    public function urlShouldOpenNewTab(bool $openNewTab = true): static;

    public function shouldOpenNewTab(): bool;

    public function withRoute(string $route, array $parameters = [], bool $absolute = true);

    public function withPermission(string $permission): static;

    public function withPermissions(array $permissions): static;

    public function getPermissions(): array;

    public function hasPermissions(): bool;

    public function withoutPermission(): static;

    public function checkPermissions();
}
