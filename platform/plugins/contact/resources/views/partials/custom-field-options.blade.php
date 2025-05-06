<x-core::table>
    <x-core::table.header>
        <x-core::table.header.cell>
            {{ trans('plugins/contact::contact.custom_field.option.label') }}
        </x-core::table.header.cell>
        @if($isDefaultLanguage)
            <x-core::table.header.cell>
                {{ trans('plugins/contact::contact.custom_field.option.value') }}
            </x-core::table.header.cell>
            <x-core::table.header.cell />
        @endif
    </x-core::table.header>

    <x-core::table.body class="custom-field-options">
        @forelse($options as $key => $option)
            <x-core::table.body.row>
                <input type="hidden" name="options[{{ $key }}][id]" value="{{ $option->id }}" />
                <input type="hidden" name="options[{{ $key }}][order]" value="{{ $option->order !== 999 ? $option->order : $key }}" />

                <x-core::table.body.cell>
                    <input
                        type="text"
                        class="form-control"
                        name="options[{{ $key }}][label]"
                        value="{{ $option->label }}"
                        data-bb-toggle="option-label"
                    />
                </x-core::table.body.cell>
                @if ($isDefaultLanguage)
                    <x-core::table.body.cell>
                        <input
                                type="text"
                                class="form-control"
                                name="options[{{ $key }}][value]"
                                value="{{ $option->value }}"
                                data-bb-toggle="option-value"
                            />
                    </x-core::table.body.cell>
                    <x-core::table.body.cell style="width: 7%">
                        <x-core::button
                            type="button"
                            :icon-only="true"
                            icon="ti ti-trash"
                            data-bb-toggle="remove-option"
                        />
                    </x-core::table.body.cell>
                @endif
            </x-core::table.body.row>
        @empty
            <x-core::table.body.row>
                <x-core::table.body.cell>
                    <input
                        type="text"
                        class="form-control"
                        name="options[0][label]"
                        data-bb-toggle="option-label"
                    />
                </x-core::table.body.cell>
                <x-core::table.body.cell>
                    <input
                        type="text"
                        class="form-control"
                        name="options[0][value]"
                        data-bb-toggle="option-value"
                    />
                </x-core::table.body.cell>
                <x-core::table.body.cell style="width: 7%">
                    <x-core::button
                        type="button"
                        :icon-only="true"
                        icon="ti ti-trash"
                        data-bb-toggle="remove-option"
                    />
                </x-core::table.body.cell>
            </x-core::table.body.row>
        @endforelse
    </x-core::table.body>
</x-core::table>
