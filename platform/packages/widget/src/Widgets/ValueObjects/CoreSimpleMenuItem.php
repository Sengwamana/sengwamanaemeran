<?php

namespace Botble\Widget\Widgets\ValueObjects;

class CoreSimpleMenuItem
{
    protected array $items = [];

    public function __construct(array $data)
    {
        foreach ($data as $item) {
            if (! isset($item['key']) || ! isset($item['value'])) {
                continue;
            }

            $this->items[$item['key']] = $item['value'];
        }
    }

    public function __get(string $name): mixed
    {
        return $this->items[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->items[$name] = $value;
    }

    public function toArray(): array
    {
        return collect($this->items)->map(function ($item, $key) {
            return [
                'key' => $key,
                'value' => $item,
            ];
        })->toArray();
    }
}
