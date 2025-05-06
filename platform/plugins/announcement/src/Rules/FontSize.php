<?php

namespace ArchiElite\Announcement\Rules;

use Illuminate\Contracts\Validation\Rule;

class FontSize implements Rule
{
    public function passes($attribute, $value): bool
    {
        $pattern = '/^\d+(\.\d+)?(px|rem|em)?$/';

        return preg_match($pattern, $value);
    }

    public function message(): string
    {
        return trans('plugins/announcement::announcements.validation.font_size');
    }
}
