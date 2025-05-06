<?php

namespace Botble\Base\PanelSections;

use Botble\Base\Contracts\PanelSections\Manager as ManagerContract;
use Botble\Base\Contracts\PanelSections\PanelSection as PanelSectionContract;
use Botble\Base\Events\PanelSectionsRendered;
use Botble\Base\Events\PanelSectionsRendering;
use Closure;
use Illuminate\Support\Arr;

class Manager implements ManagerContract
{
    public function __construct(
        protected string $groupId = 'settings',
        protected array $groups = [],
        protected array $groupNames = [],
        protected array $sections = [],
        protected array $sectionItems = [],
        protected array $ignoreItemIds = [],
        protected array $movedGroups = []
    ) {
        $this->default();
    }

    public function group(string $groupId): static
    {
        return $this->setGroupId($groupId);
    }

    public function setGroupId(string $groupId): static
    {
        $this->groupId = $groupId;
        $this->groups[$groupId] = true;

        return $this;
    }

    public function getGroupId(): string
    {
        return $this->groupId;
    }

    public function setGroupName(string $name): static
    {
        $this->groupNames[$this->groupId] = $name;

        return $this;
    }

    public function getGroupName(): string
    {
        return $this->groupNames[$this->groupId] ?? '';
    }

    public function default(): static
    {
        return $this->group('settings');
    }

    public function moveGroup(string $from, string $to): static
    {
        $this->movedGroups[$to][] = $from;

        return $this;
    }

    public function register(array|string|Closure $panelSections): static
    {
        foreach (Arr::wrap($panelSections) as $panelSection) {
            $this->sections[$this->groupId][] = $panelSection;
        }

        return $this;
    }

    public function getAllSections(): array
    {
        $groups = array_keys($this->groups);
        $sections = [];

        foreach ($groups as $group) {
            $this->group($group);

            $this->dispatchBeforeRendering();

            $currentSections = $sections[$group] ?? [];
            $sections[$group] = [...$currentSections, ...$this->getSections()];

            $this->dispatchAfterRendering();
        }

        $this->default();

        return $sections;
    }

    public function getSections(): array
    {
        $sections = $this->sections[$this->groupId] ?? [];

        return collect($sections)
            ->map(
                fn (string|Closure $panelSection)
                => is_string($panelSection) ? app($panelSection) : value($panelSection)
            )
            ->filter(fn (object $panelSection) => $panelSection instanceof PanelSectionContract)
            ->filter(fn (PanelSectionContract $panelSection) => $panelSection->checkPermissions())
            ->sortBy(fn (PanelSectionContract $panelSection) => $panelSection->getPriority())
            ->unique(fn (PanelSectionContract $panelSection) => $panelSection->getId())
            ->map(function (PanelSectionContract $panelSection) {
                return $panelSection
                    ->setGroupId($this->groupId)
                    ->addItems(
                        $this->getItems($panelSection::class)
                    );
            })
            ->each(fn (PanelSectionContract $panelSection) => $panelSection->afterSetup())
            ->tap(fn () => $this->default())
            ->all();
    }

    public function registerItem(string $section, Closure $item): static
    {
        // @phpstan-ignore-next-line
        return $this->registerItems($section, $item);
    }

    public function registerItems(string $section, Closure $items): static
    {
        $this->sectionItems[$this->groupId][$section][] = $items;

        return $this;
    }

    public function getItems(string $section): array
    {
        return $this->sectionItems[$this->groupId][$section] ?? [];
    }

    public function ignoreItemId(string $id): static
    {
        return $this->ignoreItemIds([$id]);
    }

    public function ignoreItemIds(array $ids): static
    {
        $this->ignoreItemIds[$this->groupId] = array_merge($this->ignoreItemIds[$this->groupId] ?? [], $ids);

        return $this;
    }

    public function isIgnoredItemIds(string $id): bool
    {
        return in_array($id, $this->ignoreItemIds[$this->groupId] ?? []);
    }

    public function render(): string
    {
        $this->dispatchBeforeRendering();

        $sections = apply_filters('panel_sections', $this->getSections(), $this->groupId, $this);

        $content = '';

        foreach ($sections as $section) {
            $content .= $section->render();
        }

        $content = apply_filters('panel_sections_content', $content, $this->groupId, $sections, $this);

        $this->dispatchAfterRendering();

        if (! empty($this->movedGroups[$this->groupId])) {
            $movedGroups = array_unique($this->movedGroups[$this->groupId]);

            foreach ($movedGroups as $group) {
                $content .= $this->group($group)->render();
            }
        }

        return $content;
    }

    public function toHtml(): string
    {
        return $this->render();
    }

    public function beforeRendering(Closure|callable $callback, int $priority = 100): static
    {
        add_action($this->filterPrefix() . '_rendering', $callback, $priority);

        $this->default();

        return $this;
    }

    public function afterRendering(Closure|callable $callback, int $priority = 100): static
    {
        add_action($this->filterPrefix() . '_rendered', $callback, $priority);

        $this->default();

        return $this;
    }

    protected function dispatchBeforeRendering(): void
    {
        do_action('panel_sections_rendering', $this);

        do_action($this->filterPrefix() . '_rendering', $this);

        PanelSectionsRendering::dispatch($this);
    }

    protected function dispatchAfterRendering(): void
    {
        do_action('panel_sections_rendered', $this);

        do_action($this->filterPrefix() . '_rendered', $this);

        PanelSectionsRendered::dispatch($this);
    }

    protected function filterPrefix(): string
    {
        return 'panel_sections_' . $this->groupId;
    }
}
