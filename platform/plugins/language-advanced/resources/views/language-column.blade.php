<div class="text-center language-column">
    @foreach ($languages as $language)
        @if (!is_in_admin() || (Auth::guard()->check() && Auth::guard()->user()->hasPermission($route['edit'])))
            @if ($language->lang_code == Language::getDefaultLocaleCode())
                <a href="{{ Route::has($route['edit']) ? route($route['edit'], $item->id) : '#' }}">
                    <x-core::icon name="ti ti-check" class="text-success" />
                </a>
            @else
                <a
                    data-bs-toggle="tooltip"
                    href="{{ Route::has($route['edit']) ? route($route['edit'], $item->id) . '?ref_lang=' . $language->lang_code : '#' }}"
                    title="{{ trans('plugins/language::language.edit_related') }}"
                >
                    <x-core::icon name="ti ti-edit" />
                </a>
            @endif
        @else
            <x-core::icon name="ti ti-check" class="text-success" />
        @endif
    @endforeach
</div>
