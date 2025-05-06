<?php

namespace Botble\Table\Columns;

use Botble\Base\Contracts\BaseModel;
use Botble\Base\Facades\Form;
use Botble\Table\Contracts\FormattedColumn as FormattedColumnContract;

class CheckboxColumn extends FormattedColumn implements FormattedColumnContract
{
    public static function make(array|string $data = [], string $name = ''): static
    {
        return parent::make($data ?: 'checkbox', $name)
            ->content('')
            ->title(
                Form::input('checkbox', '', null, [
                    'class' => 'form-check-input m-0 align-middle table-check-all',
                    'data-set' => '.dataTable .checkboxes',
                ])->toHtml()
            )
            ->className('w-1')
            ->alignStart()
            ->orderable(false)
            ->exportable(false)
            ->searchable(false)
            ->columnVisibility()
            ->titleAttr(trans('core/base::tables.checkbox'));
    }

    public function formattedValue($value): string
    {
        $item = $this->getItem();

        return view('core/table::partials.checkbox', [
            'id' => $item instanceof BaseModel ? $item->getKey() : null,
        ])->render();
    }
}
