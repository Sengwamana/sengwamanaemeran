<?php

namespace Botble\Table\Abstracts;

use Illuminate\Contracts\Support\Arrayable;

abstract class TableBulkChangeAbstract implements Arrayable
{
    protected string $name = '';

    protected string $title;

    protected string $type = 'text';

    protected array|string|null $validate = null;

    protected bool $required = false;

    public static function make(array $data = []): static
    {
        return app(static::class, $data);
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function validate(array|string $validate): static
    {
        if (is_array($validate)) {
            $validate = implode('|', $validate);
        }

        $this->validate = $validate;

        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        $data = [
            'title' => $this->title,
            'type' => $this->type,
            'validate' => $this->validate,
        ];

        if ($this->required) {
            $validate = explode('|', $data['validate'] ?? '');

            $validate = [
                'required',
                ...$validate,
            ];

            $data['validate'] = implode('|', $validate);
        }

        return $data;
    }
}
