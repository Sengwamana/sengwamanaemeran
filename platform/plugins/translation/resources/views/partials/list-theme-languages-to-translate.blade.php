@if (count($groups) > 1)
    <div class="mb-3 text-end d-flex gap-2 justify-content-start justify-content-lg-end align-items-center">
        <h4 class="mb-0">{{ trans('plugins/translation::translation.translations') }}:</h4>
        @if (count($groups) <= 3)
            <div class="d-flex gap-3 align-items-center">
                @foreach ($groups as $language)
                    @continue($language['locale'] === $group['locale'])
                    <a
                        href="{{ route($route, $language['locale'] == app()->getLocale() ? [] : ['ref_lang' => $language['locale']]) }}"
                        class="text-decoration-none small"
                    >
                        {!! language_flag($language['flag'], $language['name']) !!}
                        {{ $language['name'] }}
                    </a>
                @endforeach
            </div>
        @else
            <x-core::dropdown>
                <x-slot:trigger>
                    <a
                        class="d-flex align-items-center gap-2 dropdown-toggle text-muted text-decoration-none"
                        href="#"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        {!! language_flag($group['flag'], $group['name']) !!}
                        {{ $group['name'] }}
                    </a>
                </x-slot:trigger>

                @foreach ($groups as $language)
                    @continue($language['locale'] === $group['locale'])

                    <x-core::dropdown.item
                        href="{{ route($route, $language['locale'] == app()->getLocale() ? [] : ['ref_lang' => $language['locale']]) }}"
                        class="d-flex gap-2 align-items-center"
                    >
                        @if ($language['flag'])
                            {!! language_flag($language['flag'], $language['name']) !!}
                        @endif
                        {{ $language['name'] }}
                    </x-core::dropdown.item>
                @endforeach
            </x-core::dropdown>
        @endif

        <input
            name="ref_lang"
            type="hidden"
            value="{{ BaseHelper::stringify(request()->input('ref_lang')) }}"
        >
    </div>
@endif
