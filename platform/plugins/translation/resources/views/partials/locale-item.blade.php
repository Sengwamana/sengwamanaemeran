<tr data-locale="{{ $item['locale'] }}">
    <td class="text-start">
        <span>{{ $item['name'] }}</span>
    </td>
    <td>{{ $item['locale'] }}</td>
    <td>{{ $item['locale'] == app()->getLocale() ? trans('core/base::base.yes') : trans('core/base::base.no') }}</td>
    <td>
        <div class="btn-list justify-content-end">
            <x-core::button
                tag="a"
                :href="route('translations.locales.download', $item['locale'])"
                class="download-locale-button"
                :tooltip="trans('plugins/translation::translation.download')"
                icon="ti ti-download"
                :icon-only="true"
                color="primary"
                size="sm"
            />
            @if ($item['locale'] !== 'en')
                <x-core::button
                    type="button"
                    :data-url="route('translations.locales.delete', $item['locale'])"
                    class="delete-locale-button"
                    :tooltip="trans('plugins/translation::translation.delete')"
                    icon="ti ti-trash"
                    :icon-only="true"
                    color="danger"
                    size="sm"
                />
            @endif
        </div>
    </td>
</tr>
