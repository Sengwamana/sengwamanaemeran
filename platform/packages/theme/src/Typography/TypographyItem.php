<?php

namespace Botble\Theme\Typography;

class TypographyItem
{
    protected string $name;

    protected string $label;

    protected string|float $default;

    protected array $fontWeights = [];

    protected bool $isGoogleFont = true;

    public function __construct(
        string $name,
        string $label,
        string|float $default,
        array $fontWeights = [],
        bool $isGoogleFont = true
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->default = $default;
        $this->fontWeights = $fontWeights;
        $this->isGoogleFont = $isGoogleFont;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDefault(): string|float
    {
        return $this->default;
    }

    public function getFontWeights(): array
    {
        return $this->fontWeights;
    }

    public function isGoogleFont(): bool
    {
        return $this->isGoogleFont;
    }
}
