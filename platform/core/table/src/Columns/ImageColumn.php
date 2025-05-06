<?php

namespace Botble\Table\Columns;

use Botble\Base\Facades\Html;
use Botble\Media\Facades\RvMedia;
use Botble\Table\Contracts\FormattedColumn as FormattedColumnContract;

class ImageColumn extends FormattedColumn implements FormattedColumnContract
{
    protected bool $relative = false;

    protected int $width = 50;

    protected ?string $mediaSize = 'thumb';

    public static function make(array|string $data = [], string $name = ''): static
    {
        return parent::make($data ?: 'image', $name)
            ->title(trans('core/base::tables.image'))
            ->orderable(false)
            ->searchable(false)
            ->width(50);
    }

    public function relative(bool $flag = true): static
    {
        $this->relative = $flag;

        return $this;
    }

    public function with(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function mediaSize(?string $mediaSize): static
    {
        $this->mediaSize = $mediaSize;

        return $this;
    }

    public function fullMediaSize(): static
    {
        return $this->mediaSize(null);
    }

    public function formattedValue($value): string
    {
        $table = $this->getTable();

        if ($table->request()->has('action')) {
            if ($table->isExportingToCSV()) {
                return $this->getImageUrl($value, null);
            }

            if ($table->isExportingToExcel()) {
                return $this->getImageUrl($value);
            }
        }

        return Html::image(
            $this->getImageUrl($value, $this->mediaSize),
            trans('core/base::tables.image'),
            ['width' => $this->width]
        )->toHtml();
    }

    protected function getImageUrl(?string $value, ?string $mediaSize = 'thumb'): string
    {
        return (string) RvMedia::getImageUrl($value, $mediaSize, $this->relative, RvMedia::getDefaultImage());
    }
}
