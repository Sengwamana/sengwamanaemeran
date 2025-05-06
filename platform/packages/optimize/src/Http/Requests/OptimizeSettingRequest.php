<?php

namespace Botble\Optimize\Http\Requests;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class OptimizeSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'optimize_page_speed_enable' => $onOffRule = new OnOffRule(),
            'optimize_collapse_white_space' => $onOffRule,
            'optimize_elide_attributes' => $onOffRule,
            'optimize_inline_css' => $onOffRule,
            'optimize_insert_dns_prefetch' => $onOffRule,
            'optimize_remove_comments' => $onOffRule,
            'optimize_remove_quotes' => $onOffRule,
            'optimize_defer_javascript' => $onOffRule,
        ];
    }
}
