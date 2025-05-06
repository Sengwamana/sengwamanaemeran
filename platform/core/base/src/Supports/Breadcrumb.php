<?php

namespace Botble\Base\Supports;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\DashboardMenu as DashboardMenuFacade;
use Botble\Base\Facades\PageTitle as PageTitleFacade;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Breadcrumb implements Htmlable
{
    use Renderable;

    protected array $items = [];

    protected string $currentGroup = 'admin';

    protected string $view = 'core/base::breadcrumb';

    public function for(string $group): Breadcrumb
    {
        $this->currentGroup = $group;

        return $this;
    }

    public function default(): Breadcrumb
    {
        return $this->for('admin');
    }

    public function add(string $label, string $url = ''): static
    {
        $label = BaseHelper::clean($label);

        $this->items[$this->currentGroup][$label] = compact('label', 'url');

        return $this;
    }

    public function prepend(string $label, string $url = ''): static
    {
        $label = BaseHelper::clean($label);

        $breadcrumb = $this->items[$this->currentGroup] ??= [];

        $this->items[$this->currentGroup] = [...[$label => compact('label', 'url')], ...$breadcrumb];

        return $this;
    }

    public function getItems(): Collection
    {
        if (empty($this->items[$this->currentGroup])) {
            $this->fallbackGenerateBreadcrumbs();
        }

        if (empty($this->items[$this->currentGroup])) {
            return collect();
        }

        return collect($this->items[$this->currentGroup])->values();
    }

    public function render(): string
    {
        return $this->rendering(
            fn () => view($this->view, [
                'items' => $this->getItems(),
            ])->render()
        );
    }

    public function toHtml(): string
    {
        return $this->render();
    }

    protected function fallbackGenerateBreadcrumbs(): void
    {
        if ($this->currentGroup === 'admin') {
            $this->add(trans('core/dashboard::dashboard.title'), route('dashboard.index'));
        }

        $prefix = '/' . ltrim(request()->route()->getPrefix(), '/');
        $url = URL::current();
        $arMenu = DashboardMenuFacade::getAll();

        $found = false;
        foreach ($arMenu as $menuCategory) {
            if ((
                $url == $menuCategory['url']
                    || (Str::contains((string) $menuCategory['url'], $prefix) && $prefix != '//')
            )
                && ! empty($menuCategory['name'])
            ) {
                $found = true;
                $this->add(trans($menuCategory['name']), $menuCategory['url']);

                break;
            }
        }

        if (! $found) {
            foreach ($arMenu as $menuCategory) {
                if (empty($menuCategory['children'])) {
                    continue;
                }

                foreach ($menuCategory['children'] as $menuItem) {
                    if (
                        (
                            $url == $menuItem['url']
                            || (Str::contains((string) $menuItem['url'], $prefix) && $prefix != '//')
                        )
                        && ! empty($menuItem['name'])
                    ) {
                        $this->add(trans($menuCategory['name']), $menuCategory['url']);
                        $this->add(trans($menuItem['name']), $menuItem['url']);

                        break;
                    }
                }
            }
        }

        $currentTitle = PageTitleFacade::getTitle(false);

        if (! isset($this->items[$this->currentGroup][$currentTitle])) {
            $this->add($currentTitle, $url);
        }
    }
}
