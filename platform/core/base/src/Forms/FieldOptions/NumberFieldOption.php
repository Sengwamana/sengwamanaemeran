<?php

namespace Botble\Base\Forms\FieldOptions;

class NumberFieldOption extends InputFieldOption
{
    public function min(int|float $number): static
    {
        $this->addAttribute('min', $number);

        return $this;
    }

    public function max(int|float $number): static
    {
        $this->addAttribute('max', $number);

        return $this;
    }

    public function step(int|float $number): static
    {
        $this->addAttribute('step', $number);

        return $this;
    }
}
