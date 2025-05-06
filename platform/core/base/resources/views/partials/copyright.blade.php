{!! BaseHelper::clean(
    trans('core/base::layouts.copyright', [
        'year' => Carbon\Carbon::now()->year,
        'company' => setting('admin_title', config('core.base.general.base_name')),
        'version' => sprintf('<span class="fw-medium">%s</span>', get_cms_version()),
    ]),
) !!}
