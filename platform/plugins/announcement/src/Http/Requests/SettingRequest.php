<?php

namespace ArchiElite\Announcement\Http\Requests;

use ArchiElite\Announcement\Enums\AnnouncePlacement;
use ArchiElite\Announcement\Enums\FontSizeUnit;
use ArchiElite\Announcement\Enums\TextAlignment;
use ArchiElite\Announcement\Enums\WidthUnit;
use ArchiElite\Announcement\Rules\HexColor;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'announcement_placement' => ['required', 'string', Rule::in(AnnouncePlacement::values())],
            'announcement_max_width' => ['nullable', 'numeric', 'min:0'],
            'announcement_max_width_unit' => ['required', 'string', Rule::in(WidthUnit::values())],
            'announcement_text_alignment' => ['required', 'string', Rule::in(TextAlignment::values())],
            'announcement_text_color' => ['required', 'string', new HexColor()],
            'announcement_background_color' => ['required', 'string', new HexColor()],
            'announcement_font_size' => ['nullable', 'numeric', 'min:0'],
            'announcement_font_size_unit' => ['required', 'string', Rule::in(FontSizeUnit::values())],
            'announcement_dismissible' => ['required', 'boolean'],
        ];
    }
}
