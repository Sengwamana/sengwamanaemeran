<?php

namespace ArchiElite\Announcement\Http\Requests;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class AnnouncementRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'content' => ['required', 'string'],
            'has_action' => [new OnOffRule()],
            'action_label' => ['required_if:has_action,1', 'nullable', 'string', 'max:255'],
            'action_url' => ['required_if:has_action,1', 'nullable', 'string', 'max:400'],
            'action_open_new_tab' => ['required_if:has_action,1', 'boolean'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'is_active' => [new OnOffRule()],
        ];
    }
}
