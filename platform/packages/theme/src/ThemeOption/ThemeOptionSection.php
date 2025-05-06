<?php

namespace Botble\Theme\ThemeOption;

use Illuminate\Contracts\Support\Arrayable;

class ThemeOptionSection implements Arrayable
{
    protected string $id;

    protected string $title;

    protected ?string $description = null;

    protected string $icon;

    protected float $priority = 999;

    protected array $fields = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function make(string $id): self
    {
        return new self($id);
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function priority(float $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function fields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'priority' => $this->priority,
            'fields' => $this->fields,
        ];
    }
}
