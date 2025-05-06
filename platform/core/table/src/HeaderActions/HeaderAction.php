<?php

namespace Botble\Table\HeaderActions;

use Botble\Base\Supports\Builders\HasAttributes;
use Botble\Base\Supports\Builders\HasColor;
use Botble\Base\Supports\Builders\HasIcon;
use Botble\Base\Supports\Builders\HasLabel;
use Botble\Base\Supports\Builders\HasPermissions;
use Botble\Base\Supports\Builders\HasUrl;
use Illuminate\Contracts\Support\Arrayable;

class HeaderAction implements Arrayable
{
    use HasAttributes;
    use HasColor;
    use HasIcon;
    use HasLabel;
    use HasPermissions;
    use HasUrl;

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

    public function toArray(): array
    {
        return [
            'className' => $this->getClassName(),
            'text' => view('core/table::includes.header-action', ['action' => $this])->render(),
        ];
    }

    public function route(string $route, array $parameters = [], bool $absolute = true): static
    {
        $this
            ->url(fn (HeaderAction $action) => route($route, $parameters, $absolute))
            ->permission($route);

        return $this;
    }

    public function getClassName(): string
    {
        return sprintf(
            '%s %s %s',
            $this->getAttribute('data-default-action', 'true') ? 'action-item' : '',
            $this->getColor(),
            $this->getAttribute('class')
        );
    }

    public function withDefaultAction(bool $isDefault = true): static
    {
        return $this->addAttribute('data-default-action', $isDefault);
    }
}
