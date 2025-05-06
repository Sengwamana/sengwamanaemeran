@if (!empty($locales))
    <div class="table-responsive">
        <table class="table text-start table-striped table-bordered">
            <tbody>
                @foreach ($locales as $locale)
                    <tr>
                        <td>{{ $locale['name'] }} - {{ $locale['locale'] }}</td>
                        <td
                            class="text-center"
                            style="white-space: nowrap; width: 1%;"
                        >
                            <x-core::button
                                type="button"
                                color="primary"
                                class="btn-import-remote-locale"
                                data-url="{{ route('translations.locales.download-remote-locale', $locale['locale']) }}"
                                icon="ti ti-download"
                            >
                                {{ trans('plugins/translation::translation.download_locale') }}
                            </x-core::button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <span class="d-inline-block">{{ trans('core/base::tables.no_data') }}</span>
@endif
