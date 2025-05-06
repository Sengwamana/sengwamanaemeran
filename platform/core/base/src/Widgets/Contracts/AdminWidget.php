<?php

namespace Botble\Base\Widgets\Contracts;

use Illuminate\Contracts\View\View;

interface AdminWidget
{
    public function register(array $widgets, ?string $namespace): static;

    public function remove(string $id, ?string $namespace): static;

    public function getColumns(?string $namespace): int;

    public function render(string $namespace): View;
}
