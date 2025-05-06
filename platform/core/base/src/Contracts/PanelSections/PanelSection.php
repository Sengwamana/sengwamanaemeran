<?php

namespace Botble\Base\Contracts\PanelSections;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

interface PanelSection extends Htmlable, Renderable
{
    public function setup(): void;

    public function afterSetup(): void;

    public function setId(string $id): static;

    public function getId(): string;

    public function setGroupId(string $groupId): static;

    public function getGroupId(): string;

    public function setTitle(string $title): static;

    public function getTitle(): string;

    public function withDescription(string $description): static;

    public function getDescription(): string;

    public function withPriority(int $priority): static;

    public function getPriority(): int;

    public function withPermissions(array $permissions): static;

    public function withoutPermission(): static;

    public function getPermissions(): array;

    public function hasPermissions(): bool;

    public function checkPermissions(): bool;

    public function withView(string $view): static;

    public function getView(): string;

    public function withEmptyStateView(string $view): static;

    public function withoutEmptyStateView(): static;

    public function getEmptyStateView(): string;

    /**
     * @param array<int, class-string<PanelSectionItem>|PanelSectionItem> $items
     */
    public function withItems(array $items): static;

    /**
     * @param array<int, class-string<PanelSectionItem>|PanelSectionItem> $items
     */
    public function addItems(array|Closure $items): static;

    /**
     * @return array<int, PanelSectionItem>
     */
    public function items(): array;

    /**
     * @return array<int, PanelSectionItem>
     */
    public function getItems(): array;

    public function renderEmptyState(): string;

    public function renderItems(): string;

}
