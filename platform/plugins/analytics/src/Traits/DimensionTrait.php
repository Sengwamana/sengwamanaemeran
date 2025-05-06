<?php

namespace Botble\Analytics\Traits;

use Google\Analytics\Data\V1beta\Dimension;

trait DimensionTrait
{
    public array $dimensions = [];

    public function dimension(string $name): self
    {
        $this->dimensions[] = (new Dimension())
            ->setName($name);

        return $this;
    }

    public function dimensions(string|array $items): self
    {
        $this->dimensions = [];

        foreach ((array) $items as $item) {
            $this->dimension($item);
        }

        return $this;
    }
}
