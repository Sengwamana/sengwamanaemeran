<?php

namespace Botble\Analytics\Traits;

use Google\Analytics\Data\V1beta\Metric;

trait MetricTrait
{
    public array $metrics = [];

    public function metric(string $name): self
    {
        $this->metrics[] = (new Metric())
            ->setName($name);

        return $this;
    }

    public function metrics(string|array $items): self
    {
        $this->metrics = [];

        foreach ((array) $items as $item) {
            $item = trim($item);
            $this->metric($item);
        }

        return $this;
    }
}
