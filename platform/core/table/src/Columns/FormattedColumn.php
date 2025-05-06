<?php

namespace Botble\Table\Columns;

use Botble\Base\Contracts\BaseModel;
use Botble\Base\Supports\Renderable;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Columns\Concerns\Blurrable;
use Botble\Table\Columns\Concerns\Copyable;
use Botble\Table\Columns\Concerns\HasColor;
use Botble\Table\Columns\Concerns\HasEmptyState;
use Botble\Table\Columns\Concerns\HasIcon;
use Botble\Table\Columns\Concerns\Maskable;
use Botble\Table\Contracts\FormattedColumn as FormattedColumnContract;
use Closure;
use Illuminate\Support\Str;
use stdClass;

class FormattedColumn extends Column implements FormattedColumnContract
{
    use Blurrable;
    use Copyable;
    use HasEmptyState;
    use HasColor;
    use HasIcon;
    use Renderable;
    use Maskable;

    protected TableAbstract $table;

    protected int $limit;

    protected array $getValueUsingCallbacks = [];

    protected object $item;

    protected array $appendCallbacks = [];

    protected array $prependCallbacks = [];

    public static function make(array|string $data = [], string $name = ''): static
    {
        return parent::make($data, $name)
            ->renderUsing(fn (FormattedColumn $column, $value) => $column->formattedValue($value))
            ->getValueUsing(fn (FormattedColumn $column, mixed $value) => $column->applyLimitIfAvailable($value));
    }

    public function limit(int $length = 5): static
    {
        $this->limit = $length;

        return $this;
    }

    public function applyLimitIfAvailable(mixed $text): mixed
    {
        if (isset($this->limit) && $this->limit > 0 && is_string($text)) {
            return Str::limit($text, $this->limit);
        }

        return $text ?: '';
    }

    public function formattedValue($value): ?string
    {
        return $value;
    }

    public function setTable(TableAbstract $table): static
    {
        $this->table = $table;

        return $this;
    }

    public function getTable(): TableAbstract
    {
        return $this->table;
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

    public function append(Closure $callback): static
    {
        $this->appendCallbacks[] = $callback;

        return $this;
    }

    protected function renderAppends(): string
    {
        $rendered = '';

        foreach ($this->appendCallbacks as $callback) {
            $rendered .= call_user_func($callback, $this);
        }

        return $rendered;
    }

    public function prepend(Closure $callback): static
    {
        $this->prependCallbacks[] = $callback;

        return $this;
    }

    protected function renderPrepends(): string
    {
        $rendered = '';

        foreach ($this->prependCallbacks as $callback) {
            $rendered .= call_user_func($callback, $this);
        }

        return $rendered;
    }

    public function getValueUsing(Closure $callback): static
    {
        $this->getValueUsingCallbacks[] = $callback;

        return $this;
    }

    public function getValue(): mixed
    {
        $value = $this->getOriginalValue();

        if (isset($this->getValueUsingCallbacks)) {
            foreach ($this->getValueUsingCallbacks as $callback) {
                $value = call_user_func($callback, $this, $value);
            }
        }

        return $value;
    }

    public function getOriginalValue(): mixed
    {
        return $this->getItem()->{$this->name};
    }

    public function renderCell(BaseModel|stdClass|array $item, TableAbstract $table): string
    {
        $item = $item instanceof BaseModel ? $item : (object) $item;

        $this->setTable($table)->setItem($item);

        $rendered = $this->rendering(fn () => $this->getValue());

        $rendered = $this->renderEmptyStateIfAvailable($rendered);

        $rendered = $this->renderPrepends() . $rendered . $this->renderAppends();

        return $this->applyColor($rendered);
    }
}
