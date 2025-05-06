<?php

namespace Botble\Table;

use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class TableBuilder
{
    public function __construct(protected Container $container)
    {
    }

    public function create(string $tableClass): TableAbstract
    {
        if (! class_exists($tableClass)) {
            throw new InvalidArgumentException(sprintf('Table class with name %s does not exist.', $tableClass));
        }

        return $this->container->make($tableClass);
    }
}
