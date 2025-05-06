<?php

namespace Botble\Base\PanelSections;

use Botble\ACL\Contracts\HasPermissions;
use Botble\Base\Contracts\PanelSections\PanelSection as PanelSectionContract;
use Botble\Base\Contracts\PanelSections\PanelSectionItem as PanelSectionItemContract;
use Botble\Base\Events\PanelSectionItemsRendered;
use Botble\Base\Events\PanelSectionItemsRendering;
use Botble\Base\Events\PanelSectionRendered;
use Botble\Base\Events\PanelSectionRendering;
use Botble\Base\Facades\PanelSectionManager;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class PanelSection implements PanelSectionContract
{
    protected string $id;

    protected string $title;

    protected string $groupId;

    protected string $description = '';

    protected int $priority = 0;

    protected ?array $permissions = null;

    protected array $items = [];

    protected string $view = 'core/base::sections.section';

    protected string $emptyStateView = 'core/base::sections.empty';

    protected bool $emptyState = false;

    protected array $partials = [];

    public function __construct()
    {
        $this->id = uniqid('panel-section-');

        $this->items = $this->items();

        $this->setup();
    }

    public static function make(string $id): static
    {
        return app(static::class)->setId($id);
    }

    public function setup(): void
    {
        //
    }

    public function afterSetup(): void
    {
        //
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

    public function setGroupId(string $groupId): static
    {
        $this->groupId = $groupId;

        return $this;
    }

    public function getGroupId(): string
    {
        return $this->groupId;
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

    public function withPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function withPermissions(array $permissions): static
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function withoutPermission(): static
    {
        $this->permissions = null;

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

    public function checkPermissions(): bool
    {
        if (! $this->hasPermissions()) {
            return true;
        }

        $user = Auth::guard()->user();

        if (! $user || ($user instanceof HasPermissions && ! $user->hasAnyPermission($this->permissions))) {
            return false;
        }

        return true;
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

    public function withEmptyStateView(string $view = null): static
    {
        $this->emptyState = true;

        if ($view) {
            $this->emptyStateView = $view;
        }

        return $this;
    }

    public function withoutEmptyStateView(): static
    {
        $this->emptyState = false;

        return $this;
    }

    public function getEmptyStateView(): string
    {
        return $this->emptyStateView;
    }

    public function withItems(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function addItems(array|Closure $items): static
    {
        foreach ($items as $item) {
            if ($item instanceof Closure) {
                $itemsClosure = $item();
                $itemsClosure = Arr::wrap($itemsClosure);

                foreach ($itemsClosure as $itemClosure) {
                    $this->items[] = $itemClosure;
                }

                continue;
            }

            $this->items[] = $item;
        }

        return $this;
    }

    public function items(): array
    {
        return [];
    }

    public function getItems(): array
    {
        return collect($this->items)
            ->map(fn ($item) => is_string($item) ? app($item) : $item)
            ->filter(fn ($item) => $item instanceof PanelSectionItemContract)
            ->filter(fn (PanelSectionItemContract $item) => $item->checkPermissions())
            ->sortBy(fn (PanelSectionItemContract $item) => $item->getPriority())
            ->unique(fn (PanelSectionItemContract $item) => $item->setSectionId($this->getId())->getId())
            ->all();
    }

    public function renderEmptyState(): string
    {
        return view($this->getEmptyStateView())->render();
    }

    public function renderItems(): string
    {
        $items = apply_filters('panel_section_items', $this->getItems(), $this);

        $content = '';

        do_action('panel_section_items_rendering', $this, $items);

        PanelSectionItemsRendering::dispatch($this, $items);

        $items = collect($items)->reject(
            fn (PanelSectionItemContract $item)
                => PanelSectionManager::group($this->groupId)->isIgnoredItemIds($item->getId())
        )->all();

        foreach ($items as $item) {
            $content .= $item->render();
        }

        $content = apply_filters('panel_section_items_content', $content, $this);

        return tap($content, function (string $content) use ($items): void {
            PanelSectionItemsRendered::dispatch($this, $items, $content);

            do_action('panel_section_items_rendered', $this, $items, $content);
        });
    }

    public function render(): string
    {
        if (! $this->checkPermissions()) {
            return '';
        }

        do_action('panel_section_rendering', $this);

        PanelSectionRendering::dispatch($this);

        $data = $this->getDataForView();

        if ($data['children']->isEmpty() && $this->emptyState) {
            $data['children'] = new HtmlString(
                $this->renderEmptyState()
            );
        }

        $content = $data['children']->isNotEmpty() ? view(
            $this->view,
            $data,
            isset($this->mergedDataCallback)
                ? app()->call($this->mergedDataCallback)
                : []
        )->render() : '';

        $content = apply_filters('panel_section_content', $content, $this);

        return tap(
            $content,
            function (string $content): void {
                PanelSectionRendered::dispatch($this, $content);

                do_action('panel_section_rendered', $this, $content);
            }
        );
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
            'priority' => $this->getPriority(),
            'groupId' => $this->getGroupId(),
            'children' => new HtmlString(
                $this->renderItems()
            ),
        ];
    }
}
