<?php

namespace Botble\Base\Models\Concerns;

use Illuminate\Support\Str;

trait HasSlug
{
    public static function createSlug(?string $name, int|string|null $id, string $fromColumn = 'slug'): string
    {
        $language = apply_filters('core_slug_language', 'en');

        $slug = Str::slug($name, '-', $language);
        $index = 1;
        $baseSlug = $slug;

        while (
            self::query()
                ->where($fromColumn, $slug)
                ->when($id, fn ($query) => $query->whereNot('id', $id))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $index++;
        }

        if (empty($slug)) {
            $slug = time();
        }

        return $slug;
    }
}
