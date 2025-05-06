<?php

namespace Botble\Table\Columns;

use Botble\Base\Contracts\BaseModel;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Table\Contracts\FormattedColumn as FormattedColumnContract;
use Closure;

class LinkableColumn extends FormattedColumn implements FormattedColumnContract
{
    protected array $route;

    protected string $permission;

    protected string $url;

    protected bool $externalLink = false;

    protected Closure $urlUsingCallback;

    public static function make(array|string $data = [], string $name = ''): static
    {
        return parent::make($data, $name)->withEmptyState();
    }

    public function route(string $route, array $parameters = [], bool $absolute = true): static
    {
        $this->route = [$route, $parameters, $absolute];

        $this->permission($route);

        return $this;
    }

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function externalLink(bool $externalLink = true): static
    {
        $this->externalLink = $externalLink;

        return $this;
    }

    public function urlUsing(Closure $callback): static
    {
        $this->urlUsingCallback = $callback;

        return $this;
    }

    public function getUrl($value): ?string
    {
        if (isset($this->urlUsingCallback)) {
            return call_user_func($this->urlUsingCallback, $this);
        }

        if (isset($this->route)) {
            $item = $this->getItem();

            return route(
                $this->route[0],
                $this->route[1] ?: ($item instanceof BaseModel ? $item->getKey() : null),
                $this->route[2]
            );
        }

        return $this->url ?? $value;
    }

    public function permission(string $permission): static
    {
        $this->permission = $permission;

        return $this;
    }

    public function getPermission(): ?string
    {
        if (isset($this->permission)) {
            return $this->permission;
        }

        return null;
    }

    public function formattedValue($value): ?string
    {
        $item = $this->getItem();

        if (! $item instanceof BaseModel) {
            return $value;
        }

        if (! isset($this->getValueUsingCallback)) {
            $value = BaseHelper::clean($value);
        }

        $valueTruncated = $this->applyLimitIfAvailable($value);

        if (
            ($permission =  $this->getPermission())
            && ! $this->getTable()->hasPermission($permission)
        ) {
            return $valueTruncated ?: null;
        }

        $attributes = ['title' => $this->getOriginalValue()];
        $link = $valueTruncated;

        if ($this->externalLink) {
            $attributes['target'] = '_blank';
            $valueTruncated = $valueTruncated . $this->renderExternalLinkIcon();
        }

        if ($this->hasColor()) {
            $attributes['class'] = 'text-' . $this->color;
        }

        if ($url = $this->getUrl($value)) {
            $link = Html::link(
                $url,
                $valueTruncated,
                $attributes,
                escape: ! $this->externalLink
            )->toHtml();
        }

        return apply_filters('table_name_column_data', $link, $item, $this);
    }

    protected function renderExternalLinkIcon(): string
    {
        return view('core/table::cells.icon', [
            'icon' => 'ti ti-external-link',
            'positionClass' => 'ms-1',
        ])->render();
    }
}
