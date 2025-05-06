<?php

namespace Botble\Table\Http\Controllers;

use Botble\Base\Facades\Form;
use Botble\Table\Http\Requests\BulkChangeRequest;
use Botble\Table\Http\Requests\SaveBulkChangeRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class TableBulkChangeController extends TableController
{
    public function index(BulkChangeRequest $request): array
    {
        $class = $request->input('class');

        if (! class_exists($class)) {
            return [];
        }

        $object = $this->tableBuilder->create($class);

        $data = $object->getValueInput(null, null, 'text');

        $key = $request->input('key');

        if (! $key) {
            return $data;
        }

        $column = Arr::get($object->getAllBulkChanges(), $key);
        if (empty($column)) {
            return $data;
        }

        if (isset($column['callback'])) {
            $callback = $column['callback'];

            if (is_callable($callback)) {
                $data = $object->getValueInput(
                    $column['title'],
                    null,
                    $column['type'],
                    $callback()
                );
            } elseif (method_exists($object, $callback)) {
                $data = $object->getValueInput(
                    $column['title'],
                    null,
                    $column['type'],
                    call_user_func([$object, $callback])
                );
            }
        } else {
            $data = $object->getValueInput($column['title'], null, $column['type'], Arr::get($column, 'choices', []));
        }

        if (! empty($column['title'])) {
            $labelClass = config('laravel-form-builder.label_class');
            if (Str::contains(Arr::get($column, 'validate'), 'required')) {
                $labelClass .= ' required';
            }

            $data['html'] = Form::label($column['title'], null, ['class' => $labelClass])->toHtml() . $data['html'];
        }

        return $data;
    }

    public function update(SaveBulkChangeRequest $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('core/table::table.please_select_record'));
        }

        $inputKey = $request->input('key');
        $inputValue = $request->input('value');

        $class = $request->input('class');

        if (! class_exists($class)) {
            return $this
                ->httpResponse()->setError();
        }

        $object = $this->tableBuilder->create($class);

        $columns = $object->getAllBulkChanges();

        if (! empty($columns[$inputKey]['validate'])) {
            $validator = Validator::make($request->input(), [
                'value' => $columns[$inputKey]['validate'],
            ]);

            if ($validator->fails()) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($validator->messages()->first());
            }
        }

        try {
            $object->saveBulkChanges($ids, $inputKey, $inputValue);
        } catch (Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }

        return $this
            ->httpResponse()
            ->setMessage(trans('core/table::table.save_bulk_change_success'));
    }
}
