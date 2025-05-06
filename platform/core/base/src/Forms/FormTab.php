<?php

namespace Botble\Base\Forms;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class FormTab
{
    protected string $id;

    protected string $label;

    protected string $content;

    protected int $priority = 0;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return $this->id ?? Str::slug($this->getLabel()) . '-' . 'tab';
    }

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function content(string|Htmlable $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function priority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }
}
