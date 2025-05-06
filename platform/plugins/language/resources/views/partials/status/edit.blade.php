@if (!is_in_admin() || (Auth::guard()->check() && Auth::guard()->user()->hasPermission($route['edit'])))
    <a
        data-bs-toggle="tooltip"
        data-bs-original-title="{{ trans('plugins/language::language.edit_related') }}"
        href="{{ Route::has($route['edit']) ? route($route['edit'], $relatedLanguage) : '#' }}"
    >
        <x-core::icon name="ti ti-edit" />
    </a>
@else
    <x-core::icon name="ti ti-check" class="text-success" />
@endif
