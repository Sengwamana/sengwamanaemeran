<?php

namespace Botble\Shortcode;

use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ShortcodeField
{
    public function tabs(array $fields, array $attributes = [], int $max = 20, int $min = 1, ?string $tabKey = null, $wrapperAttributes = []): string
    {
        if (! $fields) {
            return '';
        }

        $current = (int) Arr::get($attributes, $tabKey ? "{$tabKey}_quantity" : 'quantity') ?: 6;

        $selector = 'quantity_' . Str::random(20);

        $choices = range($min, $max);
        $choices = array_combine($choices, $choices);

        return view(
            'packages/shortcode::fields.tabs',
            compact('fields', 'attributes', 'current', 'selector', 'choices', 'max', 'min', 'tabKey', 'wrapperAttributes')
        )->render();
    }

    public function getTabsData(array $fields, ShortcodeCompiler $shortcode, ?string $key = null): array
    {
        $quantity = min((int) $shortcode->{$key ? "{$key}_quantity" : 'quantity'}, 20);

        if (empty($shortcode->toArray()) || empty($fields) || ! $quantity) {
            return [];
        }

        $tabs = [];

        for ($i = 1; $i <= $quantity; $i++) {
            $tab = [];
            foreach ($fields as $field) {
                $tab[$field] = $key ? $shortcode->{"{$key}_{$field}_{$i}"} : $shortcode->{"{$field}_{$i}"};
            }

            if (! empty(array_filter($tab, fn ($field) => $field !== null))) {
                $tabs[] = $tab;
            }
        }

        return $tabs;
    }

    public function ids(string $field, array $attributes = [], array $options = []): string
    {
        $value = Arr::get($attributes, $field);

        $value = static::parseIds($value);

        $multiple = true;

        return view('packages/shortcode::fields.select', compact(
            'field',
            'value',
            'options',
            'multiple'
        ))->render();
    }

    public function getIds(string $field, ShortcodeCompiler $shortcode): array
    {
        $value = $shortcode->{$field};

        if (empty($value) || ! is_string($value)) {
            return [];
        }

        return static::parseIds($value);
    }

    public static function parseIds(?string $value): array
    {
        if (empty($value)) {
            return [];
        }

        return explode(',', $value) ?: [];
    }

    public function lazyLoading(array $attributes): string
    {
        return view('packages/shortcode::fields.lazy-loading', compact('attributes'))->render();
    }
}
