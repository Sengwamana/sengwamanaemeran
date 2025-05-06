<?php

namespace ArchiElite\Announcement\Http\Requests\Settings;

use ArchiElite\Announcement\Enums\AnnouncePlacement;
use ArchiElite\Announcement\Enums\FontSizeUnit;
use ArchiElite\Announcement\Enums\TextAlignment;
use ArchiElite\Announcement\Enums\WidthUnit;
use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AnnouncementSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'announcement_placement' => ['required', 'string', Rule::in(AnnouncePlacement::values())],
            'announcement_max_width' => ['nullable', 'numeric', 'min:0'],
            'announcement_max_width_unit' => ['required', 'string', Rule::in(WidthUnit::values())],
            'announcement_text_alignment' => ['required', 'string', Rule::in(TextAlignment::values())],
            'announcement_text_color' => ['required', 'string'],
            'announcement_background_color' => ['required', 'string'],
            'announcement_font_size' => ['nullable', 'numeric', 'min:0'],
            'announcement_font_size_unit' => ['required', 'string', Rule::in(FontSizeUnit::values())],
            'announcement_dismissible' => [new OnOffRule()],
            'announcement_autoplay' => [new OnOffRule()],
            'announcement_autoplay_delay' => ['nullable', 'numeric', 'min:0'],
            'announcement_lazy_loading' => [new OnOffRule()],
            'announcement_hide_on_mobile' => [new OnOffRule()],
        ];
    }
}
