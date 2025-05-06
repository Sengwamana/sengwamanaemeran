<?php

namespace Botble\Theme\Http\Requests;

use Botble\Support\Http\Requests\Request;

class WebsiteTrackingSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'google_tag_manager_type' => ['nullable', 'in:id,code'],
            'google_tag_manager_id' => ['nullable', 'string', 'max:255'],
            'google_tag_manager_code' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
