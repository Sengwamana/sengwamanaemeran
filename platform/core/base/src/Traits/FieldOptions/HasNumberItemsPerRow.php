<?php

namespace Botble\Base\Traits\FieldOptions;

trait HasNumberItemsPerRow
{
    protected int $numberItemsPerRow = 3;

    public function numberItemsPerRow(int $number): static
    {
        $this->numberItemsPerRow = $number;

        return $this;
    }
}
