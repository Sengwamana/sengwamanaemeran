<?php

namespace Botble\Base\Contracts\PanelSections;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

interface Manager extends Htmlable, Renderable
{
    public function group(string $groupId): static;

    public function setGroupId(string $groupId): static;

    public function getGroupId(): string;

    public function setGroupName(string $name): static;

    public function getGroupName(): string;

    public function default(): static;

    public function moveGroup(string $from, string $to): static;

    /**
     * @param array<int, class-string<PanelSection>>|class-string<PanelSection>|\Closure(): PanelSection $panelSections
     */
    public function register(array|string|Closure $panelSections): static;

    /**
     * @return array<array<int, PanelSection>>
     */
    public function getAllSections(): array;

    /**
     * @return array<int, PanelSection>
     */
    public function getSections(): array;

    /**
     * @param class-string<PanelSection> $section
     * @param \Closure(): class-string<PanelSectionItem> $item
     */
    public function registerItem(string $section, Closure $item): static;

    /**
     * @param class-string<PanelSection> $section
     * @param \Closure(): array<array<int, class-string<PanelSectionItem>>|class-string<PanelSectionItem>> $items
     */
    public function registerItems(string $section, Closure $items): static;

    /**
     * @return array<int, PanelSectionItem>
     */
    public function getItems(string $section): array;

    public function ignoreItemId(string $id): static;

    public function ignoreItemIds(array $ids): static;

    public function isIgnoredItemIds(string $id): bool;

    public function beforeRendering(Closure|callable $callback, int $priority = 100): static;

    public function afterRendering(Closure|callable $callback, int $priority = 100): static;
}
