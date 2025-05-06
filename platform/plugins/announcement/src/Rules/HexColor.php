<?php

namespace ArchiElite\Announcement\Rules;

use Illuminate\Contracts\Validation\Rule;

class HexColor implements Rule
{
    public function passes($attribute, $value): bool
    {
        $pattern = '/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{8})$/';

        return preg_match($pattern, $value);
    }

    public function message(): string
    {
        return trans('plugins/announcement::announcements.validation.text_color');
    }
}
