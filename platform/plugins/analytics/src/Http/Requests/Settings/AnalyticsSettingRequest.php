<?php

namespace Botble\Analytics\Http\Requests\Settings;

use Botble\Analytics\Rules\AnalyticsCredentialRule;
use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class AnalyticsSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'google_analytics' => ['nullable', 'string', 'starts_with:G-'],
            'analytics_property_id' => ['nullable', 'string', 'size:9'],
            'analytics_service_account_credentials' => ['nullable', new AnalyticsCredentialRule()],
            'analytics_dashboard_widgets' => new OnOffRule(),
        ];
    }
}
