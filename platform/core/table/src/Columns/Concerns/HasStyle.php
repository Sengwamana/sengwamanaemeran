<?php

namespace Botble\Table\Columns\Concerns;

trait HasStyle
{
    public function alignLeft(): static
    {
        return $this->alignStart();
    }

    public function alignStart(): static
    {
        return $this
            ->removeClass('text-center')
            ->removeClass('text-end')
            ->addClass('text-start');
    }

    public function alignCenter(): static
    {
        return $this
            ->removeClass('text-start')
            ->removeClass('text-end')
            ->addClass('text-center');
    }

    public function alignEnd(): static
    {
        return $this
            ->removeClass('text-start')
            ->removeClass('text-center')
            ->addClass('text-end');
    }

    public function nowrap(): static
    {
        return $this->addClass('text-nowrap');
    }

    public function fontBold(): static
    {
        return $this->addClass('fw-bold');
    }

    public function fontBolder(): static
    {
        return $this->addClass('fw-bolder');
    }

    public function fontSemibold(): static
    {
        return $this->addClass('fw-semibold');
    }

    public function fontLight(): static
    {
        return $this->addClass('fw-light');
    }

    public function fontLighter(): static
    {
        return $this->addClass('fw-lighter');
    }

    public function fontMono(): static
    {
        return $this->addClass('font-monospace');
    }

    public function underline(): static
    {
        return $this->addClass('text-decoration-underline');
    }

    public function lineThrough(): static
    {
        return $this->addClass('text-decoration-line-through');
    }
}
