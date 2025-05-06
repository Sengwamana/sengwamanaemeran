<?php

namespace Botble\Table\Http\Controllers;

use Botble\Table\Http\Requests\FilterRequest;
use Illuminate\Support\Arr;

class TableFilterController extends TableController
{
    public function __invoke(FilterRequest $request)
    {
        $class = $request->input('class');

        if (! class_exists($class)) {
            return [];
        }

        $key = $request->input('key');

        $table = $this->tableBuilder->create($class);

        $data = $table->getValueInput(null, null, 'text');

        if (! $key) {
            return $data;
        }

        $column = Arr::get($table->getFilters(), $key);
        if (empty($column)) {
            return $data;
        }

        $value = $request->input('value');

        $choices = Arr::get($column, 'choices', []);

        if (isset($column['callback'])) {
            $callback = $column['callback'];

            if (is_callable($callback)) {
                $choices = $callback($value);
            } elseif (method_exists($table, $callback)) {
                $choices = call_user_func_array([$table, $callback], [$value]);
            }
        }

        return $table->getValueInput(
            null,
            $value,
            $column['type'],
            $choices
        );
    }
}
