@if (!is_in_admin() || (Auth::guard()->check() && Auth::guard()->user()->hasPermission($route['create'])))
    <a
        data-bs-toggle="tooltip"
        data-bs-original-title="{{ trans('plugins/language::language.add_language_for_item') }}"
        href="{{ route($route['create']) }}?ref_from={{ $item->id }}&ref_lang={{ $language->lang_code }}"
    ><x-core::icon name="ti ti-plus" /></a>
@else
    <x-core::icon
        name="ti ti-plus"
        class="text-primary"
    />
@endif
