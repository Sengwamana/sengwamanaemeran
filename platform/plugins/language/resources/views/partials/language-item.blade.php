<tr data-id="{{ $item->lang_id }}">
    <td>
        <a
            href="javascript:void(0);"
            class="gap-2 edit-language-button d-flex align-items-center text-decoration-none"
            data-id="{{ $item->lang_id }}"
            data-url="{{ route('languages.get', ['lang_id' => $item->lang_id]) }}"
            data-bs-original-title="{{ trans('plugins/language::language.edit_tooltip') }}"
            data-bs-toggle="tooltip"
        >
            {!! language_flag($item->lang_flag, $item->lang_name) !!}
            {{ $item->lang_name }}
        </a>
    </td>
    <td>{{ $item->lang_locale }}</td>
    <td>{{ $item->lang_code }}</td>
    <td>
        @if ($item->lang_is_default)
            <x-core::icon
                name="ti ti-star-filled"
                class="text-yellow"
                data-id="{{ $item->lang_id }}"
                data-name="{{ $item->lang_name }}"
            />
        @else
            <a
                href="javascript:void(0);"
                data-section="{{ route('languages.set.default') }}?lang_id={{ $item->lang_id }}"
                class="text-decoration-none set-language-default"
                data-bs-toggle="tooltip"
                data-bs-original-title="{{ trans('plugins/language::language.choose_default_language', ['language' => $item->lang_name]) }}"
            >
                <x-core::icon
                    name="ti ti-star-filled"
                    data-id="{{ $item->lang_id }}"
                    data-name="{{ $item->lang_name }}"
                />
            </a>
        @endif
    </td>
    <td>{{ $item->lang_order }}</td>
    <td>
        <x-core::button
            color="primary"
            icon="ti ti-edit"
            :icon-only="true"
            :data-id="$item->lang_id"
            :data-url="route('languages.get', ['lang_id' => $item->lang_id])"
            :tooltip="trans('plugins/language::language.edit_tooltip')"
            size="sm"
            class="edit-language-button"
        />

        <x-core::button
            color="danger"
            icon="ti ti-trash"
            :icon-only="true"
            :tooltip="trans('plugins/language::language.delete_tooltip')"
            size="sm"
            class="deleteDialog"
            data-section="{{ route('languages.destroy', $item->lang_id) }}"
        />
    </td>
</tr>
