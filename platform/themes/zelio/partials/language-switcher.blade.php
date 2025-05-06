@if (is_plugin_active('language'))
    @php
        $supportedLocales = Language::getSupportedLocales();

        if (empty($options)) {
            $options = [
                'before' => '',
                'lang_flag' => true,
                'lang_name' => true,
                'class' => '',
                'after' => '',
            ];
        }
    @endphp

    @if ($supportedLocales && count($supportedLocales) > 1)
        @php
            $languageDisplay = setting('language_display', 'all');
            $showRelated = setting('language_show_default_item_if_current_version_not_existed', true);
        @endphp

        <div class="dropdown language-dropdown">
            <button class="btn btn-secondary dropdown-toggle language-dropdown-button" type="button" id="language-dropdown-button-{{ $buttonId = time() }}" data-bs-toggle="dropdown" aria-expanded="false">
                @if (Arr::get($options, 'lang_flag', true) && ($languageDisplay == 'all' || $languageDisplay == 'flag'))
                    {!! language_flag(Language::getCurrentLocaleFlag()) !!}
                @endif
                @if (Arr::get($options, 'lang_name', true) && ($languageDisplay == 'all' || $languageDisplay == 'name'))
                    {{ Language::getCurrentLocaleName() }}
                @endif
            </button>
            <ul class="dropdown-menu" aria-labelledby="language-dropdown-button-{{ $buttonId }}">
                @foreach ($supportedLocales as $localeCode => $properties)
                    @if ($localeCode != Language::getCurrentLocale())
                        <li>
                            <a class="dropdown-item language-switcher-item" href="{{ $showRelated ? Language::getLocalizedURL($localeCode) : url($localeCode) }}">
                                @if (Arr::get($options, 'lang_flag', true) && ($languageDisplay == 'all' || $languageDisplay == 'flag'))
                                    {!! language_flag($properties['lang_flag']) !!} <span class="ms-2">{{ $properties['lang_name'] }}</span>
                                @endif
                                @if (Arr::get($options, 'lang_name', true) &&  ($languageDisplay == 'name'))
                                    &nbsp;{{ $properties['lang_name'] }}
                                @endif
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif
@endif
