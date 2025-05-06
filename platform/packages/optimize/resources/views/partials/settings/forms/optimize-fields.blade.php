<x-core::form.on-off.checkbox
    name="optimize_page_speed_enable"
    :label="trans('packages/optimize::optimize.settings.enable')"
    :checked="setting('optimize_page_speed_enable', false)"
    data-bb-toggle="collapse"
    data-bb-target=".optimize-settings"
    :wrapper="false"
/>

<x-core::form.fieldset
    data-bb-value="1"
    class="optimize-settings mt-3"
    @style(['display: none;' => ! setting('optimize_page_speed_enable', false)])
>
    <x-core::form.on-off.checkbox
        name="optimize_collapse_white_space"
        :label="trans('packages/optimize::optimize.collapse_white_space')"
        :checked="setting('optimize_collapse_white_space', false)"
        :helper-text="trans('packages/optimize::optimize.collapse_white_space_description')"
    />

    <x-core::form.on-off.checkbox
        name="optimize_elide_attributes"
        :label="trans('packages/optimize::optimize.elide_attributes')"
        :checked="setting('optimize_elide_attributes', false)"
        :helper-text="trans('packages/optimize::optimize.elide_attributes_description')"
    />

    <x-core::form.on-off.checkbox
        name="optimize_inline_css"
        :label="trans('packages/optimize::optimize.inline_css')"
        :checked="setting('optimize_inline_css', false)"
        :helper-text="trans('packages/optimize::optimize.inline_css_description')"
    />

    <x-core::form.on-off.checkbox
        name="optimize_insert_dns_prefetch"
        :label="trans('packages/optimize::optimize.insert_dns_prefetch')"
        :checked="setting('optimize_insert_dns_prefetch', false)"
        :helper-text="trans('packages/optimize::optimize.insert_dns_prefetch_description')"
    />

    <x-core::form.on-off.checkbox
        name="optimize_remove_comments"
        :label="trans('packages/optimize::optimize.remove_comments')"
        :checked="setting('optimize_remove_comments', false)"
        :helper-text="trans('packages/optimize::optimize.remove_comments_description')"
    />

    <x-core::form.on-off.checkbox
        name="optimize_remove_quotes"
        :label="trans('packages/optimize::optimize.remove_quotes')"
        :checked="setting('optimize_remove_quotes', false)"
        :helper-text="trans('packages/optimize::optimize.remove_quotes_description')"
    />

    <x-core::form.on-off.checkbox
        name="optimize_defer_javascript"
        :label="trans('packages/optimize::optimize.defer_javascript')"
        :checked="setting('optimize_defer_javascript', false)"
        :helper-text="trans('packages/optimize::optimize.defer_javascript_description')"
    />
</x-core::form.fieldset>
