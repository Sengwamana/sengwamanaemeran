<?php

namespace Botble\Table\Columns;

use BackedEnum;
use Botble\Base\Supports\Enum;
use Botble\Table\Contracts\FormattedColumn as FormattedColumnContract;
use Throwable;

class EnumColumn extends FormattedColumn implements FormattedColumnContract
{
    public static function make(array|string $data = [], string $name = ''): static
    {
        return parent::make($data, $name)
            ->alignCenter()
            ->width(100)
            ->withEmptyState()
            ->renderUsing(function (EnumColumn $column, $value) {
                try {
                    return $column->formattedValue($value);
                } catch (Throwable) {
                    return $value;
                }
            });
    }

    public function formattedValue($value): ?string
    {
        if (! $value instanceof Enum && ! $value instanceof BackedEnum) {
            return '';
        }

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        $table = $this->getTable();

        if ($table->isExportingToExcel() || $table->isExportingToCSV()) {
            return $value->getValue();
        }

        return $value->toHtml() ?: $value->getValue();
    }
}
