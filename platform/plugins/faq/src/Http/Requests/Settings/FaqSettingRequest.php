<?php

namespace Botble\Faq\Http\Requests\Settings;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class FaqSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'enable_faq_schema' => new OnOffRule(),
        ];
    }
}
