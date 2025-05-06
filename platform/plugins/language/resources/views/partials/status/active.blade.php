@if (!is_in_admin() || (Auth::guard()->check() && Auth::guard()->user()->hasPermission($route['edit'])))
    <a
        data-bs-toggle="tooltip"
        data-bs-original-title="{{ trans('plugins/language::language.current_language') }}"
        href="{{ Route::has($route['edit']) ? route($route['edit'], $item->id) : '#' }}"
    ><x-core::icon name="ti ti-check" class="text-success" /></a>
@else
    <x-core::icon name="ti ti-check" class="text-success" />
@endif
