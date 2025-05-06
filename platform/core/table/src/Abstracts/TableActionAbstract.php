<?php

namespace Botble\Table\Abstracts;

use Botble\Base\Supports\Builders\HasLabel;
use Botble\Base\Supports\Builders\HasPermissions;
use Botble\Base\Supports\Renderable;
use Botble\Table\Abstracts\Concerns\HasConfirmation;
use Botble\Table\Abstracts\Concerns\HasPriority;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Traits\Conditionable;
use Stringable;

abstract class TableActionAbstract implements Htmlable, Stringable
{
    use Conditionable;
    use HasConfirmation;
    use HasLabel;
    use HasPermissions;
    use HasPriority;
    use Renderable;

    protected object $item;

    protected string $view = 'core/table::actions.action';

    protected string $dropdownItemView = 'core/table::actions.action-dropdown-item';

    protected array $mergeData = [];

    protected bool $displayAsDropdown = false;

    public function __construct(protected string $name)
    {
    }

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setItem(object $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getItem(): object
    {
        return $this->item;
    }

    public function view(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function displayAsDropdownItem(bool $enabled = true): static
    {
        $this->displayAsDropdown = $enabled;

        return $this;
    }

    public function dropdownItemView(string $view): static
    {
        $this->displayAsDropdownItem();

        $this->dropdownItemView = $view;

        return $this;
    }

    public function getDropdownItemView(): string
    {
        return $this->dropdownItemView;
    }

    public function dataForView(array $mergeData): static
    {
        $this->mergeData = $mergeData;

        return $this;
    }

    public function getDataForView(): array
    {
        return array_merge([
            'action' => $this,
        ], $this->mergeData);
    }

    public function render(): string
    {
        return $this->rendering(
            fn () => view(
                $this->displayAsDropdown ? $this->getDropdownItemView() : $this->getView(),
                $this->getDataForView()
            )->render()
        );
    }

    public function toHtml(): string
    {
        return $this->render();
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
