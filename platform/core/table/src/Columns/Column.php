<?php

namespace Botble\Table\Columns;

use Botble\Table\Columns\Concerns\HasStyle;
use Illuminate\Support\Traits\Conditionable;
use Yajra\DataTables\Html\Column as BaseColumn;

class Column extends BaseColumn
{
    use Conditionable;
    use HasStyle;

    protected array $initialized = [];

    public static function make(array|string $data = [], string $name = ''): static
    {
        $instance = parent::make($data, $name);

        $instance->initialize();

        return $instance;
    }

    public function initialize(): void
    {
        foreach (class_uses_recursive(static::class) as $trait) {
            $method = 'initialize' . class_basename($trait);

            if (method_exists($this, $method) && ! in_array($method, $this->initialized)) {
                call_user_func([$this, $method]);
                $this->initialized[] = $method;
            }
        }
    }

    public function removeClass(string $class): static
    {
        if (isset($this->attributes['className'])) {
            $className = $this->attributes['className'];
            $this->attributes['className'] = trim(str_replace($class, '', $className));
        }

        return $this;
    }

    public function label(string $label): static
    {
        return $this->title($label);
    }

    public function columnVisibility(bool $has = false): static
    {
        if ($has) {
            return $this->removeClass('no-column-visibility');
        }

        return $this->addClass('no-column-visibility');
    }
}
