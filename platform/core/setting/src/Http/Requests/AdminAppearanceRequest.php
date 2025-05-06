<?php

namespace Botble\Setting\Http\Requests;

use Botble\Base\Facades\AdminAppearance;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Rules\GoogleFontsRule;
use Botble\Base\Supports\Language;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AdminAppearanceRequest extends Request
{
    public function rules(): array
    {
        return [
            'admin_logo' => ['nullable', 'string'],
            'admin_logo_max_height' => ['nullable', 'integer', 'min:10', 'max:300'],
            'admin_favicon' => ['nullable', 'string'],
            'login_screen_backgrounds' => ['nullable', 'array'],
            'login_screen_backgrounds*' => ['string', 'required'],
            'admin_title' => ['nullable', 'string', 'max:255'],
            'admin_appearance_locale' => ['sometimes', 'required', Rule::in(array_keys(Language::getAvailableLocales()))],
            'admin_appearance_locale_direction' => ['required', 'in:ltr,rtl'],
            'rich_editor' => ['required', Rule::in(array_keys(BaseHelper::availableRichEditors()))],
            'admin_appearance_layout' => ['required', 'string', Rule::in(array_keys(AdminAppearance::getLayouts()))],
            'admin_appearance_show_menu_item_icon' => ['nullable', 'bool'],
            'admin_appearance_container_width' => ['required', 'string', Rule::in(array_keys(AdminAppearance::getContainerWidths()))],
            'admin_primary_font' => new GoogleFontsRule(),
            'admin_primary_color' => ['nullable', 'string'],
            'admin_secondary_color' => ['nullable', 'string'],
            'admin_heading_color' => ['nullable', 'string'],
            'admin_text_color' => ['nullable', 'string'],
            'admin_link_color' => ['nullable', 'string'],
            'admin_link_hover_color' => ['nullable', 'string'],
            'admin_appearance_custom_css' => ['nullable', 'string', 'max:100000'],
            'admin_appearance_custom_header_js' => ['nullable', 'string', 'max:2500'],
            'admin_appearance_custom_body_js' => ['nullable', 'string', 'max:2500'],
            'admin_appearance_custom_footer_js' => ['nullable', 'string', 'max:2500'],
        ];
    }
}
